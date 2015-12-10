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
class InstrumentoSearchType extends AbstractType
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

		$builder->add('pla', 'entity', array(
			'label'          => 'Plan'
			,'class'         => 'AppModelBundle:Plan'
			,'choice_label'  => 'nombre'
			,'empty_value'   => 'Seleccionar Plan'
			,'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) {
				return $this->devuelveQueryPlan($repository);
			})
		);

		$builder->add('niv', 'entity', array(
			'label'          => 'Nivel'
			,'class'         => 'AppModelBundle:Nivel'
			,'choice_label'  => 'nombre'
			,'empty_value'   => 'Seleccionar Nivel'
			,'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) {
				return $this->devuelveQueryNivel($repository);
			})
		);

		$builder->add('are', 'entity', array(
			'label'          => 'Área'
			,'class'         => 'AppModelBundle:Area'
			,'choice_label'  => 'nombre'
			,'empty_value'   => 'Seleccionar Área'
			,'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) {
				return $this->devuelveQueryArea($repository);
			})
		);

		$builder->add('per', 'entity', array(
			'label'          => 'Periodo'
			,'class'         => 'AppModelBundle:Periodo'
			,'choice_label'  => 'nombre'
			,'empty_value'   => 'Seleccionar Periodo'
			,'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) {
				return $this->devuelveQueryPeriodo($repository);
			})
		);

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

	private function devuelveQueryNivel($repository)
	{
		return $repository->createQueryBuilder('s');
	}

	private function devuelveQueryArea($repository)
	{
		return $repository->createQueryBuilder('s');
	}

	private function devuelveQueryPeriodo($repository)
	{
		return $repository->createQueryBuilder('s');
	}
}
