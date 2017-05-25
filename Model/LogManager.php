<?php

namespace Model;

class LogManager {

    private $DBManager;
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null)
            self::$instance = new LogManager();
        return self::$instance;
    }
    private function __construct() {
        $this->DBManager = DBManager::getInstance();
    }

    public function giveMeDate() {
        $date = date('d-m-Y'); 
        $hour = date('H:i'); 
        return $date . ' ' . $hour;
    }

    public function writeLogUser($file, $text) {
        $date = $this->giveMeDate();
        $fileLog = fopen('logs/' . $file, 'a'); // 'a' -> single writing, file created if doesn't exist
        $logInfo = $date . ' -> ' . $_SESSION['user_name'] .  ' => ' . $text . "\r\n";

        fwrite($fileLog, $logInfo); // writes in the current opened file
        fclose($fileLog); // closes the current opened file
    }

    public function writeLog($file, $text) {
        $date = $this->giveMeDate();
        $fileLog = fopen('logs/' . $file, 'a'); 
        $logInfo = $date . ' -> ' . $text . "\r\n";

        fwrite($fileLog, $logInfo);
        fclose($fileLog);	
    }
}