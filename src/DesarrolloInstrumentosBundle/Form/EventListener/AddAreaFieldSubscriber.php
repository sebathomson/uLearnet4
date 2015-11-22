<?php
namespace DesarrolloInstrumentosBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;

/**
 * 
 * AddAreaFieldSubscriber EventSubscriber
 * @author [Sebastián Thomson] <[seba.thomson@gmail.com]>
 * 
 */
class AddAreaFieldSubscriber implements EventSubscriberInterface
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

    private function addFieldForm($form, $plan = null, $nivel = null)
    {
        $options          = array();
        $options['plan']  = $plan;
        $options['nivel'] = $nivel;

        $form->add($this->factory->createNamed('area', 'entity', null, 
            array(
                'label'           => 'Area',
                'class'           => 'AppModelBundle:Area',
                'choice_label'    => 'nombre',
                'mapped'          => false,
                'auto_initialize' => false,
                'empty_value'     => 'Seleccionar Area',
                'query_builder'   => function (EntityRepository $repository) use ($options)
                {
                    return $this->devuelveQueryField($repository, $options['plan'], $options['nivel'] );
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

        $plan  = array_key_exists('plan', $data) ? $data['plan'] : null;
        $nivel = array_key_exists('nivel', $data) ? $data['nivel'] : null;

        $this->addFieldForm($form, $plan, $nivel);
    }

    private function devuelveQueryField($repository, $plan, $nivel)
    {
        if ($plan === null) {
            return $repository->createQueryBuilder('p')->where('p.id is null');
        }

        return $repository->createQueryBuilder('p')
        ->leftJoin('AppModelBundle:PlanArea', 'pa', 'WITH', 'pa.are = p.id')
        ->where('pa.pla = :plan')          
        ->andWhere('pa.niv = :nivel')          
        ->setParameter('plan', $plan)
        ->setParameter('nivel', $nivel);
    }
}