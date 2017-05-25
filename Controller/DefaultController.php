<?php

namespace Controller;

use Model\UserManager;
use Model\FileManager;
use Model\FolderManager;

class DefaultController extends BaseController {

    public function homeAction() {
        if (!empty($_SESSION['user_id'])) {
            $userManager = UserManager::getInstance();
            $fileManager = FileManager::getInstance();
            $folderManager = FolderManager::getInstance();

            $user = $userManager->getUserById($_SESSION['user_id']);
            $files = $fileManager->getUserFiles();
            $folders = $folderManager->getUserFolders($_SESSION['user_id']);

            //getContentFile
            foreach ($files as $file) {
                if ($file['extension'] == 'text/plain') {
                    $contentFiles[$file['filepath']] = file_get_contents($file['filepath']);
                }
            }

            echo $this->renderView('home.html.twig', [
                'user'    => $user,
                'files'   => $files,
                'folders' => $folders,
            ]);
        }
        else {
            echo $this->redirect('login');
        }
    }

    public function profileAction() {
        if (!empty($_SESSION['user_id'])) {
            $userManager = UserManager::getInstance();
            $user = $userManager->getUserById($_SESSION['id']);
            echo $this->renderView('profile.html.twig', ['user' => $user]);
        }
        else {
            echo $this->redirect('login');
        }
    }

}
