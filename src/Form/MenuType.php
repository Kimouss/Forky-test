<?php

namespace App\Form;

use App\Entity\Menu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class MenuType extends AbstractType
{
    public function __construct(
        private readonly RouterInterface $router,
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $routes = $this->router->getRouteCollection()->all();
        $routeChoices = [];

        foreach ($routes as $name => $route) {
            if (str_starts_with($name, 'app_')) {
                $routeChoices[$name] = $name;
            }
        }

        $builder
            ->add('label')
            ->add('route', ChoiceType::class, [
                'choices' => $routeChoices,
                'placeholder' => 'Select a route or leave empty',
                'required' => false,
            ])
            ->add('active', CheckboxType::class, [
                'required' => false,
                'empty_data' => null,
            ])
            ->add('visible', CheckboxType::class, [
                'required' => false,
                'empty_data' => null,
            ])
            ->add('position', NumberType::class, [
                'required' => false, // Rend le champ non obligatoire
                'empty_data' => 100, // Définit la valeur par défaut si rien n'est saisi
                'data' => 100, // Affiche 100 comme valeur par défaut dans le champ
            ])
            ->add('parent', EntityType::class, [
                'class' => Menu::class,
                'choice_label' => function (Menu $menu) {
                    return $menu->getParent() === null
                        ? $menu->getLabel() . ' (Parent)'
                        : $menu->getLabel();
                },
                'required' => false,
                'placeholder' => 'No parent',
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
