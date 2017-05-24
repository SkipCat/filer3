<?php

namespace Model;

use Model\UserManager;

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
            $result['data'] = $data;
        }
        else {
            $errors[] = 'Veuillez choisir une image';
            $isFormGood = false;
        }

        $result['isFormGood'] = $isFormGood;
        $result['errors'] = $errors;
        return $result;
    }

    public function uploadFile($files, $post) {
        if (empty($post['newname'])) {
            $filename = $files['userfile']['name'];
            echo 'filename';
        } 
        else {
            $filename = $post['newname'];
            echo 'newname';
        }

        $file['id_user'] = (int)$_SESSION['user_id'];
        $file['id_folder'] = NULL;
        $file['filename'] = $filename;
        $file['extension'] = $files['userfile']['type'];
        $file['filepath'] = 'uploads/' . $_SESSION['user_name'] . '/' . $filename;        
        $file['date'] = $this->DBManager->getDatetimeNow();

        $this->DBManager->insert('files', $file);
        move_uploaded_file($files['userfile']['tmp_name'], $file['filepath']);
    }

    public function checkRenameFile($data) {
        $result['isFormGood'] = true;

        if (!isset($data['newname']) || empty($data['newname'])) { // empty field
            $result['isFormGood'] = false;
            $result['errors'] = 'Veuillez saisir le nouveau nom du fichier';
        }
        else {
            $file = $this->DBManager->findOneSecure("SELECT * FROM files WHERE filename = :filename", 
                ['filename' => $data['filename']]);

            // check if file belongs to a folder
            if ($file['id_folder'] == NULL) {
                $newpath = 'uploads/' . $_SESSION['user_name'] . '/' . $data['newname']; // path by default
            }
            else {
                $folderManager = FolderManager::getInstance();
                $folder = $folderManager->getFolderById($file['id_folder']);
                $newpath = $folder['folderpath'] . '/' . $newname; // path of file within folder
            }
            $newpath = 'uploads/' . $_SESSION['user_name'] . '/' . $data['newname'];

            // check if newname already exists
            $fileExist = $this->getFileByUrl($newpath);
            if ($fileExist) {
                $result['isFormGood'] = false;
                $result['errors'] = 'Le fichier existe déjà';
            }
            else {
                $result['filepath'] = $file['filepath'];
                $result['newname'] = $data['newname'];
                $result['newpath'] = $newpath;
            }
        }
        return $result;
    }
    public function renameFile($data) {
        $currentPath = $data['filepath'];
        $newpath = $data['newpath'];
        $newname = $data['newname'];
        rename($currentPath, $newpath);

        $file = $this->DBManager->findOneSecure("UPDATE files
            SET filename = :newname, filepath = :newpath
            WHERE filepath = :currentPath", [
                'newname' => $newname,
                'newpath' => $newpath,
                'currentPath' => $currentPath,
            ]
        );
        return $file;
    }

    public function deleteFile($filepath) {
        unlink($filepath);
        return $this->DBManager->findOneSecure("DELETE FROM files WHERE filepath = :filepath",
            ['filepath' => $filepath]);
    }

    public function getFileExtension($file_name){
        return strrchr($file_name, '.');
    }

    public function extensionAccept($ext){
        $extensions = array('.jpg', '.jpeg', '.txt','.png','.pdf', '.mp3', '.mp4');
        return in_array($ext, $extensions);
    }

}
