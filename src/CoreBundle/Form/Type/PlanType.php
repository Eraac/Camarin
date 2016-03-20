<?php

namespace CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PlanType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentYear = date('Y');

        $builder
            ->add('time', TimeType::class, ['label' => 'core.form.plan.time', 'minutes' => range(0, 45, 15)])
            ->add('expireAt', DateType::class, ['label' => 'core.form.plan.expireAt', 'years' => range($currentYear, $currentYear + 3)])
            ->add('description', TextareaType::class, ['label' => 'core.form.plan.description'])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CoreBundle\Entity\Plan',
            'translation_domain' => 'form',
        ));
    }
}
