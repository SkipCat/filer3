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
                    $fileManager->uploadFile($_FILES, $_POST);
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

    public function renameFileAction() {
        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $fileManager = FileManager::getInstance();                
                $data = $fileManager->fileCheckRename($_POST);
                if ($data['isFormGood']) {
                    $fileManager->renameFile($_POST);
                    echo $this->redirect('home');
                    /*
                        $res['currentFileUrl'] = $currentFileUrl;
                        $res['newFileUrl'] = $newFileUrl;
                        $res['newFileName'] = $newFileName;
                    */ 
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
}