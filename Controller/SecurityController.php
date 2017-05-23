<?php

namespace Controller;

use Model\UserManager;

class SecurityController extends BaseController {

    public function logoutAction() {
        session_destroy();
        echo $this->redirect('home');
    }

    public function registerAction() {
        if (empty($_SESSION['user_id'])) {
            $errors = [];
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $manager = UserManager::getInstance();
                $result = $manager->userCheckRegister($_POST);
                if ($result['isFormGood']) {
                    $manager->userRegister($result['data']);
                    echo $this->redirect('login');
                }
                else {
                    $errors = $result['errors'];
                }
            }
            echo $this->renderView('register.html.twig', ['errors' => $errors]);
        }
        else {
            echo $this->redirect('home');
        }
    }

    public function loginAction() {
        if (empty($_SESSION['user_id'])) {
            $errors = '';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $manager = UserManager::getInstance();
                if ($manager->userCheckLogin($_POST)) {
                    $manager->userLogin($_POST['username']);
                    echo $this->redirect('home');
                }
                else {
                    $errors = "Pseudo ou mot de passe incorrect";
                }
            }
            echo $this->renderView('login.html.twig', ['errors' => $errors]);
        }
        else {
            echo $this->redirect('home');
        }
    }

}
