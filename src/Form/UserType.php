<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array(
                'required' => true
            ))
            ->add('plainPassword', PasswordType::class, array(
                'required' => true
            ))
            ->add('username', TextType::class, array(
                'required' => true
            ))
            ->add('name', TextType::class, array(
                'required' => true
            ))
            ->add('lastname', TextType::class, array(
                'required' => true
            ))
            ->add('street', TextType::class, array(
                'required' => true
            ))
            ->add('additionalAddress', TextType::class)
            ->add('postalCode', TextType::class, array(
                'required' => true
            ))
            ->add('city', TextType::class, array(
                'required' => true
            ))
            ->add('card', CardType::class, array(
                'required' => true
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
