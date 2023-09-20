            
            if($_GET['method']=='{{CLASS.NAME}}' && method_exists('{{NAMESPACE}}\Controllers\{{CLASS.NAME}}Controller', $_GET['action'] )){
                
                $controller = new \{{NAMESPACE}}\Controllers\{{CLASS.NAME}}Controller(); 
                $actionMethod=$_GET['action'];
                $controller->$actionMethod($_GET);
                exit();
            }

