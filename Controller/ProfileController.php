<?php
/**
 * Created by PhpStorm.
 * User: sow
 * Date: 05/05/17
 * Time: 20:18
 */

namespace Controller;
use Model\FileManager;
use Model\UserManager;


class ProfileController extends BaseController
{
    /*public function profileAction()
    {

        if (!empty($_SESSION['user_id'])) {
            $userManager = UserManager::getInstance();
            $fileManager = FileManager::getInstance();
            $errors = array();
            $user = $userManager->getUserById((int)$_SESSION['user_id']);
            $myFiles = $fileManager->getUserFiles();
            if(isset($_POST['submitAddFile'])){
                $res = $fileManager->fileCheckAdd($_POST);
                if($res['isFormGood']){
                    $fileManager->addFile($res['data']);
                    header('Location:?action=profile');
                }else{
                    $errors[] = $res['errors'];
                }
            }
            if(isset($_POST['submitRenameFile'])){
                $res = $fileManager->checkRenameFile($_POST);
                if($res['isFormGood']){
                    $fileManager->renameFile($res);
                    header('Location:?action=profile');
                }
            }
            if(isset($_POST['submitDeleteFile'])){
                $fileManager->deleteFile($_POST['fileToDelete']);
                header('Location:?action=profile');
            }
            echo $this->renderView('profile.html.twig',
                                    [
                                        'user'   => $user,
                                        'myFiles' => $myFiles,
                                        'errors' => $errors,
                                    ]
                );
        } else {
            $this->redirect('home');
        }


    }*/



}