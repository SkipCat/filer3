<?php

namespace Controller;

use Model\FileManager;
use Model\FolderManager;
use Model\UserManager;

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
                }else{
                    $errors = $data['errors'];
                    //echo $this->renderView('home.html.twig', ['errors' => $errors]);
                    $folderManager = FolderManager::getInstance();
                    $userManager = UserManager::getInstance();

                    $user = $userManager->getUserById($_SESSION['user_id']);
                    $files = $fileManager->getUserFiles();
                    $folders = $folderManager->getUserFolders($_SESSION['user_id']);

                    echo $this->renderView('home.html.twig', [
                        'user'    => $user,
                        'files'   => $files,
                        'folders' => $folders,
                        'errors'  => $errors,
                    ]);
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
                $fileManager->deleteFile($_POST['input-filepath']);
                echo $this->redirect('home');
            }
        }
        else {
            echo $this->redirect('login');
        }
    }

}