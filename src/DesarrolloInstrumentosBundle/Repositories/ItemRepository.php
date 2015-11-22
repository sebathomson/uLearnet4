<?php

namespace DesarrolloInstrumentosBundle\Repositories;

use Doctrine\ORM\EntityManager;

/**
 * 
 * Ítem Controller
 * @author [Sebastián Thomson] <[seba.thomson@gmail.com]>
 * 
 */
class ItemRepository
{
    /**
     *
     * @var EntityManager 
     */
    protected $em;

    /**
     *
     * @var Boolean 
     */
    public $returnQuery;

    public function __construct(EntityManager $entityManager)
    {
        $this->em          = $entityManager;
        $this->returnQuery = true;
    }

    public function obtenerItems($arrOpciones = array())
    {
        $query = "SELECT
        ite.id       as idItem,
        ite.pregunta as preguntaItem

        FROM AppModelBundle:Item ite

        WHERE 1 = 1
        ";

        $query = $this->em->createQuery($query);
        
        if ($this->returnQuery) {
            return $query;
        }

        return $query->getArrayResult();
    }

    public function obtenerItemsPorId($idItem)
    {
        $query = "SELECT
        ite.id       as idItem,
        ite.pregunta as preguntaItem

        FROM AppModelBundle:Item ite

        WHERE 1 = 1
        
        AND ite.id = :idItem

        ";

        $query = $this->em->createQuery($query);
        $query->setParameter('idItem', $idItem);
        
        if ($this->returnQuery) {
            return $query;
        }

        return $query->getArrayResult();
    }

    public function obtenerItemsPorInstrumento($idInstrumento)
    {
        $query = "SELECT
        ite.id       as idItem,
        iin.id       as idInstrumentoItem,
        ite.pregunta as preguntaItem

        FROM AppModelBundle:Item ite
        INNER JOIN AppModelBundle:InstrumentoItem iin WITH (iin.ite = ite.id)

        WHERE 1 = 1
        
        AND iin.ins = :idInstrumento

        ORDER BY  iin.id DESC

        ";

        $query = $this->em->createQuery($query);
        $query->setParameter('idInstrumento', $idInstrumento);
        
        if ($this->returnQuery) {
            return $query;
        }

        return $query->getArrayResult();
    }

    public function eliminarItemsPorInstrumento($idInstrumento)
    {
        $query = "DELETE

        FROM AppModelBundle:InstrumentoItem iin

        WHERE 1 = 1
        
        AND iin.ins = :idInstrumento
        ";

        $query = $this->em->createQuery($query);
        $query->setParameter('idInstrumento', $idInstrumento);
        
        return $query->execute();
    }

}
