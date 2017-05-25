<?php

namespace Controller;

use Model\UserManager;
use Model\LogManager;

class SecurityController extends BaseController {

    public function logoutAction() {
        $logManager = LogManager::getInstance();
        $logManager->writeLogUser('access.log', 'User logged out.');
        session_destroy();
        echo $this->redirect('home');
    }

    public function registerAction() {
        $errors = [];        
        $userManager = UserManager::getInstance();
        $logManager = LogManager::getInstance();

        if (empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $result = $userManager->userCheckRegister($_POST);
                if ($result['isFormGood']) {
                    $userManager->userRegister($result['data']);
                    $logManager->writeLogUser('access.log', 'User registered.');                                     
                    echo $this->redirect('login');
                }
                else {
                    $errors = $result['errors'];
                    $logManager->writeLogUser('security.log', 'Error register: invalid form.');                    
                    echo $this->renderView('register.html.twig', ['errors' => $errors]);
                }
            }
            else {
                $logManager->writeLogUser('security.log', 'Error register: method not POST.');                
                echo $this->renderView('register.html.twig', ['errors' => $errors]);                
            }
        }
        else {
            $logManager->writeLogUser('security.log', 'Error register: user already connected.');            
            echo $this->redirect('home');
        }
    }

    public function loginAction() {
        $errors = '';
        $userManager = UserManager::getInstance();
        $logManager = LogManager::getInstance();

        if (empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if ($userManager->userCheckLogin($_POST)) {
                    $userManager->userLogin($_POST['username']);
                    $logManager->writeLogUser('access.log', 'User logged in.');
                    echo $this->redirect('home');
                }
                else {
                    $errors = "Pseudo ou mot de passe incorrect";
                    $logManager->writeLogUser('security.log', 'Error login: invalid form.');                    
                    echo $this->renderView('login.html.twig', ['errors' => $errors]);
                }
            }
            else {
                $logManager->writeLogUser('security.log', 'Error login: method not POST.');
                echo $this->renderView('login.html.twig', ['errors' => $errors]);  
            }
        }
        else {
            $logManager->writeLogUser('security.log', 'Error login: user already connected.');
            echo $this->redirect('home');
        }
    }

}
