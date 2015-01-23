<?php

namespace FqBundle\View\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Route extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('startLocation', new Location(), ['required' => false, 'label' => false])
            ->add('endLocation', new Location(), ['required' => false, 'label' => false])
            ->add('route', 'hidden');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => 'FqBundle\Entity\Route']);
    }
    public function getName()
    {
        return 'fqbundle_route_form';
    }
}
