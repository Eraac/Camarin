<?php

namespace CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class InterventionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('time', TimeType::class, ['label' => 'core.form.intervention.time', 'minutes' => range(0, 45, 15)])
            ->add('date', DateType::class, ['label' => 'core.form.intervention.date'])
            ->add('description', TextareaType::class, ['label' => 'core.form.intervention.description'])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'CoreBundle\Entity\Intervention',
            'translation_domain' => 'form',
        ]);
    }
}
