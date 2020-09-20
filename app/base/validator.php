<?php

namespace base;

use DB;

include_once 'db.php';

/**
 * Валідатор 
 *
 * @author User
 */

class Validator {
    protected $_errors=[];
    protected $_rules=[]; // ключ поле =>Правило
    protected $_fields=[];
    protected $_data=[];    
    
    public function __construct($data,$rules) {                                
        $this->_rules = $rules;         
        $this->_data = $data;            
        $this->_fields = array_keys($rules);            
        
    }
    
    protected function required($field) {                
        if (empty($this->_data[$field])) {            
            $this->addError($field, 'Field must be sent');
        }          
    }
    
    protected function email($field){        
        if (!preg_match('/^([\w\-.])+@+([\w\-]{2}+.+[a-zA-Z]{2})$/', $this->_data[$field] )) {
            $this->addError($field,'email in wrong format');
        }
    }
            
    protected function unique($data) {
        
        $field=key($data); $table=$data[$field];
                
        $result = DB::getValue("SELECT {$field} "
                                . "FROM {$table} "
                                . "WHERE {$field} = ? ", [$this->_data[$field]]
                               );

        if ($result) {
              $this->addError($field,' Not unique ');            
        }                          
    }
    
    protected function confirm($field) {
        if ($this->data[$field] != $this->_data[$field.'_confirm']){
            $this->addError($field,$field.' do not match '.$field.'_confirm');            
        }
    }
    
    public function addError($field,$error) {
        $this->_errors[$field] = $error;        
    }
    
    public function getErrors(){
        return $this->_errors ;
    }

    public function getError($field) {
        if (isset($this->_errors[$field]))
            return $this->_errors[$field] ;
        
    }
    
    public function ValidateThis() {
        
        foreach ($this->_rules as $field => $rules) {

            foreach ($rules as $rule  ) {
                         
                if (is_array($rule)) 
                {  
                    $param=current($rule);
                    $rule = key($rule);  
                    $fieldrule = [$field=>$param];
                    
                } else $fieldrule  =$field;
        
                if (method_exists($this,$rule)) {

                    if (is_null($this->getError($field))) {                                           
                           
                        $this->$rule($fieldrule); 
                    }

                } 
                else {
                    
                    throw new Exception('Uknown validation rule'.$rule);
                }
            }
        }               
        if ( !empty($this->_errors)) {
            return false ;
        }        
        return true;
    }    

}
