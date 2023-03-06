<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => "Nom d'utilisateur",
                'constraints' => [
                    new Assert\NotBlank([ 'message' => 'Vous devez entrez un nom' ]),
                    new Assert\Length([
                        'min' => 3, 'minMessage' => 'Votre nom doit faire au moins {{ limit }} caractères.',
                        'max' => 20, 'maxMessage' => 'Votre nom ne peut pas faire plus de {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,

                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'required' => true,
                'first_options'  => ['always_empty' => false, 'label' => 'Mot de passe'],
                'second_options' => ['always_empty' => false, 'label' => 'Tapez le mot de passe à nouveau'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'constraints' => [
                    new Assert\Email([ 'message' => "Le format de l'email n'est pas valide"])
                ]
            ])
            ->add('is_admin', CheckboxType::class, [
                'data' => $options['is_admin_checked'],
                'required' => false,
                'mapped' => false,
                'label' => "Définir comme administrateur"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_type' => User::class,
            'is_admin_checked' => false
        ]);
    }
}
