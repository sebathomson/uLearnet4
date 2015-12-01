<?php

namespace DesarrolloInstrumentosBundle\Repositories;

use Doctrine\ORM\EntityManager;

/**
 * 
 * Ítem Controller
 * @author [Sebastián Thomson] <[seba.thomson@gmail.com]>
 * 
 */
class AlternativaRepository
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

	public function obtenerAlternativasPorItem($idItem)
	{
		$query = "SELECT
		ite.id    as idItem,
		alt.id    as idAlternativa,
		alt.texto as textoAlternativa

		FROM AppModelBundle:Alternativa alt
		JOIN alt.ite ite

		WHERE 1 = 1
		
		AND ite.id = :idItem
		";

		$query = $this->em->createQuery($query);
		$query->setParameter('idItem', $idItem);

		return $query->getArrayResult();
	}
}
