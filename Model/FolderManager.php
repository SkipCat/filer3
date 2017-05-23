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

    public function getUserFolders($id) {
        $data = $this->DBManager->findAllSecure("SELECT * FROM folders WHERE id_user = :id",
            ['id' => $id]);
        return $data;
    }
}