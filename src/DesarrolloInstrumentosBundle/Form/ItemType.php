<?php

namespace DesarrolloInstrumentosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * 
 * Ítem Controller
 * @author [Sebastián Thomson] <[seba.thomson@gmail.com]>
 * 
 */
class ItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('iteId')
            ->add('justificacion')
            ->add('pregunta')
            ->add('imagen')
            ->add('id2')
            ->add('con')
            ->add('dif')
            ->add('tex')
            ->add('ins')
            ->add('alt')
            ->add('obj')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppModelBundle\Entity\Item'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'desarrolloinstrumentosbundle_item';
    }
}
