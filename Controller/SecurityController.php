<?php

namespace Controller;

use Model\UserManager;

class SecurityController extends BaseController
{

    public function logoutAction()
    {
        session_destroy();
        echo $this->redirect('home');
    }

    public function registerAction()
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('home');
        }
        else{
            $errors = array();
            if ($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                $manager = UserManager::getInstance();
                $res = $manager->userCheckRegister($_POST);
                if ($res['isFormGood'])
                {
                    $manager->userRegister($res['data']);
                    $this->redirect('login');
                }
                else {
                    $errors = $res['errors'];
                }
            }
            echo $this->renderView('register.html.twig', ['errors' => $errors]);
        }
    }
    public function loginAction()
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('home');
        }
        else{
            $errors = '';
            if ($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                $manager = UserManager::getInstance();
                if ($manager->userCheckLogin($_POST))
                {
                    $manager->userLogin($_POST['username']);
                    $this->redirect('profile');
                }
                else {
                    $errors = "Pseudo ou mot de passe incorrect";
                }
            }
            echo $this->renderView('login.html.twig', ['errors' => $errors]);
        }
    }

}
