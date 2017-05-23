<?php

namespace Model;


class FileManager
{
    private $DBManager;
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null)
            self::$instance = new FileManager();
        return self::$instance;
    }

    private function __construct()
    {
        $this->DBManager = DBManager::getInstance();
    }

    public function getFileById($id)
    {
        $id = (int)$id;
        $data = $this->DBManager->findOne("SELECT * FROM files WHERE id = " . $id);
        return $data;
    }

    public function getFileByUrl($file_url)
    {
        $data = $this->DBManager->findOneSecure("SELECT * FROM files WHERE file_url = :file_url",
            ['file_url' => $file_url]);
        return $data;
    }
    public function fileCheckAdd($data){
        $isFormGood = true;
        $errors = array();
        $res = array();
        if(isset($_FILES['addFile']['name']) && !empty($_FILES) && $_FILES['addFile']['name'] !== ""){
            $data['file_name'] = $_FILES['addFile']['name'];
            $data['file_tmp_name'] = $_FILES['addFile']['tmp_name'];
            $res['data'] = $data;
        }
        else{
            $errors['file'] = 'Veillez choisir une image';
            $isFormGood = false;
        }
        $res['isFormGood'] = $isFormGood;
        $res['errors'] = $errors;
        return $res;
    }
    public function addFile($data){
        $file['file_name'] = $data['file_name'];
        $file['file_url'] = 'uploads/'.$_SESSION['user_username'].'/'.$data['file_name'];
        $file['user_id'] = (int)$_SESSION['user_id'];
        $data['date'] = $this->DBManager->getDatetimeNow();
        $this->DBManager->insert('files', $file);
    }





}
