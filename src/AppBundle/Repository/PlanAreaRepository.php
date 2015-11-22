<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityManager;

/**
 * 
 * PlanArea Controller
 * @author [Sebastián Thomson] <[seba.thomson@gmail.com]>
 * 
 */
class PlanAreaRepository
{
    /**
     *
     * @var EntityManager 
     */
    protected $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function obtenerNivelesPorPlan($idPlan, $idPeriodo)
    {
        $query = "SELECT
        niv.id     as idNivel,
        niv.nombre as nombreNivel

        FROM AppModelBundle:PlanArea pit
        JOIN pit.niv niv

        WHERE 1 = 1
    
        AND pit.pla = :idPlan
        AND pit.per = :idPeriodo

        ";

        $query = $this->em->createQuery($query);
        $query->setParameter('idPeriodo', $idPeriodo);
        $query->setParameter('idPlan', $idPlan);
        
        return $query->getArrayResult();
    }

    public function obtenerAreasPorPlan($idPlan, $idNivel, $idPeriodo)
    {
        $query = "SELECT
        are.id     as idArea,
        are.nombre as nombreArea

        FROM AppModelBundle:PlanArea pit
        JOIN pit.are are

        WHERE 1 = 1
    
        AND pit.pla = :idPlan
        AND pit.niv = :idNivel
        AND pit.per = :idPeriodo

        ";

        $query = $this->em->createQuery($query);
        $query->setParameter('idPlan', $idPlan);
        $query->setParameter('idNivel', $idNivel);
        $query->setParameter('idPeriodo', $idPeriodo);
        
        return $query->getArrayResult();
    }

}
