<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * 
 * Default Controller
 * @author [Sebastián Thomson] <[seba.thomson@gmail.com]>
 * 
 */
class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('AppBundle::inicio.html.twig');
    }
}
