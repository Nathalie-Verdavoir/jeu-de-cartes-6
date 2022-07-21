<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('preferedColors', TextType::class,[
                'mapped' => false,
                'attr' => ['class' => 'd-none'],
                'label' => ' ',
            ])
            ->add('preferedValues', TextType::class,[
                'mapped' => false,
                'attr' => ['class' => 'd-none'],
                'label' => ' ',
            ])
            ->add('num', TextType::class,[
                'label' => 'Combien de cartes voulez-vous?',
                'attr' => ['class' => 'ms-2 box questions'],
            ])
            ->add('cardsTemplate', ChoiceType::class,[
                'choices' => [
                    'Fun' => 'fun',
                    'Standard' => '0',
                ],
                'attr' => ['class' => 'ms-2 box questions'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
