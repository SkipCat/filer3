<?php

namespace Model;


class UserManager
{
    private $DBManager;
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null)
            self::$instance = new UserManager();
        return self::$instance;
    }

    private function __construct()
    {
        $this->DBManager = DBManager::getInstance();
    }

    public function getUserById($id)
    {
        $id = (int)$id;
        $data = $this->DBManager->findOne("SELECT * FROM users WHERE id = " . $id);
        return $data;
    }

    public function getUserByUsername($username)
    {
        $data = $this->DBManager->findOneSecure("SELECT * FROM users WHERE username = :username",
            ['username' => $username]);
        return $data;
    }

    public function userCheckRegister($data)
    {
        $isFormGood = true;
        $errors = array();
        $res = array();
        if (!isset($data['username']) || !$this->usernameValid($data['username'])) {
            $errors['username'] = 'Veuillez saisir un pseudo de 6 caractères minimum';
            $isFormGood = false;
        }
        $data2 = $this->getUserByUsername($data['username']);
        if($data2 !== false){
            $errors['username'] = 'Le pseudo existe déjà';
            $isFormGood = false;
        }
        if(!isset($data['password']) || !$this->passwordValid($data['password'])){
            $errors['password'] = "Veiller saisir un mot de passe valide ".'<br>'."Minimum : 8 caractères avec au moins une lettre majuscule et un nombre";
            $isFormGood = false;
        }
        if($this->passwordValid($data['password']) && $data['password'] !== $data['verifpassword']){
            $errors['password'] = "Les deux mot de passe ne sont pas identiques";
            $isFormGood = false;
        }
        if (!isset($data['firstname']) || strlen($data['firstname']) < 2) {
            $errors['firstname'] = 'Veuillez saisir un nom de 2 caractères minimum';
            $isFormGood = false;
        }
        if (!isset($data['lastname']) || strlen($data['lastname']) < 2) {
            $errors['lastname'] = 'Veuillez saisir un prénom de 2 caractères minimum';
            $isFormGood = false;
        }
        $res['isFormGood'] = $isFormGood;
        $res['errors'] = $errors;
        $res['data'] = $data;
        return $res;
    }
    public function userRegister($data)
    {
        $user['username'] = $data['username'];
        $user['password'] = $this->userHash($data['password']);
        $user['firstname'] = ucwords($data['firstname']);
        $user['lastname'] = mb_strtoupper($data['lastname']);
        $user['date'] = $this->DBManager->getDatetimeNow();
        $this->DBManager->insert('users',$user);
        mkdir('uploads/'.$data['username']);
    }


    public function userCheckLogin($data)
    {
        $isFormGood = true;
        $user = $this->getUserByUsername($data['username']);
        if (empty($data['username']) OR empty($data['password'])) {
            $isFormGood = false;
        }
        if (!password_verify($data['password'], $user['password'])) {
            $isFormGood = false;
        }
        return $isFormGood;
    }


    public function userLogin($username)
    {
        $data = $this->getUserByUsername($username);
        if ($data === false)
            return false;
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['user_username'] = $data['pseudo'];
        return true;
    }
    private function usernameValid($username)
    {
        return preg_match('`^([a-zA-Z0-9-_]{6,20})$`', $username);
    }

    private function passwordValid($password)
    {
        return preg_match('`^([a-zA-Z0-9-_]{8,20})$`', $password);
    }

    private function userHash($pass)
    {
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        return $hash;
    }




}
