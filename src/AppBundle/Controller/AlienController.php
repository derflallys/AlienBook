<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AlienController extends Controller
{
    public function newAction($name)
    {
        return $this->render('', array('name' => $name));
    }
}
