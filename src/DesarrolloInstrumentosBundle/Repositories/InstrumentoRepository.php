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
		$queryPorId          = '';
		$queryPorTipo        = '';
		$queryPorPeriodo     = '';
		$queryPorPlan        = '';
		$queryPorNivel       = '';
		$queryPorArea        = '';
		$queryPorNombre      = '';
		$queryPorDescripcion = '';

		if (array_key_exists('idInstrumento', $arrOpciones) && $arrOpciones[ 'idInstrumento' ] != '')
		{
			$queryPorId = ' AND ins.id = :idInstrumento ';
		}

		if (array_key_exists('tip', $arrOpciones) && $arrOpciones[ 'tip' ] != '')
		{
			$queryPorTipo = ' AND tip.id = :idTipo ';
		}

		if (array_key_exists('periodo', $arrOpciones) && $arrOpciones[ 'periodo' ] != '')
		{
			$queryPorPeriodo = ' AND per.id = :idPeriodo ';
		}

		if (array_key_exists('plan', $arrOpciones) && $arrOpciones[ 'plan' ] != '')
		{
			$queryPorPlan = ' AND pla.id = :idPlan ';
		}

		if (array_key_exists('nivel', $arrOpciones) && $arrOpciones[ 'nivel' ] != '')
		{
			$queryPorNivel = ' AND niv.id = :idNivel ';
		}

		if (array_key_exists('area', $arrOpciones) && $arrOpciones[ 'area' ] != '')
		{
			$queryPorArea = ' AND are.id = :idArea ';
		}

		if (array_key_exists('nombre', $arrOpciones) && $arrOpciones[ 'nombre' ] != '')
		{
			$queryPorNombre = ' AND ins.nombre LIKE :nombreInstrumento ';
		}

		if (array_key_exists('descripcion', $arrOpciones) && $arrOpciones[ 'descripcion' ] != '')
		{
			$queryPorDescripcion = ' AND ins.descripcion LIKE :descripcionInstrumento ';
		}

		$query = "SELECT
		ins.id              as idInstrumento,
		ins.nombre          as nombreInstrumento,
		ins.descripcion     as descripcionInstrumento,
		ins.fechaCreacion   as fechaCreacionInstrumento,
		insClon.id          as idInstrumentoClon,
		tip.nombre          as nombreTipoInstrumento,
		are.nombre          as nombreArea,
		niv.nombre          as nombreNivel,
		usr.nombres         as nombresDesarrollador,
		usr.apellidoPaterno as apellidoPaternoDesarrollador,
		usr.apellidoMaterno as apellidoMaternoDesarrollador,
		per.nombre          as nombrePeriodo,
		pla.nombre          as nombrePlan,
		est.nombre          as nombreEstado,
		est.id              as idEstado

		FROM AppModelBundle:Instrumento ins
		LEFT JOIN ins.ins  insClon
		LEFT JOIN ins.tip  tip
		LEFT JOIN ins.are  are
		LEFT JOIN ins.niv  niv
		LEFT JOIN ins.user usr
		LEFT JOIN ins.per  per
		LEFT JOIN ins.pla  pla
		LEFT JOIN ins.est  est

		WHERE 1 = 1
		
		$queryPorId
		$queryPorTipo
		$queryPorPeriodo
		$queryPorPlan
		$queryPorNivel
		$queryPorArea
		$queryPorNombre
		$queryPorDescripcion

		";

		$query = $this->em->createQuery($query);

		if (array_key_exists('idInstrumento', $arrOpciones) && $arrOpciones[ 'idInstrumento' ] != '')
		{
			$query->setParameter('idInstrumento', $arrOpciones[ 'idInstrumento' ]);
		}

		if (array_key_exists('tip', $arrOpciones) && $arrOpciones[ 'tip' ] != '')
		{
			$query->setParameter('idTipo', $arrOpciones[ 'tip' ]);
		}

		if (array_key_exists('periodo', $arrOpciones) && $arrOpciones[ 'periodo' ] != '')
		{
			$query->setParameter('idPeriodo', $arrOpciones[ 'periodo' ]);
		}

		if (array_key_exists('plan', $arrOpciones) && $arrOpciones[ 'plan' ] != '')
		{
			$query->setParameter('idPlan', $arrOpciones[ 'plan' ]);
		}

		if (array_key_exists('nivel', $arrOpciones) && $arrOpciones[ 'nivel' ] != '')
		{
			$query->setParameter('idNivel', $arrOpciones[ 'nivel' ]);
		}

		if (array_key_exists('area', $arrOpciones) && $arrOpciones[ 'area' ] != '')
		{
			$query->setParameter('idArea', $arrOpciones[ 'area' ]);
		}

		if (array_key_exists('nombre', $arrOpciones) && $arrOpciones[ 'nombre' ] != '')
		{
			$nombreInstrumento = '%' . $arrOpciones[ 'nombre' ] . '%';
			$query->setParameter('nombreInstrumento', $nombreInstrumento);
		}

		if (array_key_exists('descripcion', $arrOpciones) && $arrOpciones[ 'descripcion' ] != '')
		{
			$descripcionInstrumento = '%' . $arrOpciones[ 'descripcion' ] . '%';
			$query->setParameter('descripcionInstrumento', $descripcionInstrumento);
		}
		
		return $query;
	}

	public function obtenerEstadosInstrumentos($arrIdInstrumentos)
	{
		$query = "SELECT
		ins.id              as idInstrumento,
		hpi.observacion     as observacionInstrumento,
		hpi.fecha           as fechaInstrumento,
		est.nombre          as nombreEstado,
		est.id              as idEstado,
		usr.nombres         as nombresUsuario,
		usr.apellidoPaterno as apellidoPaternoUsuario,
		usr.apellidoMaterno as apellidoMaternoUsuario

		FROM AppModelBundle:HistorialPatronInstrumento hpi
		LEFT JOIN hpi.est  est
		LEFT JOIN hpi.ins  ins
		LEFT JOIN hpi.user usr

		WHERE 1 = 1
		
		AND ins.id IN (:arrIdInstrumentos)

		ORDER BY hpi.fecha ASC
		";

		$query = $this->em->createQuery($query);
		
		$query->setParameter('arrIdInstrumentos', $arrIdInstrumentos);
		
		return $query->getArrayResult();
	}

    public function eliminarHistorialPorInstrumento($idInstrumento)
    {
        $query = "DELETE

        FROM AppModelBundle:HistorialPatronInstrumento hpi

        WHERE 1 = 1
        
        AND hpi.ins = :idInstrumento
        ";

        $query = $this->em->createQuery($query);
        $query->setParameter('idInstrumento', $idInstrumento);
        
        return $query->execute();
    }

    public function eliminarRelacionPorInstrumento($idInstrumento)
    {
        $query = "UPDATE

        AppModelBundle:Instrumento ins

		SET ins.ins = NULL

        WHERE 1 = 1
        
        AND ins.ins = :idInstrumento
        ";

        $query = $this->em->createQuery($query);
        $query->setParameter('idInstrumento', $idInstrumento);
        
        return $query->execute();
    }
}
