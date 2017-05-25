<?php

namespace Controller;

use Model\FolderManager;
use Model\UserManager;
use Model\FileManager;
use Model\LogManager;

class FolderController extends BaseController {

    public function createFolderAction() {
        $folderManager = FolderManager::getInstance();                
        $logManager = LogManager::getInstance();                
        
        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $folderManager->folderCheckAdd($_POST);
                if ($data['isFormGood']) {
                    $folderManager->createFolder($data);
                    $logManager->writeLogUser('access.log', 'User created folder.');
                    echo $this->redirect('home');
                }
                else {
                    $errors = $data['errors'];
                    $logManager->writeLogUser('security.log', 'Error create folder: invalid form.');
                    echo $this->renderView('home.html.twig', ['errors' => $errors]);
                }
            }
            else {
                $logManager->writeLogUser('security.log', 'Error create folder: method not POST.');
                echo $this->redirect('home');
            }
        }
        else {
            $logManager->writeLogUser('security.log', 'Error create folder: user not connected.');
            echo $this->redirect('login');
        }
    }

    public function renameFolderAction() {
        $folderManager = FolderManager::getInstance();                
        $logManager = LogManager::getInstance();                
    
        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $folderManager->folderCheckRename($_POST);
                if ($data['isFormGood']) {
                    $folderManager->renameFolder($data);
                    $logManager->writeLogUser('access.log', 'User renamed folder.');
                    echo $this->redirect('home');
                }
                else {
                    $errors = $data['errors'];
                    $logManager->writeLogUser('security.log', 'Error rename folder: invalid form.');
                    echo $this->renderView('home.html.twig', ['errors' => $errors]);
                }
            }
            else {
                $logManager->writeLogUser('security.log', 'Error rename folder: method not POST.');
                echo $this->redirect('home');
            }
        }
        else {
            $logManager->writeLogUser('security.log', 'Error rename folder: user not connected.');
            echo $this->redirect('login');
        }
    }

    public function deleteFolderAction() {
        $folderManager = FolderManager::getInstance();                
        $logManager = LogManager::getInstance(); 

        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $folderManager->deleteFolder($_POST);
                $logManager->writeLogUser('access.log', 'User deleted folder.');
                echo $this->redirect('home');   
            }
            else {
                $logManager->writeLogUser('security.log', 'Error delete folder: method not POST.');
                //echo $this->renderView('home.html.twig', ['errors' => $errors]);
            }
        }
        else {
            $logManager->writeLogUser('security.log', 'Error delete folder: user not connected.');
            echo $this->redirect('login');
        }
    }

    public function foldersAction() {
        $userManager = UserManager::getInstance();
        $fileManager = FileManager::getInstance();
        $folderManager = FolderManager::getInstance();
        $logManager = LogManager::getInstance();

        if (!empty($_SESSION['user_id'])) {
            $user = $userManager->getUserById($_SESSION['user_id']);
            $files = $fileManager->getUserFiles();
            $folders = $folderManager->getUserFolders($_SESSION['user_id']);
            $id_folder = (int)$_GET['id'];
            $foldersById = $fileManager->getFilesByIdFolder($id_folder);

            $logManager->writeLogUser('access.log', 'User looked into a folder.');
            echo $this->renderView('home.html.twig', [
                'user'    => $user,
                'files'   => $files,
                'folders' => $folders,
                'foldersById' => $foldersById,
                'id_folder' => $id_folder,
            ]);
        }
        else {
            $logManager->writeLogUser('security.log', 'Error folder: user not connected.');
            echo $this->redirect('login');
        }
    }


}