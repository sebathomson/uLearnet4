<?php

namespace DesarrolloInstrumentosBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppModelBundle\Entity\Instrumento;
use AppModelBundle\Entity\InstrumentoItem;
use AppModelBundle\Entity\HistorialPatronInstrumento;
use DesarrolloInstrumentosBundle\Form\InstrumentoType;

/**
 * 
 * Instrumento Controller
 * @author [Sebastián Thomson] <[seba.thomson@gmail.com]>
 * 
 */
class InstrumentoController extends Controller
{
	/**
	 * Listado de todos los instrumentos.
	 */
	public function listadoAction(Request $request)
	{
		$esBusqueda    = $request->query->getBoolean('busqueda', false);
		$idInstrumento = $request->query->get('id', null);

		if ($esBusqueda && ($idInstrumento != 0)) {
			$arrOpciones = array('idInstrumento' => $idInstrumento);
			$arrInstrumentos = $this->get('desarrolloInstrumentos.Instrumento')->obtenerInstrumentos($arrOpciones);
		} elseif ($esBusqueda && ($idInstrumento == 0)) {
			$arrOpciones = $request->query->get('desarrolloinstrumentosbundle_instrumento');

			$arrInstrumentos = $this->get('desarrolloInstrumentos.Instrumento')->obtenerInstrumentos($arrOpciones);		
		} else {
			$arrInstrumentos = $this->get('desarrolloInstrumentos.Instrumento')->obtenerInstrumentos();
		}

		$paginator  = $this->get('knp_paginator');

		$pagination = $paginator->paginate(
			$arrInstrumentos,
			$request->query->getInt('page', 1),
			$this->container->getParameter('cantidadInstrumentosPorPagina')
			);

		$arrIdInstrumentos = array();

		foreach ($pagination as $instrumento) {
			$arrIdInstrumentos[] = $instrumento[ 'idInstrumento' ];
		}

		$arrEstadosInstrumento = $this->obtenerEstadosInstrumentos( $arrIdInstrumentos );

		return $this->render('DesarrolloInstrumentosBundle:Instrumento:index.html.twig', 
			array(
				'pagination'            => $pagination,
				'arrIdInstrumentos'     => $arrIdInstrumentos,
				'arrEstadosInstrumento' => $arrEstadosInstrumento,
				)
			);
	}

	public function obtenerItemsAction(Request $request)
	{
		$idInstrumento = $request->query->getInt('idInstrumento', 0);
		$idItem        = $request->query->getInt('idItem', 0);

		$arrItems = $this->obtenerItemsPorInstrumento( $idInstrumento );

		return $this->render('DesarrolloInstrumentosBundle:Instrumento:_items.html.twig', 
			array(
				'isAjax'   => true,
				'idItem'   => $idItem,
				'arrItems' => $arrItems,
				)
			);
	}

	/**
	 * Agregar un Ítem a un Instrumento.
	 */
	public function agregarItemAction(Request $request)
	{
		$idItem        = $request->request->getInt('idItem', 0);
		$idInstrumento = $request->request->getInt('idInstrumento', 0);

		if ( $idItem === 0 OR $idInstrumento === 0 ) {
			return new Response('Parámetros mal ingresados', 404);
		}

		$em = $this->getDoctrine()->getManager();

		$item = $em->getRepository('AppModelBundle:Item')->find($idItem);

		if ($item === null) {
			return new Response('Él ítem ingresado no existe', 404);
		}

		$entity = $em->getRepository('AppModelBundle:InstrumentoItem')->findOneBy(
			array(
				'ins' => $idInstrumento,
				'ite' => $idItem,
				)
			);

		if ( $entity != null) {
			return new Response('Relación ya existente', 404);
		}

		$entity = new InstrumentoItem();

		$entity->setIns( $em->getReference('AppModelBundle:Instrumento', $idInstrumento) );
		$entity->setIte( $em->getReference('AppModelBundle:Item', $idItem) );

		$em->persist($entity);
		$em->flush();

		return new Response('Ítem Agregado Correctamente', 200);
	}

	/**
	 * Quitar un Ítem a un Instrumento.
	 */
	public function quitarItemAction(Request $request)
	{
		$idInstrumentoItem = $request->request->getInt('idInstrumentoItem', 0);

		if ( $idInstrumentoItem === 0 ) {
			return new Response('Parámetros mal ingresados', 404);
		}

		$em = $this->getDoctrine()->getManager();

		$instrumentoItem = $em->getRepository('AppModelBundle:InstrumentoItem')->find($idInstrumentoItem);

		if ($instrumentoItem === null) {
			return new Response('La relación no existe', 404);
		}

		$em->remove($instrumentoItem);
		$em->flush();

		return new Response('Ítem Quitado Correctamente', 200);
	}

	/**
	 * Obtener el formulario para la búsqueda avanzada.
	 */
	public function obtenerFormularioBusquedaAction()
	{
		$entity   = new Instrumento();

		$form     = $this->createCreateForm($entity);

		$strVista = $this->renderView('DesarrolloInstrumentosBundle:Instrumento:_search.html.twig', 
			array(
				'form'   => $form->createView(),
				)
			);

		return new Response( $strVista );
	}

