<?php

namespace FqBundle\View\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Event extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text')
            ->add('description', 'textarea')
            ->add('image', 'vlabs_file')
            ->add('category', 'entity', ['class' => 'FqBundle\Entity\Category'])
            ->add('route', new Route(), ['required' => false, 'label' => false])
            ->add('location', new Location(), ['required' => false, 'label' => false])
            ->add('city', 'hidden')
            ->add(
                'schedule',
                'collot_datetime',
                [
                    'format' => 'dd.MM.yyyy HH:mm',
                    'required' => true,
                    'pickerOptions' =>
                    [
                        'format' => 'dd.mm.yyyy HH:ii'
                    ]
                ]
            )
            ->add(
                'endTime',
                'collot_datetime',
                [
                    'required' => false,
                    'format' => 'dd.MM.yyyy HH:mm',
                    'pickerOptions' => ['format' => 'dd.mm.yyyy HH:ii']
                ]
            )
            ->add('save', 'submit', ['label' => 'Сохранить событие'])
            ->getForm();
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => 'FqBundle\Entity\Event', 'cascade_validation' => true]);
    }
    public function getName()
    {
        return 'fqbundle_event_form';
    }
}
