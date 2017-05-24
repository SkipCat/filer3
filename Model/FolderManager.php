<?php

namespace Model;

class FolderManager {

    private $DBManager;
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null)
            self::$instance = new FolderManager();
        return self::$instance;
    }
    private function __construct() {
        $this->DBManager = DBManager::getInstance();
    }

    public function getFolderByUrl($folderpath) {
        $data = $this->DBManager->findOneSecure("SELECT * FROM folders WHERE folderpath = :folderpath",
            ['folderpath' => $folderpath]);
        return $data;
    }

    public function getAllFolders() {
        $data = $this->DBManager->findAllSecure("SELECT * FROM folders");
        return $data;
    }

    public function getUserFolders($id) {
        $data = $this->DBManager->findAllSecure("SELECT * FROM folders WHERE id_user = :id",
            ['id' => $id]);
        return $data;
    }

    public function getFolderById($id) {
        $data = $this->DBManager->findOneSecure("SELECT * FROM folders WHERE id = :id", 
            ['id' => $id]);
        return $data;
    }

    public function folderCheckAdd($data) {
        $result['isFormGood'] = true;
        $folderExist = $this->DBManager->findOneSecure("SELECT * FROM folders WHERE foldername = :foldername",
            ['foldername' => $data['foldername']]);
        if ($folderExist) {
            $result['isFormGood'] = false;
            $result['errors'] = 'Le fichier existe déjà';
        }
        else {
            $result['foldername'] = $data['foldername'];
        }
        return $result;
    }

    public function createFolder($data) {
        $folder['id_user'] = $_SESSION['user_id'];
        $folder['id_folder'] = NULL;
        $folder['foldername'] = $data['foldername'];
        $folder['folderpath'] = 'uploads/' . $_SESSION['user_name'] . '/' . $folder['foldername'];
        $folder['date'] = $this->DBManager->getDatetimeNow();

        $query = $this->DBManager->insert('folders', $folder);

        $folderToUpdate = $this->DBManager->findOneSecure("SELECT * FROM folders WHERE folderpath = :folderpath",
            ['folderpath' => $folder['folderpath']]);
        $newpath = 'uploads/' . $_SESSION['user_name'] . '/' . $folderToUpdate['id'];
        $this->DBManager->findOneSecure("UPDATE folders SET folderpath = :newpath WHERE folderpath = :folderpath",
            ['folderpath' => $folder['folderpath'], 'newpath' => $newpath]);

        mkdir($newpath); 
    }

    public function folderCheckRename($data) {
        $result['isFormGood'] = true;

        if (!isset($data['newname']) || empty($data['newname'])) { // empty field
            $result['isFormGood'] = false;
            $result['errors'] = 'Veuillez saisir le nouveau nom du fichier';
        }
        else {
            $folder = $this->DBManager->findOneSecure("SELECT * FROM folders WHERE foldername = :foldername", 
                ['foldername' => $data['foldername']]);

            // check if file belongs to a folder
            if ($folder['id_folder'] == NULL) {
                $newpath = 'uploads/' . $_SESSION['user_name'] . '/' . $data['newname']; // path by default
            }
            else {
                $folderParent = $folderManager->getFolderById($folder['id_folder']);
                $newpath = $folderParent['folderpath'] . '/' . $data['newname']; // path of file within folder
            }

            // check if newname already exists
            $folderExist = $this->getFolderByUrl($newpath);
            if ($folderExist) {
                $result['isFormGood'] = false;
                $result['errors'] = 'Le fichier existe déjà';
            }
            else {
                $result['folderpath'] = $folder['folderpath'];
                $result['newname'] = $data['newname'];
                $result['newpath'] = $newpath;
            }
        }
        return $result;
    }

    public function renameFolder($data) {
        rename($data['folderpath'], $data['newpath']);
        $query = $this->DBManager->findOneSecure("UPDATE folders
            SET foldername = :newname, folderpath = :newpath
            WHERE folderpath = :currentPath", [
                'newname' => $data['newname'],
                'newpath' => $data['newpath'],
                'currentPath' => $data['folderpath'],
        ]);
        return $query;
    }

}