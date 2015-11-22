<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 
 * Api Controller
 * @author [Sebastián Thomson] <[seba.thomson@gmail.com]>
 * 
 */
class ApiController extends Controller
{
	public function obtenerNivelesPorPlanAction(Request $request)
	{
		$idPlan    = $request->query->getInt('idPlan', null);
		$idPeriodo = $this->container->getParameter('idPeriodoVigente');

		$arrNiveles = $this->obtenerNivelesPorPlan( $idPlan, $idPeriodo );

		return new JsonResponse( $arrNiveles );
	}

	public function obtenerAreasPorPlanAction(Request $request)
	{
		$idPlan    = $request->query->getInt('idPlan', null);
		$idNivel   = $request->query->getInt('idNivel', null);
		$idPeriodo = $this->container->getParameter('idPeriodoVigente');

		$arrAreas = $this->obtenerAreasPorPlan( $idPlan, $idNivel, $idPeriodo );

		return new JsonResponse( $arrAreas );
	}

	public function obtenerNivelesPorPlan($idPlan, $idPeriodo)
	{
		return $this->get('app.PlanArea')->obtenerNivelesPorPlan($idPlan, $idPeriodo);
	}

	public function obtenerAreasPorPlan($idPlan, $idNivel, $idPeriodo)
	{
		return $this->get('app.PlanArea')->obtenerAreasPorPlan($idPlan, $idNivel, $idPeriodo);
	}
}
