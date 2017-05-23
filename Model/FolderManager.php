<?php

namespace Model;

class FolderManager {

    public function getUserFolders($id) {
        $data = $this->DBManager->findAllSecure("SELECT * FROM folders WHERE id_user = :id",
            ['id' => $id]);
        return $data;
    }
}