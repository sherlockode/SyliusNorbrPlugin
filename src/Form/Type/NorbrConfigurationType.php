<?php

namespace Sherlockode\SyliusNorbrPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class NorbrConfigurationType
 */
class NorbrConfigurationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('merchant_id', TextType::class)
            ->add('api_key', TextType::class)
            ->add('production', CheckboxType::class)
        ;
    }
}
