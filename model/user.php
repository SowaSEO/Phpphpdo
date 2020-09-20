<?php

include_once 'app/base/baseform.php';

use base\BaseForm;

class User extends BaseForm{     
    
    protected $_tableName = "users";
      
    public $id;
    public $first_name;
    public $last_name;
    public $login;
    public $password;
                  
    public function getRules() {

        return [
                'first_name'=>['required'],
                'last_name'=>['required'],
                'login'=>['required',['unique'=>'users']],
                'password'=>['required']
        ];
    }        
    
    function read_one(){             
        return DB::getRow("SELECT * FROM {$this->_tableName}  WHERE `id` = ?", [$this->id]);
    }
    
    Public function read_all() {                
        $datares = DB::getRows("SELECT * FROM {$this->_tableName} ");
        return $datares;
    }
    
    function add(){               
        $sql = "INSERT INTO {$this->_tableName} SET ".$this->set4sql;        
                 
        if( DB::run($sql,$this->data4sql )){
            
            $this->id = DB::lastInsertId();
            return true;
        }
        return false;                 
    }
        
    function update(){       
        $sql = "UPDATE {$this->_tableName} SET ".$this->set4sql." WHERE id = :id";        
  
        $this->data4sql["id"] = $this->id; 
        
        if( DB::run($sql,$this->data4sql )->rowCount()){
            return true;
        }
        return false;         
    }
    
    function delete(){        
        $query = "DELETE FROM " . $this->_tableName . " WHERE id = ?";

        if( DB::run($query,[$this->id] )->rowCount()){
            return true;
        }
        return false;           
    }  
}
?>