	/**
	 * Muestra el formulario para crear un Instrumento.
	 */
	public function nuevoAction()
	{
		$entity = new Instrumento();
		$form   = $this->createCreateForm($entity);

		return $this->render('DesarrolloInstrumentosBundle:Instrumento:new.html.twig', 
			array(
				'entity' => $entity,
				'form'   => $form->createView(),
				)
			);
	}

	/**
	 * Valida y guarda el formulario para crear.
	 */
	public function crearAction(Request $request)
	{
		$entity = new Instrumento();
		$form   = $this->createCreateForm($entity);

		$form->submit($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			// Seteando los datos del Ajax.
			// $entity->setAre( $form['area']->getData() );
			// $entity->setNiv( $form['nivel']->getData() );
			// $entity->setPla( $form['plan']->getData() );
			$entity->setPer(
				$em->getReference(
					'AppModelBundle:Periodo', $form['periodo']->getData()
					)
				);

			$entity->setFechaCreacion( new \DateTime() );
			$entity->setUser( $this->getUser() );
			$entity->setEst(
				$em->getReference(
					'AppModelBundle:EstadoInstrumento', 1
					)
				);

			$em->persist($entity);

			$oHistorialPatronInstrumento = new HistorialPatronInstrumento();
			$oHistorialPatronInstrumento->setObservacion('Creado');
			$oHistorialPatronInstrumento->setFecha(new \DateTime());
			$oHistorialPatronInstrumento->setEst(
				$em->getReference(
					'AppModelBundle:EstadoInstrumento', 1
					)
				);
			$oHistorialPatronInstrumento->setIns($entity);
			$oHistorialPatronInstrumento->setUser($this->getUser());

			$em->persist($oHistorialPatronInstrumento);

			$em->flush();

			return $this->redirect($this->generateUrl('desarrolloInstrumentos_instrumentoVer', 
				array(
					'id' => $entity->getId()
					)
				)
			);
		}

		return $this->render('DesarrolloInstrumentosBundle:Instrumento:new.html.twig',
			array(
				'entity' => $entity,
				'form'   => $form->createView(),
				)
			);
	}

