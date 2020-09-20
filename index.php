<?php

use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Response;

include_once 'model/user.php';

$container = new FactoryDefault();
$app = new Micro($container);
$user = new user(); 

$app->get( '/api/users', function () use ($app,$user) {      
        $response = new Response();
        $res = $user->read_all();  
        if ($res === false) {
            $response->setJsonContent(
                [
                    'status' => 'NOT-FOUND'
                ]
            );
        } else {
            $response->setJsonContent(
                [
                    'status' => 'FOUND',
                    'data'   => [$res]
                ]
            );
        }
        return $response;                
    }
);

$app->get('/api/users/{id:[0-9]+}', 
    function ($id) use ($app,$user) {        
        $user->id = $id;             
        $response = new Response();    
        $res = $user->read_one();        
        if ($res === false) {           
            $response->setStatusCode(404, 'Not Found');
            $response->setJsonContent(
                [
                    'status' => 'NOT-FOUND'
                ]
            );
        } else {
            $response->setJsonContent(
                [
                    'status' => 'FOUND',
                    'data'   => [$res]
                ]
            );
        }
        return $response;
    }
);

$app->post( '/api/users', 
    function () use ($app,$user) {                
        
        $response = new Response();
        
        if ( $user->load($app->request->getPost()) and $user->validate() ) {
                                        
            if ($user->add() === true) {

                $response->setStatusCode(201, 'Created');

                $response->setJsonContent(
                [
                    'status' => 'OK',
                    'data'   => $user->id,
                ]
                );
                return $response;
            }                                                                                                                                                             
         }         
            
        $response->setStatusCode(409, 'Conflict');       
        $response->setJsonContent(
            [
                'status'   => 'ERROR',         
                'data'   => $user->getErrors(),
            ]
        );
        return $response;
    }
);

$app->put('/api/users/{id:[0-9]+}',          
    function ($id) use ($app,$user) {        
        $response = new Response();        
        $user->id= $id;        
        if ($user->load((array)$app->request->getJsonRawBody()) 
                and $user->validate())
        {            
            if ($user->update() === true) {                
                $response->setStatusCode(201, 'Created');
                $response->setJsonContent(
                [
                    'status' => 'OK',
                    'data'   => $user,
                ]
                );
                return $response;
            }                     
            
        }        
            $response->setStatusCode(409, 'Conflict');                              
            $response->setJsonContent(
            [
                'status'   => 'ERROR',                    
                'data'   => $user->getErrors(),
            ]
        );
        return $response;
    }
);

$app->delete( '/api/users/{id:[0-9]+}',
    function ($id) use ($app,$user) {               
        $user->id= $id;
        $response = new Response();                                
        if ( $user->delete() === true) {            
            $response->setJsonContent(
                [
                    'status' => 'OK'
                ]
            );
        } else {                        
            $response->setStatusCode(409, 'Conflict');            
            $response->setJsonContent(
                [
                    'status'   => 'ERROR',                    
                ]                    
            );
        }
        return $response;
    }
);

$app->handle( 
          $_SERVER["REQUEST_URI"] 
);