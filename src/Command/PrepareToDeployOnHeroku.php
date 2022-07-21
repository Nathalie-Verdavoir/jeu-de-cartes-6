<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

// the "name" and "description" arguments of AsCommand replace the
// static $defaultName and $defaultDescription properties
#[AsCommand(
    name: 'nat:heroku',
    description: 'Prepare To Deploy On Heroku.',
    hidden: false,
    aliases: ['nat:h']
)]
class PrepareToDeployOnHeroku extends Command
{
    private $io;
    private $filesystem;
    private $input;
    private $output;
    private $databaseUrl;
    private $appSecret;
    private $clearDB;

    //need form
    private $herokuUser;
    private $herokuApiKey;
    private $herokuAppName;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($this->input, $this->output);
        $this->filesystem = new Filesystem();
        
        $this->herokuApiKey = $this->io->ask('What is your ApiKey in Heroku Account?', '', function ($apiKey) {
            if (empty($apiKey)) {
                throw new \RuntimeException('Password cannot be empty.');
            }
            return $apiKey;
        });

        $this->herokuUser = $this->io->ask('What is your Username to log in Heroku Account? (your.email@exemple.com)', '',function ($username) {
            if (empty($username)) {
                throw new \RuntimeException('Username (email) cannot be empty.');
            }
            return $username;
        });

        $this->herokuAppName = $this->io->ask('What is the name of your app to log in Heroku Account? (app-exemple-name)', '',function ($appName) {
            if (empty($appName)) {
                throw new \RuntimeException('AppName cannot be empty.');
            }
            return $appName;
        });


        $this->getEnvVars();
  

        $this->io->progressStart(100);

        //$this->CreateProcfile();

        $this->io->progressAdvance(2);

        //$this->CreateHtaccess();
      
        $this->io->progressAdvance(2);

        //$this->CreateEnvPhp();

        $this->io->progressAdvance(2);

        //$this->checkForHerokuLogin();

        if( $this->databaseUrl ){
            //$this->setClearDbAddon();
        }

        if( $this->appSecret ){
            //$this->setAppSecret();
        }
        $this->io->progressFinish(100);

        // outputs a message followed by a "\n"
        $this->output->writeln('Whoa!');
    
