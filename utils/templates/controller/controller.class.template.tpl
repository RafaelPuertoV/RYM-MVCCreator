<?php

namespace {{NAMESPACE}}\Controllers;

use {{NAMESPACE}}\Models\{{CLASS.PREFIX}}{{CLASS.NAME}};
use {{NAMESPACE}}\MVC\Http\HTTPResponse;

class {{CLASS.PREFIX}}{{CLASS.NAME}}Controller
{
    
    public function index($_request)
    {
        if($_request['actionType'] == 'viewForm'){

            $view = file_get_contents(\{{NAMESPACE}}\MVC\Controllers\MVCController::viewsPath().'{{CLASS.PREFIX}}{{CLASS.NAME}}/index.php');
            $containerView = HTTPResponse::renderView($view,array()) ;
            $parameters = array( 
                '{{DATABASE.NAME}}' => DB_NAME , 
                '{{MVC.NAMESPACE}}' => '{{NAMESPACE}} :: {{CLASS.PREFIX}}{{CLASS.NAME}}',
                '{{APP.CONTAINER}}' => $containerView
            );

           
            $responseView = file_get_contents(\{{NAMESPACE}}\MVC\Controllers\MVCController::viewsPath().'Core/base.template.html');
            
            HTTPResponse::View($responseView,$parameters) ;
            exit();
        }

        if(!isset($_request['mvc_page']) || is_numeric($_request['mvc_page']))
        {
            $_request['mvc_page']=1;
        }

        $items = {{CLASS.PREFIX}}{{CLASS.NAME}}::all( array('page' => $_request['mvc_page'] ));
        $response = array(
            "data" => $items,
            'status' => 200
        );
        
        HTTPResponse::json($response) ;
    }

    public function store($_request)
    {
 
        $item = new {{CLASS.PREFIX}}{{CLASS.NAME}}();
        
        {{CONTROLLER.SETITEM.VALUES}}
        
        $item::create();

        $response = array(
            "data" => $item,
            'status' => 201
        );
        HTTPResponse::json($response);
    }

    public function show($_request)
    {

        $item = {{CLASS.PREFIX}}{{CLASS.NAME}}::findBy(array('{{CLASS.PRIMARYKEY}}' => $_request["{{CLASS.PRIMARYKEY}}"]));

        if($_request['actionType'] == 'viewForm'){

            $parameters = array( );
            
            $view = "Hello this is the {{CLASS.PREFIX}}{{CLASS.NAME}}-Edit Page" ;  // file_get_contents(\{{NAMESPACE}}\MVC\Controllers\MVCController::viewsPath().'{{CLASS.PREFIX}}{{CLASS.NAME}}/index.php');
            $containerView = HTTPResponse::renderView($view,$parameters) ;

            $parameters = array( 
                '{{DATABASE.NAME}}' => DB_NAME , 
                '{{MVC.NAMESPACE}}' => '{{NAMESPACE}} :: {{CLASS.PREFIX}}{{CLASS.NAME}}',
                '{{APP.CONTAINER}}' => $containerView
            );

            $responseView = file_get_contents(\{{NAMESPACE}}\MVC\Controllers\MVCController::viewsPath().'Core/base.template.html');
            
            HTTPResponse::View($responseView,$parameters) ;
            exit();
        }
        
        $response = array(
            "data" => $item,
            'status' => 200
        );
        HTTPResponse::json($response);
    }

    public function update( $_request )
    {
        $items = {{CLASS.PREFIX}}{{CLASS.NAME}}::findBy(array('{{CLASS.PRIMARYKEY}}' => $_request["{{CLASS.PRIMARYKEY}}"]));
        if(is_null($items) || count($items)==0 ){       
             $response = array(
                "message" => "Not found",
                'status' => 404
            );
            HTTPResponse::json($response);
            exit();
        }
        $item = items[0];
        {{CONTROLLER.SETITEM.VALUES}}
        $item->update();
        $response = array(
            "data" => $item,
            "message" => "Updated successfully",
            'status' => 200
        );
    }

    public function destroy($_request)
    {
        $items = {{CLASS.PREFIX}}{{CLASS.NAME}}::findBy(array('{{CLASS.PRIMARYKEY}}' => $_request["{{CLASS.PRIMARYKEY}}"]));

        if(is_null($items) || count($items)==0 ){       
             $response = array(
                "message" => "Not found",
                'status' => 404
            );
        }else{
            $items[0]->delete();
            $response = array(
                "message" => "Deleted successfully",
                'status' => 200
            );
        }
       
        HTTPResponse::json($response);
    }
}
