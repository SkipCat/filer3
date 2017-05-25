<?php

namespace Controller;

use Model\UserManager;
use Model\FileManager;
use Model\FolderManager;
use Model\LogManager;

class DefaultController extends BaseController {

    public function homeAction() {
        $userManager = UserManager::getInstance();
        $fileManager = FileManager::getInstance();
        $folderManager = FolderManager::getInstance();
        $logManager = LogManager::getInstance();

        if (!empty($_SESSION['user_id'])) {
            $user = $userManager->getUserById($_SESSION['user_id']);
            $files = $fileManager->getUserFiles();
            $folders = $folderManager->getUserFolders($_SESSION['user_id']);

            //getContentFile
            foreach ($files as $file) {
                if ($file['extension'] == 'text/plain') {
                    $contentFiles[$file['filepath']] = file_get_contents($file['filepath']);
                }
            }
            
            $logManager->writeLogUser('access.log', 'Page home.');
            echo $this->renderView('home.html.twig', [
                'user'    => $user,
                'files'   => $files,
                'folders' => $folders,
            ]);
        }
        else {
            $logManager->writeLogUser('security.log', 'Error home: user not connected.');
            echo $this->redirect('login');
        }
    }

}
