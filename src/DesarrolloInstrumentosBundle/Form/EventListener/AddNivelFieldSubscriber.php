<?php
namespace DesarrolloInstrumentosBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;

/**
 * 
 * AddNivelFieldSubscriber EventSubscriber
 * @author [Sebastián Thomson] <[seba.thomson@gmail.com]>
 * 
 */
class AddNivelFieldSubscriber implements EventSubscriberInterface
{
	private $factory;
	private $periodo;

	public function __construct(FormFactoryInterface $factory, $periodo)
	{
		$this->factory = $factory;
		$this->periodo = $periodo;
	}

	public static function getSubscribedEvents()
	{
		return array(
			FormEvents::PRE_SET_DATA  => 'preSetData',
			FormEvents::PRE_SUBMIT    => 'preBind'
			);
	}

	private function addFieldForm($form, $plan = null)
	{    
		$form->add($this->factory->createNamed('nivel', 'entity', null, 
			array(
				'label'           => 'Nivel',
				'class'           => 'AppModelBundle:Nivel',
				'choice_label'    => 'nombre',
				'mapped'          => false,
				'auto_initialize' => false,
				'empty_value'     => 'Seleccionar Nivel',
				'query_builder'   => function (EntityRepository $repository) use ($plan)
				{
					return $this->devuelveQueryField($repository, $plan);
				}
				)
			)
		);
	}

	public function preSetData(FormEvent $event)
	{
		$data = $event->getData();
		$form = $event->getForm();
		$this->addFieldForm($form);
	}

	public function preBind(FormEvent $event)
	{
		$data = $event->getData();
		$form = $event->getForm();

		if (null === $data) {
			return;
		}

		$plan = array_key_exists('plan', $data) ? $data['plan'] : null;

		$this->addFieldForm($form, $plan);
	}

	private function devuelveQueryField($repository, $plan)
	{
		if ($plan === null) {
			return $repository->createQueryBuilder('p')->where('p.id is null');
		}

		return $repository->createQueryBuilder('p')
		->leftJoin('AppModelBundle:PlanArea', 'pa', 'WITH', 'pa.niv = p.id')
		->where('pa.pla = :plan')          
		->setParameter('plan', $plan);
	}
}