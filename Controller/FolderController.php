<?php

namespace Controller;

use Model\FolderManager;
use Model\UserManager;
use Model\FileManager;

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
            echo $this->redirect('home');
        }
        else {
            echo $this->redirect('login');
        }
    }

    public function foldersAction() {
        if (!empty($_SESSION['user_id'])) {
            $userManager = UserManager::getInstance();
            $fileManager = FileManager::getInstance();
            $folderManager = FolderManager::getInstance();

            $user = $userManager->getUserById($_SESSION['user_id']);
            $files = $fileManager->getUserFiles();
            $folders = $folderManager->getUserFolders($_SESSION['user_id']);

            $id_folder = (int)$_GET['id'];
            $foldersById = $fileManager->getFilesByIdFolder($id_folder);

            echo $this->renderView('home.html.twig', [
                'user'    => $user,
                'files'   => $files,
                'folders' => $folders,
                'foldersById' => $foldersById,
                'id_folder' => $id_folder,
            ]);
        }
        else {
            echo $this->redirect('login');
        }
    }


}