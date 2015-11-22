<?php

namespace AppSecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AppSecurityBundle:Default:index.html.twig', array('name' => $name));
    }
}
