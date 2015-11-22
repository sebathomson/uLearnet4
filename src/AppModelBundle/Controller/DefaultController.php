<?php

namespace AppModelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AppModelBundle:Default:index.html.twig', array('name' => $name));
    }
}
