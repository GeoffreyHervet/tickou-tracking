<?php

namespace AppBundle\Form;

use AppBundle\Entity\Fulfillment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FulfillmentForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('order', NumberType::class, [
                'label' => 'fulfillment.form.order',
                'attr' => [
                    'min' => 1,
                ]
            ])
            ->add('trackingNumber', TextType::class, [
                'label' => 'fulfillment.form.tracking-number',
            ])
            ->add('trackingLink', TextType::class, [
                'label' => 'fulfillment.form.tracking-link',
                'required' => false,
            ])
            ->add('shipperName', TextType::class, [
                'label' => 'fulfillment.form.shipper-name',
                'required' => false,
            ])
            ->add('supplier', TextType::class, [
                'label' => 'fulfillment.form.supplier',
                'required' => false,
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fulfillment::class,
        ]);
    }
}
