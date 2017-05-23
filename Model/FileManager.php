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
        return $this->DBManager->findOneSecure("SELECT * FROM files WHERE file_url = :file_url",
            ['file_url' => $file_url]);
    }
    public function getUserFiles(){
        $user_id = (int)$_SESSION['user_id'];
        return $this->DBManager->findAllSecure("SELECT * FROM files WHERE user_id = :user_id ORDER BY date DESC",
            ['user_id' => $user_id]);
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
        $file['date'] = $this->DBManager->getDatetimeNow();
        $this->DBManager->insert('files', $file);
        move_uploaded_file($data['file_tmp_name'],$file['file_url']);
    }
    public function deleteFile($file_url){
        unlink($file_url);
        return $this->DBManager->findOneSecure("DELETE FROM files WHERE file_url = :file_url",
            ['file_url' => $file_url]);
    }
    public function checkRenameFile($data){
        $res['isFormGood'] = true;
        if (!isset($data['newFileName']) || empty($data['newFileName'])) {
            $res['isFormGood'] = false;
            $res['errors'] = 'Veuillez saisir le nouveau nom du fichier';
            return $res;
        }else{
            $currentFileUrl = $data['urlFileToRename'];
            $currentFileName = substr(strrchr($currentFileUrl,'/'),1);
            $ext = $this->getFileExtension($currentFileName);
            $newFileName = $data['newFileName'].$ext;
            $newFileUrl = "uploads/".$_SESSION['user_username']."/".$newFileName ;

            $fileExist = $this->getFileByUrl($newFileUrl);
            if($fileExist){
                $res['isFormGood'] = false;
                $res['errors'] = 'Le fichier existe déjà';
                return $res;
            }else{
                $res['currentFileUrl'] = $currentFileUrl;
                $res['newFileUrl'] = $newFileUrl;
                $res['newFileName'] = $newFileName;
                return $res;
            }
        }
    }
    public function renameFile($data){
        $currentFileUrl = $data['currentFileUrl'];
        $newFileUrl = $data['newFileUrl'];
        $newFileName = $data['newFileName'];
        rename($currentFileUrl, $newFileUrl);
        return $this->DBManager->findOneSecure('UPDATE files SET file_name =:newFileName, file_url =:newFileUrl WHERE file_url =:currentFileUrl',
        [
            'newFileName' => $newFileName,
            'newFileUrl' => $newFileUrl,
            'currentFileUrl' => $currentFileUrl,
        ]);
    }
    public function getFileExtension($file_name){
        return strrchr($file_name, '.');
    }
    public function extensionAccept($ext){
        $extensions = array('.jpg', '.jpeg', '.txt','.png','.pdf', '.mp3', '.mp4');
        return in_array($ext, $extensions);
    }






}
