<?php

namespace Controller;

use Model\FolderManager;

class FolderController extends BaseController {

    public function createFolderAction() {
        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $folderManager = FolderManager::getInstance();                
                $data = $folderManager->folderCheckAdd($_POST);
                if ($data['isFormGood']) {
                    $folderManager->createFolder($data);
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

    public function renameFolderAction() {
        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $folderManager = FolderManager::getInstance();                
                $data = $folderManager->folderCheckRename($_POST);
                if ($data['isFormGood']) {
                    $folderManager->renameFolder($data);
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

    public function deleteFolderAction() {
        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $folderManager = FolderManager::getInstance();
                $folderManager->deleteFolder($_POST);
            }
            else {
                echo $this->redirect('home');
            }
        }
        else {
            echo $this->redirect('login');
        }
    }
}