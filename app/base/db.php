<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'mservis');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_CHAR', 'utf8'
        . '');

class DB
{
    protected static $instance = null;

    public static function instance(){
        if (self::$instance === null)
        {
            $opt  = array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            );
            $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHAR;
            self::$instance = new PDO($dsn, DB_USER, DB_PASS, $opt);
        }
        return self::$instance;
    }      
  
    public static function query($stmt)  {
       return self::instance()->query($stmt);
    }    
      
    public static function getRow($query, $args = [])  
    {
        return self::run($query, $args)->fetch();
    }

    public static function getRows($query, $args = []) {
        return self::run($query, $args)->fetchAll();
    }
    
    public static function getColumn($query, $args = []) {
        return self::run($query, $args)->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function sql($query, $args = []){
        self::run($query, $args);
    }
                  
    public static function getValue($query, $args = []) {
        $result = self::getRow($query, $args);
        if (!empty($result)) {
            $result = array_shift($result);
        }
        return $result;
    }
      
    public static function run($sql, $args = []) {
        if (!$args){
            return self::instance()->query($sql);
        }
        $stmt = self::instance()->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
    
    public static function lastinsertId()  {
        return self::instance()->lastInsertId();
    }    
    
}