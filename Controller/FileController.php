<?php

namespace Controller;

use Model\FileManager;

class FileController extends BaseController {

    public function uploadFileAction() {
        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $fileManager = FileManager::getInstance();                
                $data = $fileManager->fileCheckAdd($_POST);
                if ($data['isFormGood']) {
                    $fileManager->uploadFile($_FILES);
                    echo $this->redirect('home');
                }
                else {
                    $errors = $data['errors'];
                    echo $this->renderView('home.html.twig', ['errors' => $errors[0]]);
                }
            }
            else {
                echo $this->redirect('home');
            }
        }
        else {
            echo $this->redirect('login');
        }
    }

    public function renameFileAction() {
        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $fileManager = FileManager::getInstance();                
                $data = $fileManager->checkRenameFile($_POST);
                if ($data['isFormGood']) {
                    $fileManager->renameFile($data);
                    echo $this->redirect('home');
                }
                else {
                    $errors = $data['errors'];
                    echo $this->renderView('home.html.twig', ['errors' => $errors]);
                }
            }
            else {
                echo $this->redirect('home');
            }
        }
        else {
            echo $this->redirect('login');
        }
    }

    public function replaceFileAction() {
        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $fileManager = FileManager::getInstance();                
                $data = $fileManager->fileCheckAdd($_POST);
                // also check if new file doesn't already exist
                if ($data['isFormGood']) {
                    $fileManager->replaceFile($_POST, $_FILES);
                    echo $this->redirect('home');
                }
                else {
                    $errors = $data['errors'];
                    echo $this->renderView('home.html.twig', ['errors' => $errors]);
                }
            }
            else {
                echo $this->redirect('home');
            }
        }
        else {
            echo $this->redirect('login');
        }
    }

    public function deleteFileAction() {
        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $fileManager = FileManager::getInstance();
                $fileManager->deleteFile($_POST);
                echo $this->redirect('home');
            }
        }
        else {
            echo $this->redirect('login');
        }
    }

    public function moveFileAction() {
        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $fileManager = FileManager::getInstance();                
                $data = $fileManager->fileCheckMove($_POST);
                if ($data['isFormGood']) {
                    $fileManager->moveFile($data);
                    echo $this->redirect('home');
                }
                else {
                    $errors = $data['errors'];
                    echo $this->renderView('home.html.twig', ['errors' => $errors]);
                }
            }
            else {
                echo $this->redirect('home');
            }
        }
        else {
            echo $this->redirect('login');
        }
    }

    public function modifyFileAction() {
        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $fileManager = FileManager::getInstance();
                $fileManager->modifyFile($_POST);
                echo $this->redirect('home');
            }
        }
        else {
            echo $this->redirect('login');
        }
    }
}