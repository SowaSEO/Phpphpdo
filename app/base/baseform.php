<?php
namespace base;

include_once 'validator.php';
include_once 'db.php';

/**
* Description of BaseForm
*
* @author User
*/
abstract class BaseForm  {
    
    protected $_errors =[];
    protected $_data;    
    protected $data4sql; // параметри для  SQL   
    protected $set4sql; // стрічка для SQL    
    protected $_validator = null;    
    
    abstract public function getRules() ;
    
    public function validate() {        
        $this->_validator = new Validator($this->_data, $this->getRules());              
        $resvalid = $this->_validator->validateThis();
        if (!$resvalid) {
            $this->_errors = $this->_validator->getErrors();
            return false;
        }        
        return true;       
    }
    /**
     * записуємо вхідні дані  безпечно у властивості класу
     * і формуємо для SQL масив парамертрів , і стрічку SQL
     *
     * @param type $data
     * @return boolean
     */    
    public function load($data){        
        foreach ($data as $propName=>$propValue) { 
            if (property_exists(static::class, $propName)){                                                
                $propValue = $this->getSafeData($propName,$propValue);                                
                $this->$propName = $propValue;                
                $this->_data[$propName] = $propValue;                                                
                $this->data4sql[$propName] = $propValue;                
                $this->set4sql.="`{$propName}`=:{$propName}, ";
            } else {                
                return false;
            }
        }        
        $this->set4sql=substr($this->set4sql, 0, -2);
        return true;
    }
    
    public function getErrors() {        
        return $this->_errors;
    }
        
    public function getSafeData(&$propname, $param) {        
        if ($propname=='password') {
            return md5($param);
        }        
        return htmlspecialchars(strip_tags($param));        
    } 
}





