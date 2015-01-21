<?php

namespace FqBundle\View\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @Route("/event")
 */
class Location extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('city')
            ->add('address')
            ->add('latitude')
            ->add('longitude');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => 'FqBundle\Entity\Location']);
    }
    public function getName()
    {
        return 'fqbundle_location_form';
    }
}
