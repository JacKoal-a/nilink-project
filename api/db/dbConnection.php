<?php

class DB{
    public static function getConnection(){
        $config=$GLOBALS['config'];
        try {
            return new PDO ("mysql:host={$config["db"]["host"]};dbname={$config["db"]["dbname"]};charset=utf8", 
                $config["db"]["username"],
                $config["db"]["password"]
            );
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
