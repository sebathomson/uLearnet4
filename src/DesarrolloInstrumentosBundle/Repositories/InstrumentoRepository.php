<?php

namespace DesarrolloInstrumentosBundle\Repositories;

use Doctrine\ORM\EntityManager;

/**
 * 
 * Instrumento Repository
 * @author [Sebastián Thomson] <[seba.thomson@gmail.com]>
 * 
 */
class InstrumentoRepository
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

    public function obtenerInstrumentos($arrOpciones = array())
    {
        $query = "SELECT
        ite.id              as idInstrumento,
        ite.nombre          as nombreInstrumento,
        tip.nombre          as nombreTipoInstrumento,
        are.nombre          as nombreArea,
        niv.nombre          as nombreNivel,
        ite.descripcion     as descripcionInstrumento

        FROM AppModelBundle:Instrumento ite
        LEFT JOIN ite.tip  tip
        LEFT JOIN ite.are  are
        LEFT JOIN ite.niv  niv

        WHERE 1 = 1
        ";

        $query = $this->em->createQuery($query);
        
        return $query;
        // return $query->getArrayResult();
    }

    public function obtenerInstrumentosPorId($idInstrumento)
    {
        $query = "SELECT
        ite.id              as idInstrumento,
        ite.nombre          as nombreInstrumento,
        tip.nombre          as nombreTipoInstrumento,
        are.nombre          as nombreArea,
        niv.nombre          as nombreNivel,
        ite.descripcion     as descripcionInstrumento

        FROM AppModelBundle:Instrumento ite
        LEFT JOIN ite.tip  tip
        LEFT JOIN ite.are  are
        LEFT JOIN ite.niv  niv

        WHERE 1 = 1
        
        AND ite.id = :idInstrumento

        ";

        $query = $this->em->createQuery($query);
        $query->setParameter('idInstrumento', $idInstrumento);
        
        return $query;
        // return $query->getArrayResult();
    }

}
