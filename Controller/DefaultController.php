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
            $folders = $folderManager->getUserFolders();

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
}
