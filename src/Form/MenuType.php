<?php

namespace App\Form;

use App\Entity\Menu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label')
            ->add('route', null, [
                'empty_data' => null,
            ])
            ->add('isActive')
            ->add('parent', EntityType::class, [
                'class' => Menu::class,
                'choice_label' => function (Menu $menu) {
                    if ($menu->getParent() === null) {
                        return $menu->getLabel(). '(Parent)';
                    }

                    return $menu->getLabel();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