        return Command::SUCCESS;
    }

    private function getEnvVars()
    {
        $processes = [
            ['composer', 'require', 'symfony/dotenv'],
            ['composer', 'up']
        ];
        $this->runProcesses($processes);
        foreach (explode(',', $_SERVER['SYMFONY_DOTENV_VARS']) as $parm) {
            // $this->output->writeln([$parm. '='.$_SERVER[$parm]]);
            if($parm=='DATABASE_URL') $this->databaseUrl =  $_SERVER[$parm];
            if($parm=='APP_SECRET') $this->appSecret = $_SERVER[$parm];
        } 
    }

    private function CreateProcfile()
    {
        $this->getColoredMessage('Creating Procfile', 'blue'); 
        $processes = [
            ['composer', 'require', 'symfony/filesystem'],
            ['composer', 'up']
        ];
        $this->runProcesses($processes);
        $this->filesystem->dumpFile('Procfile', 'web: heroku-php-apache2 public/');
        $this->getColoredMessage('Procfile done!', 'green');
    }

    private function CreateHtaccess()
    {
        $this->getColoredMessage('Creating .htaccess', 'blue');
        $processes = [
            ['composer', 'remove', 'symfony/apache-pack'],
            ['composer', 'config', 'extra.symfony.allow-contrib', 'true'],
            ['composer', 'up'],
            ['composer', 'require', 'symfony/apache-pack'],
            ['composer', 'up'],
            ['composer', 'config', 'extra.symfony.allow-contrib', ''],
            ['composer', 'up'],
        ];
        $this->runProcesses($processes);
        $this->getColoredMessage('.htaccess done!', 'green');
    }

    private function CreateEnvPhp()
    {
        $this->getColoredMessage('Creating .env.php file', 'blue');
        $processes = [
            ['composer', 'require', 'symfony/dotenv'],
            ['php', 'bin/console', 'cache:pool:clear', 'cache.global_clearer'],
            ['composer', 'up'],
            ['composer', 'dump-env', 'prod'],
        ];
        
        $this->runProcesses($processes);
        $this->filesystem->copy('.env.local.php', '.env.php');
        $this->filesystem->dumpFile('.env.php', "<?php

        return array (
          'APP_ENV' => 'prod',
        );
        ");
        $this->getColoredMessage('.env.php done!', 'green');
    }

    private function getColoredMessage(string|CommandError $message, string $color)
    {
        $separator = str_repeat('=',36-strlen($message));
        $this->output->writeln([
            '',
            '<bg='. $color .'>  ========================================  ',
            '  ====' . $message . $separator . '  ',
            '  ==================================<bg=bright-magenta>by Nat</>  </>',
            '',
        ]);
    }

    private function runProcesses($processes)
    {
        foreach($processes as $proc){
            $process = new Process($proc);
            try {
                $process->mustRun();
                $this->getProcessMessages($proc);
                
                //FOR CLEARDB_DATABASE_URL ONLY 
                if($proc[1]==='config:get' && $proc==='CLEARDB_DATABASE_URL') {
                    $this->clearDB= $process->getOutput();
                }

                //FOR HEROKU LOGIN ONLY 
                if($proc[1]=="authorizations:create") {
                    $process->setInput($this->herokuUser,$this->herokuApiKey);
                }

                echo $process->getOutput();
            } catch (ProcessFailedException $exception) {
                echo $exception->getMessage();

                //FOR HEROKU AUTH FAILED ONLY
                if($proc[1]=="authorizations:create") {
                    $this->getColoredMessage(CommandError::HEROKU_LOG_FAILED->value, 'red');
                }
            } 
        }
    }
    private function getProcessMessages(array $proc)
    {
        $this->output->writeln(
            [ '<info>' . implode(' ',$proc) . '</>',
            '']
        );
        $this->io->progressAdvance(2);
        $this->output->writeln(['']);
    }

    private function checkForHerokuLogin()
    {
        $processes = [
            ['heroku', 'authorizations:create'],
            ['heroku', 'auth:whoami'],
        ];
        $this->runProcesses($processes);
    }

    private function setClearDbAddon()
    {
        $processes = [
            ['heroku', 'addons:create', 'cleardb:ignite', '--app='.$this->herokuAppName],
            ['heroku', 'config|grep', 'CLEARDB_DATABASE_URL'],
            ['heroku', 'config:get', 'CLEARDB_DATABASE_URL', '--app='.$this->herokuAppName, '>>', '.env.local.heroku'],
        ];
        $this->runProcesses($processes);

        $databasevar = 'DATABASE_URL='.$this->clean($this->clearDB);

        $processes = [
            ['heroku', 'config:set', $databasevar, '--app='.$this->herokuAppName],
        ];
        $this->runProcesses($processes);
    }

    private function clean($text)
    {
        $text = trim( preg_replace( '/\s+/', ' ', $text ) );  
        $text = preg_replace("/(\r\n|\n|\r|\t)/i", '', $text);
        return $text;
    }

    private function setAppSecret()
    {
        $processes = [
            ['heroku', 'config:set', $this->appSecret, '--app='.$this->herokuAppName],
        ];
        $this->runProcesses($processes);
    }
}

 /*foreach($processes as $proc){
    $process = new Process($proc);
    try {


        if($proc[1]=="authorizations:create") {
            
            $process->setInput($this->herokuUser,$this->herokuApiKey);
        }
        $process->mustRun();
        $this->getProcessMessages($proc);
        
        echo $process->getOutput();
    } catch (ProcessFailedException $exception) {
        echo $exception->getMessage();
        $this->getColoredMessage(CommandError::HEROKU_LOG_FAILED->value, 'red');
       
        $this->output->writeln([dirname(__DIR__)]);
        $username= $_SERVER["USERNAME"];
        $this->filesystem->dumpFile('_netrc', 'machine api.heroku.com
        login '. $herokuUser .'
        password '. $herokuApiKey .'
      machine git.heroku.com
        login '. $herokuUser .'
        password '. $herokuApiKey .''); 
        $destination ='C:\Users\\'.$username.'\_netrc';
        $processes = [
            ['copy', '_netrc', $destination],
            ['heroku', 'login', '-i'],
            ['heroku', 'auth:whoami'],
        ];
        foreach($processes as $proc){
            $process = new Process($proc);
            try {
                $process->mustRun();
                $this->getProcessMessages($proc);
                
                echo $process->getOutput();
            } catch (ProcessFailedException $exception) {
                echo $exception->getMessage();
            } 
        }
    } 
}*/