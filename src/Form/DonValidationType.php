<?php

namespace App\Form;

use App\Entity\Don;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DonValidationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeDon', ChoiceType::class, [
                'label' => 'Type de don',
                'choices' => [
                    'Sang total' => 'Sang total',
                    'Plasma' => 'Plasma',
                    'Plaquettes' => 'Plaquettes',
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité (ml)',
                'attr' => ['class' => 'form-control', 'min' => 1]
            ])
            ->add('apte', CheckboxType::class, [
                'label' => 'Donateur apte',
                'required' => false,
                'attr' => ['class' => 'form-check-input']
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaire',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Don::class,
        ]);
    }
}

