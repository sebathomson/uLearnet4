<?php

namespace DesarrolloInstrumentosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use DesarrolloInstrumentosBundle\Form\EventListener\AddNivelFieldSubscriber;
use DesarrolloInstrumentosBundle\Form\EventListener\AddAreaFieldSubscriber;

/**
 * 
 * Instrumento Type
 * @author [Sebastián Thomson] <[seba.thomson@gmail.com]>
 * 
 */
class InstrumentoType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$factory = $builder->getFormFactory();

		$builder->add('tip', 'entity', array(
			'label'          => 'Tipo Instrumento'
			,'class'         => 'AppModelBundle:TipoInstrumento'
			,'choice_label'  => 'nombre'
			,'empty_value'   => 'Seleccionar Tipo Instrumento'
			,'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) {
				return $this->devuelveQueryTipoInstrumento($repository);
			})
		);

		$builder->add('plan', 'entity', array(
			'label'          => 'Plan'
			,'class'         => 'AppModelBundle:Plan'
			,'choice_label'  => 'nombre'
			,'mapped'        => false
			,'empty_value'   => 'Seleccionar Plan'
			,'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) {
				return $this->devuelveQueryPlan($repository);
			})
		);

		$nivelSubscriber = new AddNivelFieldSubscriber($factory, $options['periodo']);
		$builder->addEventSubscriber($nivelSubscriber);

		$areaSubscriber = new AddAreaFieldSubscriber($factory, $options['periodo']);
		$builder->addEventSubscriber($areaSubscriber);

		$builder->add('nombre', null, array(
			'label'    => 'Nombre',
			'required' => true,
			)
		);
		$builder->add('descripcion', null, array(
			'label'    => 'Descripción',
			'required' => true,
			)
		);
	}
	
	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class' => 'AppModelBundle\Entity\Instrumento',
				'periodo'    => '',
				)
			);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'desarrolloinstrumentosbundle_instrumento';
	}

	private function devuelveQueryTipoInstrumento($repository)
	{
		return $repository->createQueryBuilder('s');
	}

	private function devuelveQueryPlan($repository)
	{
		return $repository->createQueryBuilder('s');
	}
}
