<?php

namespace Model;

use Model\FileModel;

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
            $result['errors'] = 'Le dossier existe déjà';
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
            $folder = $this->DBManager->findOneSecure("SELECT * FROM folders WHERE id = :id", 
                ['id' => $data['folder_id']]);

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
                $result['errors'] = 'Le dossier existe déjà';
            }
            else {
                $result['folder_id'] = $data['folder_id'];
                $result['newname'] = $data['newname'];
                $result['newpath'] = $newpath;
            }
        }
        return $result;
    }

    public function renameFolder($data) {
        $query = $this->DBManager->findOneSecure("UPDATE folders
            SET foldername = :newname
            WHERE id = :id", [
                'newname' => $data['newname'],
                'id' => $data['folder_id'],
        ]);
        return $query;
    }

    public function deleteFolder($data) {
        $folder = $this->getFolderById($data['folder_id']);
        $this->deleteFolderRecursive($folder['folderpath'], $data['folder_id']);
    }

    public function deleteFolderRecursive($dirpath, $dir_id) {
        if (is_dir($dirpath)) {
            $objects = scandir($dirpath);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    $path = $dirpath . '/' . $object;
                    if (filetype($path) == 'dir') { // or is_dir()
                        $folder = $this->getFolderByUrl($path);
                        $this->deleteFolderRecursive($path, $folder['id']); // recursivity
                    }
                    else {
                        $fileManager = FileManager::getInstance();
                        $file = $fileManager->getFileByUrl($path);
                        $fileManager->deleteFile($file['id']);
                    }
                }
            }
            reset($objects); // set internal pointer of array 'objects' to its first element
            rmdir($dirpath); // delete folder in local
            $this->DBManager->findOneSecure("DELETE FROM folders WHERE id = :id",
                ['id' => $dir_id]); // delete folder in db
        }
    }

    public function moveFolder($data) {
        $folder = $this->getFolderById($data['folder_id']);
        $this->moveFolderRecursive($data['folder_id'], $folder['folder_parent_id']);
    }

    public function moveFolderRecursive($folderId, $parentId) {
        // get folder to move and future parent
        $dir = $this->getFolderById($folderId);
        $dirpath = $dir['folderpath'];
        $newParent = $this->getFolderById($parentId); // get future parent

        // move folder
        $newpath = $dirpath . '/' . $folderId;
        $this->DBManager->findOneSecure("UPDATE folders
            SET id_folder = :parent_id, folderpath = :newpath
            WHERE id = :id", ['parent_id' => $parentId, 'newpath' => $newpath]
        );
        rename($dirpath, $newpath); // move folder in local

        // then move files and folders inside
        if (is_dir($dirpath)) {
            $objects = scandir($dirpath);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    $path = $dirpath . '/' . $object;
                    if (filetype($path) == 'dir') { // or is_dir()
                        $folderId = basename($path);
                        $folderParentId = basename($dirpath);
                        $this->moveFolderRecursive($folderId, $folderParentId); // recursivity
                    }
                    else {
                        $fileManager = FileManager::getInstance();
                        $file = $fileManager->getFileByUrl($path);
                        var_dump($file);
                        $currentFolder = $this->getFolderById($file['id_folder']);

                        $data['filepath'] = $file['filepath'];
                        $data['id_folder'] = $file['id_folder']; // SAME! file belongs to same direct folder
                        $data['newpath'] = $currentFolder['folderpath'] . '/' . $file['id']; // get modified direct folderpath
                        $fileManager->moveFile($data);
                    }
                }
            }
            reset($objects); // set internal pointer of array 'objects' to its first element
        }
    }


}