	/**
	 * Encuentra y muestra la información de un Instrumento.
	 */
	public function verAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('AppModelBundle:Instrumento')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Instrumento entity.');
		}

		$arrItems = $this->obtenerItemsPorInstrumento( $id );

		return $this->render('DesarrolloInstrumentosBundle:Instrumento:show.html.twig', 
			array(
				'id'          => $id,
				// 'isAjax'      => false,
				'arrItems'    => $arrItems,
				'entity'      => $entity,
				)
			);
	}

	/**
	 * Previsualizar un Instrumento.
	 */
	public function previsualizarAction(Request $request, $id)
	{
		$page   = $request->query->getInt('page', 0);
		
		$em     = $this->getDoctrine()->getManager();
		
		$entity = $em->getRepository('AppModelBundle:Instrumento')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Instrumento entity.');
		}

		if ($page === 0) {
			// Si la página es 0, quiere decir que está ENTRANDO a la previsualización.

			return $this->render('DesarrolloInstrumentosBundle:Instrumento:preview.html.twig', 
				array(
					'id'       => $id,
					'entity'   => $entity,
					'esInicio' => true,
					)
				);
		}

		$arrItems = $this->obtenerItemsPorInstrumento( $id, true );

		$paginator  = $this->get('knp_paginator');

		$pagination = $paginator->paginate(
			$arrItems,
			$page,
			1
			);

		$pagination->setPageRange($pagination->getTotalItemCount());

		$idItem;

		foreach ($pagination as $item) {
			$idItem = $item[ 'idItem' ];
		}

		$arrAlternativas = $this->obtenerAlternativasItem( $idItem );

		return $this->render('DesarrolloInstrumentosBundle:Instrumento:preview.html.twig', 
			array(
				'id'              => $id,
				'page'            => $page,
				'entity'          => $entity,
				'esInicio'        => false,
				'arrAlternativas' => $arrAlternativas,
				'pagination'      => $pagination,
				)
			);
	}

	/**
	 * Eliminar un Instrumento.
	 */
	public function eliminarAction(Request $request)
	{
		$idInstrumento = $request->request->getInt('idInstrumento', 0);

		if ( $idInstrumento === 0 ) {
			return new Response('Parámetros mal ingresados', 404);
		}

		$em = $this->getDoctrine()->getManager();

		$instrumento = $em->getRepository('AppModelBundle:Instrumento')->find($idInstrumento);

		if ($instrumento === null) {
			return new Response('El instrumento no existe', 404);
		}

		$this->get('desarrolloInstrumentos.Item')->eliminarItemsPorInstrumento($idInstrumento);
		$this->get('desarrolloInstrumentos.Instrumento')->eliminarHistorialPorInstrumento($idInstrumento);
		$this->get('desarrolloInstrumentos.Instrumento')->eliminarRelacionPorInstrumento($idInstrumento);

		$em->remove($instrumento);
		$em->flush();

		return new Response('Instrumento Eliminado Correctamente', 200);
	}

	/**
	 * Clonar un Instrumento.
	 */
	public function clonarAction(Request $request, $id)
	{
		$nombre = $request->request->get('nombre', null);

		if ( $nombre === 0 ) {
			return new Response('Parámetros mal ingresados', 404);
		}

		$em = $this->getDoctrine()->getManager();

		$instrumento = $em->getRepository('AppModelBundle:Instrumento')->find($id);

		if ($instrumento === null) {
			return new Response('El instrumento no existe', 404);
		}

		$instrumentoClon = clone $instrumento;

		$instrumentoClon->setNombre( $nombre );
		$instrumentoClon->setIns( $instrumento );

		$instrumentoClon->setFechaCreacion( new \DateTime() );
		$instrumentoClon->setUser( $this->getUser() );

		$em->persist( $instrumentoClon );

		// Clonar la relación entre el instrumento y los ítems
		$instrumentoItems = $em->getRepository('AppModelBundle:InstrumentoItem')->findBy(
			array(
				'ins' => $id
				)
			);

		foreach ($instrumentoItems as $item) {
			$itemClon = clone $item;
			$itemClon->setIns( $instrumentoClon );

			$em->persist( $itemClon );
		}

		$oHistorialPatronInstrumento = new HistorialPatronInstrumento();
		$oHistorialPatronInstrumento->setObservacion('Creado');
		$oHistorialPatronInstrumento->setFecha(new \DateTime());
		$oHistorialPatronInstrumento->setEst(
			$em->getReference(
				'AppModelBundle:EstadoInstrumento', 1
				)
			);
		$oHistorialPatronInstrumento->setIns($instrumentoClon);
		$oHistorialPatronInstrumento->setUser($this->getUser());

		$em->persist($oHistorialPatronInstrumento);

		$em->flush();

		$urlResponse = $this->generateUrl('desarrolloInstrumentos_instrumentoVer', 
			array(
				'id' => $instrumentoClon->getId()
				)
			);

		return new Response($urlResponse, 200);
	}

	/**
	 * Muestra el formulario para editar un Instrumento.
	 */
	public function editarAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('AppModelBundle:Instrumento')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Instrumento entity.');
		}

		$form = $this->createEditForm($entity);

		return $this->render('DesarrolloInstrumentosBundle:Instrumento:edit.html.twig', 
			array(
				'entity' => $entity,
				'form'   => $form->createView(),
				)
			); 
	}

	/**
	 * Retorna el formulario para editar un Instrumento.
	 *
	 * @param Instrumento $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createEditForm(Instrumento $entity)
	{
		$form = $this->createForm(new InstrumentoType(), $entity, 
			array(
				'action' => $this->generateUrl('desarrolloInstrumentos_instrumentoActualizar', array('id' => $entity->getId())),
				'method' => 'PUT',
				'periodo' => $this->container->getParameter('idPeriodoVigente')
				)
			);

		$form->add('submit', 'submit', array('label' => 'Update'));

		return $form;
	}
	
	/**
	 * Valida y guarda el formulario para crear.
	 */
	public function actualizarAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('AppModelBundle:Instrumento')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Instrumento entity.');
		}

		$form = $this->createEditForm($entity);
		$form->submit($request);

		if ($form->isValid()) {
			$em->flush();

			return $this->redirect($this->generateUrl('desarrolloInstrumentos_instrumentoVer', array('id' => $id)));
		}

		return $this->render('DesarrolloInstrumentosBundle:Instrumento:edit.html.twig', 
			array(
				'entity'      => $entity,
				'form'   => $form->createView(),
				)
			);
	}

	/**
	 * Retorna el formulario para crear un Instrumento.
	 *
	 * @param Instrumento $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createCreateForm(Instrumento $entity)
	{
		return $this->createForm(new InstrumentoType(), $entity, 
			array(
				'action'  => $this->generateUrl('desarrolloInstrumentos_instrumentoCrear'),
				'method'  => 'POST',
				'periodo' => $this->container->getParameter('idPeriodoVigente')
				)
			);
	}

	public function obtenerItemsPorInstrumento($idInstrumento, $returnQuery = false)
	{
		$repItem = $this->get('desarrolloInstrumentos.Item');

		$repItem->returnQuery = $returnQuery;

		return $repItem->obtenerItemsPorInstrumento($idInstrumento);
	}

	public function obtenerAlternativasItem($idItem)
	{
		$repItem = $this->get('desarrolloInstrumentos.Alternativa');

		return $repItem->obtenerAlternativasPorItem($idItem);
	}

	public function obtenerEstadosInstrumentos($arrIdInstrumentos)
	{
		$estadosInstrumentos = $this->get('desarrolloInstrumentos.Instrumento')->obtenerEstadosInstrumentos($arrIdInstrumentos);

		$arrReturn = array();

		foreach ($estadosInstrumentos as $historial) {
			if (!array_key_exists($historial[ 'idInstrumento' ], $arrReturn)) {
				$arrReturn[ $historial[ 'idInstrumento' ] ] = array();
			}

			$arrReturn[ $historial[ 'idInstrumento' ] ] = $historial;
		}

		return $arrReturn;
	}
}
