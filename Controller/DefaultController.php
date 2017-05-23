<?php

namespace Controller;

use Model\UserManager;

class DefaultController extends BaseController
{
    public function homeAction()
    {
        echo $this->renderView('home.html.twig');
    }
}
