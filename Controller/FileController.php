<?php

namespace Controller;

use Model\FileManager;
use Model\FolderManager;
use Model\LogManager;

class FileController extends BaseController {

    public function uploadFileAction() {
        $fileManager = FileManager::getInstance();
        $folderManager = FolderManager::getInstance();
        $logManager = LogManager::getInstance();

        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $fileManager->fileCheckAdd($_POST);
                if ($data['isFormGood']) {
                    $fileManager->uploadFile($data, $_FILES);
                    $logManager->writeLogUser('access.log', 'User uploaded file.');                    
                    echo $this->redirect('home');
                }
                else {
                    $errors = $data['errors'];
                    $files = $fileManager->getUserFiles();
                    $folders = $folderManager->getUserFolders($_SESSION['user_id']);
                    
                    $logManager->writeLogUser('security.log', 'Error upload file: invalid form.');                      
                    echo $this->renderView('home.html.twig', [
                        'errors' => $errors,
                        'files' => $files,
                        'folders' => $folders
                    ]);
                }
            }
            else {
                $logManager->writeLogUser('security.log', 'Error upload file: method not POST.');                  
                echo $this->redirect('home');
            }
        }
        else {
            $logManager->writeLogUser('security.log', 'Error upload file: user not connected.');              
            echo $this->redirect('login');
        }
    }

    public function renameFileAction() {
        $logManager = LogManager::getInstance();
        $fileManager = FileManager::getInstance();  

        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {             
                $data = $fileManager->checkRenameFile($_POST);
                if ($data['isFormGood']) {
                    $fileManager->renameFile($data);
                    $logManager->writeLogUser('access.log', 'User renamed file.');    
                    echo $this->redirect('home');
                }
                else {
                    $errors = $data['errors'];
                    $logManager->writeLogUser('security.log', 'Error rename file: invalid form.');    
                    echo $this->renderView('home.html.twig', ['errors' => $errors]);
                }
            }
            else {
                $logManager->writeLogUser('security.log', 'Error rename file: method not POST.');    
                echo $this->redirect('home');
            }
        }
        else {
            $logManager->writeLogUser('security.log', 'Error rename file: user not connected.');  
            echo $this->redirect('login');
        }
    }

    public function replaceFileAction() {
        $logManager = LogManager::getInstance();
        $fileManager = FileManager::getInstance();                

        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $fileManager->fileCheckAdd($_POST);
                // also check if new file doesn't already exist
                if ($data['isFormGood']) {
                    $fileManager->replaceFile($_POST, $_FILES);
                    $logManager->writeLogUser('access.log', 'User replaced file.');    
                    echo $this->redirect('home');
                }
                else {
                    $errors = $data['errors'];
                    $logManager->writeLogUser('security.log', 'Error replace file: invalid form.');    
                    echo $this->renderView('home.html.twig', ['errors' => $errors]);
                }
            }
            else {
                $logManager->writeLogUser('security.log', 'Error replace file: method not POST.');    
                echo $this->redirect('home');
            }
        }
        else {
            $logManager->writeLogUser('security.log', 'Error replace file: user not connected.');    
            echo $this->redirect('login');
        }
    }

    public function deleteFileAction() {
        $logManager = LogManager::getInstance();
        $fileManager = FileManager::getInstance();

        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $fileManager->deleteFile($_POST['file_id']);
                $logManager->writeLogUser('access.log', 'User deleted file.'); 
                echo $this->redirect('home');
            }
            else {
                $logManager->writeLogUser('security.log', 'Error delete file: method not POST.');   
            }
        }
        else {
            $logManager->writeLogUser('security.log', 'Error delete file: user not connected.');   
            echo $this->redirect('login');
        }
    }

    public function moveFileAction() {
        $logManager = LogManager::getInstance();
        $fileManager = FileManager::getInstance();

        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $fileManager->fileCheckMove($_POST);
                if ($data['isFormGood']) {
                    $fileManager->moveFile($data);
                    $logManager->writeLogUser('access.log', 'User moved file.'); 
                    echo $this->redirect('home');
                }
                else {
                    $errors = $data['errors'];
                    $logManager->writeLogUser('security.log', 'Error move file: invalid form.');
                    echo $this->renderView('home.html.twig', ['errors' => $errors]);
                }
            }
            else {
                $logManager->writeLogUser('security.log', 'Error move file: method not post.');
                echo $this->redirect('home');
            }
        }
        else {
            $logManager->writeLogUser('security.log', 'Error move file: user not connected.');  
            echo $this->redirect('login');
        }
    }

    public function modifyFileAction() {
        $fileManager = FileManager::getInstance();
        $logManager = LogManager::getInstance();

        if (!empty($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $fileManager->modifyFile($_POST);
                $logManager->writeLogUser('access.log', 'User modified file.');
                echo $this->redirect('home');
            }
            else {
                $logManager->writeLogUser('security.log', 'Error modify file: method not POST.');
            }
        }
        else {
            $logManager->writeLogUser('security.log', 'Error modify file: user not connected.');
            echo $this->redirect('login');
        }
    }

}