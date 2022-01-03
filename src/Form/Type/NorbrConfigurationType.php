<?php

namespace Sherlockode\SyliusNorbrPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

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
            ->add('merchant_id', TextType::class, [
                'label' => 'sylius.norbr.merchant_id',
                'constraints' => [new NotBlank()],
            ])
            ->add('api_key', TextType::class, [
                'label' => 'sylius.norbr.api_key',
                'constraints' => [new NotBlank()],
            ])
            ->add('production', CheckboxType::class, [
                'label' => 'sylius.norbr.enable_for_production',
                'required' => false,
            ])
        ;
    }
}
