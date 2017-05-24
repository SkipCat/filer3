<?php

namespace Model;

use Model\UserManager;
use Model\FolderManager;

class FileManager {

    private $DBManager;
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null)
            self::$instance = new FileManager();
        return self::$instance;
    }
    private function __construct() {
        $this->DBManager = DBManager::getInstance();
    }

    public function getFileById($id) {
        $id = (int)$id;
        $data = $this->DBManager->findOne("SELECT * FROM files WHERE id = " . $id);
        return $data;
    }

    public function getFileByUrl($filepath) {
        $data = $this->DBManager->findOneSecure("SELECT * FROM files WHERE filepath = :filepath",
            ['filepath' => $filepath]);
        return $data;
    }

    public function getFileByName($filename) {
        $data = $this->DBManager->findOneSecure("SELECT * FROM files
            WHERE filename = :filename and id_user = :id_user",
            ['filename' => $filename, 'id_user' => $_SESSION['user_id']
        ]);
        return $data;
    }

    public function getUserFiles() {
        $data = $this->DBManager->findAllSecure("SELECT * FROM files WHERE id_user = :id_user
            ORDER BY date DESC", ['id_user' => $_SESSION['user_id']]);
        return $data;
    }

    public function fileCheckAdd($data) {
        $isFormGood = true;
        $errors = [];
        $res = [];

        if (isset($_FILES['userfile']['name']) && !empty($_FILES) && $_FILES['userfile']['name'] !== "") {
            $data['filename'] = $_FILES['userfile']['name'];
            $data['file_tmp_name'] = $_FILES['userfile']['tmp_name'];

            // check if newname already exists
            $fileExist = $this->getFileByName($data['filename']);
            if ($fileExist) {
                $result['isFormGood'] = false;
                $result['errors'] = 'Le fichier existe déjà';
            }
        }
        else {
            $errors['file'] = 'Veuillez choisir une image';
            $isFormGood = false;
        }

        $result['isFormGood'] = $isFormGood;
        $result['errors'] = $errors;
        return $result;
    }

    public function uploadFile($data) {
        $file['id_user'] = (int)$_SESSION['user_id'];
        $file['id_folder'] = NULL;
        $file['filename'] = $data['userfile']['name'];
        $file['extension'] = $data['userfile']['type'];
        $file['filepath'] = 'uploads/' . $_SESSION['user_name'] . '/' . $data['userfile']['name'];        
        $file['date'] = $this->DBManager->getDatetimeNow();
        $this->DBManager->insert('files', $file);

        $fileToUpdate = $this->DBManager->findOneSecure("SELECT * FROM files WHERE filepath = :filepath",
            ['filepath' => $file['filepath']]);
        $newpath = 'uploads/' . $_SESSION['user_name'] . '/' . $fileToUpdate['id'];
        $this->DBManager->findOneSecure("UPDATE files SET filepath = :newpath WHERE filepath = :filepath",
            ['filepath' => $file['filepath'], 'newpath' => $newpath]);

        move_uploaded_file($data['userfile']['tmp_name'], $newpath);
    }

    public function checkRenameFile($data) {
        $result['isFormGood'] = true;

        if (!isset($data['newname']) || empty($data['newname'])) { // empty field
            $result['isFormGood'] = false;
            $result['errors'] = 'Veuillez saisir le nouveau nom du fichier';
        }
        else {
            $file = $this->DBManager->findOneSecure("SELECT * FROM files
                WHERE id = :id", ['id' => $data['file_id']]
            );

            // check if newname already exists
            $fileExist = $this->getFileByName($data['newname']);
            if ($fileExist) {
                $result['isFormGood'] = false;
                $result['errors'] = 'Le fichier existe déjà';
            }
            else {
                $result['newname'] = $data['newname'];
                $result['file_id'] = $data['file_id'];                
            }
        }
        return $result;
    }

    // caution : extension weirdly handled
    public function renameFile($data) {
        $query = $this->DBManager->findOneSecure("UPDATE files SET filename = :newname
            WHERE id = :id", [
                'newname' => $data['newname'],
                'id' => $data['file_id']
        ]);
        return $query;
    }

    public function fileCheckReplace($files, $post) {
        $result['isFormGood'] = true;

        if (!isset($files['newfile']['name']) || empty($files['newfile']['name'])) { // empty field
            $result['isFormGood'] = false;
            $result['errors'] = 'Veuillez saisir un nouveau fichier';
        }
        else {
            $file = $this->DBManager->findOneSecure("SELECT * FROM files WHERE id = :id", 
                ['id' => $post['file_id']]);
            $newname = $files['newfile']['name'];
            $newtype = $files['newfile']['type'];

            // check if file belongs to a folder
            if ($file['id_folder'] == NULL) {
                $newpath = 'uploads/' . $_SESSION['user_name'] . '/' . $newname; // path by default
            }
            else {
                $folderManager = FolderManager::getInstance();
                $folder = $folderManager->getFolderById($file['id_folder']);
                $newpath = $folder['folderpath'] . '/' . $data['newfile']['name']; // path of file within folder
            }

            // check if newname already exists
            $fileExist = $this->getFileByName($files['newfile']['name']);
            if ($fileExist) {
                $result['isFormGood'] = false;
                $result['errors'] = 'Le fichier existe déjà';
            }
            else {
                $result['file_id'] = $post['file_id'];
                $result['newname'] = $newname;
                $result['extension'] = $newtype;
                $result['newpath'] = $newpath;                
            }
        }
        var_dump($result);
        return $result;
    }

    public function replaceFile($post, $files) {
        $file = $this->DBManager->findOneSecure("SELECT * FROM files WHERE id = :id",
            ['id' => $post['file_id']]);

        $this->deleteFile($post['file_id']);
        $this->uploadFile($files);
    }

    public function deleteFile($data) {
        $file = $this->getFileById($data['file_id']);
        $filepath = $file['filepath'];
        unlink($filepath);

        $data = $this->DBManager->findOneSecure("DELETE FROM files WHERE id = :id",
            ['id' => $data['file_id']]);
        return $data;
    }

    public function fileCheckMove($data) {
        $result['isFormGood'] = true;

        if ($data['folderpath'] == NULL) { // empty field
            $result['isFormGood'] = false;
            $result['errors'] = 'Veuillez saisir un nouveau fichier';
        }
        else {
            //  get newpath
            $file = $this->DBManager->findOneSecure("SELECT * FROM files WHERE filepath = :filepath", 
                ['filepath' => $data['filepath']]);
            $newpath = $data['folderpath'] . '/' . $file['filename'];

            $folder = $this->DBManager->findOneSecure("SELECT * FROM folders WHERE folderpath = :folderpath",
                ['folderpath' => $data['folderpath']]);
            var_dump($folder);
            $folderpath = $folder['folderpath'];
            $folderId = $folder['id'];

            // check if newname already exists
            $fileExist = $this->getFileByUrl($newpath);
            if ($fileExist) {
                $result['isFormGood'] = false;
                $result['errors'] = 'Le fichier existe déjà dans le dossier que vous avez sélectionné';
            }
            else {
                $result['filepath'] = $data['filepath'];
                $result['id_folder'] = $folderId;
                $result['newpath'] = $folderpath;                
            }
        }
        return $result;
    }

    public function moveFile($data) {
        $query = $this->DBManager->findOneSecure("UPDATE files
            SET id_folder = :new_id, filepath = :newpath
            WHERE filepath = :filepath", [
                'filepath' => $data['filepath'],
                'new_id' => $data['id_folder'],
                'newpath' => $data['newpath']
        ]);
        return $query;
    }

    public function getFileExtension($file_name){
        return strrchr($file_name, '.');
    }

    public function extensionAccept($ext){
        $extensions = array('.jpg', '.jpeg', '.txt','.png','.pdf', '.mp3', '.mp4');
        return in_array($ext, $extensions);
    }
    public function modifyFile($filepath,$current){
        return file_put_contents($filepath, $current);
    }

}
