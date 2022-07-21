<?php

namespace App\Controller;
use App\Entity\Color;
use App\Entity\Value;
use App\Entity\Deck;
use App\Entity\Game;
use App\Form\GameType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    /**
     * @Route("/game", name="game_index")
     */
    public function gameIndex(): Response // show mixed full deck
    {
        $deck = new Deck();

        return $this->render('game/index.html.twig', [
            "cards" => $deck->getMixedDeck(),
        ]);
    }

    /**
     * @Route("/reset", name="game_reset", methods={"GET","POST"})
     */
    public function gameReset(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('game');
        return $this->redirectToRoute('game_init');
    }

    /**
     * @Route("/", name="game_init", methods={"GET","POST"})
     */
    public function gameInit(Request $request): Response
    {


        $session = $request->getSession();

        $game = $session->get('game', new Game());//if no game in session, create a new one (second argument)
        $session->set('game',$game);
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //new number of cards to keep
            $game->setNum($form->getData()->getNum());
            //new set of cards
            $game->setCardsTemplate($form->getData()->getCardsTemplate());
            //new prefered order of colors and values
            $deck = $game->getDeck();
            $colorsFromForm = explode(',',$form->get('preferedColors')->getData());
            $colorsToSave = [];
            $len = count($colorsFromForm);
            for($i=0;$i<$len;$i++){
                foreach(Color::cases() as $enum){
                    if($enum->name === $colorsFromForm[$i]){
                        $colorsToSave[] =   $enum;
                    }
                }
            }
           
            $deck->setMixedColors($colorsToSave);

            $valuesFromForm = explode(',',$form->get('preferedValues')->getData());
            $valuesToSave = [];
            $len = count($valuesFromForm);
            for($j=0;$j<$len;$j++){
                foreach(Value::cases() as $enum){
                    if($enum->name === $valuesFromForm[$j]){
                        $valuesToSave[] =   $enum;
                    }
                }
            }
           
            $deck->setMixedValues($valuesToSave);
            
            
            $game->setDeck($deck);
            //save it in session
            $session->set('game',$game);

            return $this->redirectToRoute('game_index_random');
        }

        return $this->renderForm('game/init.html.twig',array(
            'form' => $form,
            ));

    }

    /**
     * @Route("/random/", name="game_index_random")
     */
    public function gameHand(Request $request): Response
    {

        $session = $request->getSession();
        $game=$session->get('game', new Game());
        $deck = $game->getDeck();
        $colors = $deck->getMixedColors();
        $values = $deck->getMixedValues();
        $cards = $deck->getFullDeck();
        $numberOfCards = $game->getNum();
        $orderedResult =  [];
        $unorderedResult =  [];
        $index = array_rand($cards,$numberOfCards);
        if($numberOfCards>1){
            for ($i=0;$i < $numberOfCards ;$i++){
                $orderedResult[$index[$i]] = $cards[$index[$i]];
                unset($cards[$index[$i]]);
            }
        }else{$orderedResult[0] = $cards[0];}

        $session->set('orderedResult', $orderedResult);
        foreach ($colors as $color){
            foreach ($values as $value){
                foreach ($orderedResult as $card){
                    /** @var Color $color */
                    if ($card->color === $color && $card->value === $value){
                        $unorderedResult[] = $card;
                    }
                }
            }
        }
        $session->set('unorderedResult', $unorderedResult);


        return $this->render('game/play.html.twig');
    }

    /**
     * @Route("/random_colors", name="game_random_colors")
     */
    public function randomColors(Request $request): Response
    {

        $session = $request->getSession();
        $game=$session->get('game');
        $deck = $game->getDeck();

        $colors = $deck->getMixedColors();
        $colors = $deck->random($colors);

        $deck->setMixedColors($colors);
        $game->setDeck($deck);

        $session->set('game', $game);


        return $this->redirectToRoute('game_init');
    }

    /**
     * @Route("/random_values", name="game_random_values")
     */
    public function randomValues(Request $request): Response
    {

        $session = $request->getSession();
        $game=$session->get('game');
        $deck = $game->getDeck();

        $values = $deck->getMixedValues();
        $values = $deck->random($values);

        $deck->setMixedValues($values);
        $game->setDeck($deck);

        $session->set('game', $game);


        return $this->redirectToRoute('game_init');
    }
}
