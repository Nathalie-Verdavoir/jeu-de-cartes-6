<?php

namespace App\Command;

enum CommandError: string
{
    case HEROKU_LOG_FAILED = 'Please run \'heroku login -i\' command';
}
