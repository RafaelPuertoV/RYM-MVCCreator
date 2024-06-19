<?php

namespace {{NAMESPACE}}\Controllers;

use {{NAMESPACE}}\MVC\Http\HTTPResponse;

class {{CLASS.PREFIX}}DefaultController
{
    
    public function index($_request)
    {
        if($_request['actionType'] == 'viewForm'){

            // This is the home page
            // Initiate the home controller
            // and render the home view
            $methodList = \{{NAMESPACE}}\MVC\Controllers\MVCController::getContollerClasses();
            $tbody='';
            $idx= 1;
            foreach ($methodList as $ctllr) {
                $method = str_replace("Controller", "", $ctllr ); 
                $tbody.='<tr><td>'.($idx++)
                    .'</td><td class="catalog-item" >'
                    .$method.'</td>
                        <td><a href="'.\{{NAMESPACE}}\MVC\MVCRequesHandler::$web_root.$method.'/index/" > View </a></td></tr>';
            }
            $parameters = array(
                '{{HOMEPAGE.TBODY}}'=>$tbody
            );

            $containerView = HTTPResponse::renderView(\{{NAMESPACE}}\MVC\Controllers\MVCController::viewsPath()."homepage.php",$parameters) ;

            $parameters = array( 
                '{{DATABASE.NAME}}' => DB_NAME , 
                '{{MVC.NAMESPACE}}' => '{{NAMESPACE}} :: HOME PAGE',
                '{{APP.CONTAINER}}' => $containerView
            );
            $responseView = file_get_contents(\{{NAMESPACE}}\MVC\Controllers\MVCController::viewsPath().'Core/base.template.html');
            HTTPResponse::View($responseView,$parameters) ;
            exit();
        }

        
        
    }
}
