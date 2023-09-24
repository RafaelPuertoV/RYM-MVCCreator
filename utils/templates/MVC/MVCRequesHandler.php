<?php

namespace RYMAPP\MVC;



class MVCRequesHandler
{

    public static $url='';
    public static $api=false;
    public static $web_root='';
    public static $requestedController='';
    public static $requestedAction='';
    public static $requestedParams=array();
    public static $publiFolders = array();
    public function __construct()
    {
        $request = isset($_SERVER['PATH_INFO']) ? explode('/', ltrim($_SERVER['PATH_INFO'],'/')) : '/';
        $List  = scandir(\{{NAMESPACE}}\MVC\Controllers\MVCController::publicPath());
        
        foreach ($List as $pth) {
            if ($pth == '.' || $pth == '..') {
                continue;
            }
            if (is_dir(\{{NAMESPACE}}\MVC\Controllers\MVCController::publicPath() . $pth)) {
                self::$publiFolders[$pth] = 'public';
            }
        }

        if ($request == '/')
        {
            self::$requestedController = 'Default';
            self::$url = $request;
        }else{
            // This is not home page
            // Initiate the appropriate controller
            // and render the required view


            self::$api = false;
            if(isset($request[0]) && $request[0]=='api'){
                self::$api = true; 
                self::$url = array();
                $urlParams = array_values($request);
                //echo '<br><br>urlParams:'; var_dump($urlParams);
                for ($i=1; $i < count($urlParams) ; $i++) { 
                    self::$url[]= $urlParams[$i];
                }
            }else{
                self::$url = $request;
            }

            //The first element should be a controller
            self::$requestedController = self::$url[0]; 
            unset(self::$url[0]);

            // If a second part is added in the URI, 
            // it should be a method
            self::$requestedAction = isset(self::$url[1])? self::$url[1] :'';
            unset(self::$url[1]);

            // The remain parts are considered as 
            // arguments of the method
            $urlParams = array_values(self::$url);
            //echo '<br><br>urlParams:'; var_dump($urlParams);

            for ($i=0; $i < count($urlParams) ; $i=$i+2 ) { 
                self::$requestedParams[$urlParams[$i]]= $urlParams[$i+1];
            }
            
            //echo '<br><br>requestedParams:'; var_dump($requestedParams);
            foreach ($_POST as $key => $value) {
                if(!array_key_exists($key , self::$requestedParams)){
                    self::$requestedParams[$key]=$value;
                }
            }
            /*echo '<br><br>Request info: <br>';
            var_dump(array(
                'requestedController'=>self::$requestedController,
                'requestedAction'=>self::$requestedAction,
                'requestedParams'=>self::$requestedParams
            ));
            echo '-------------------------------------------------';
            // Check if controller exists. NB: */
            // You have to do that for the model and the view too
        }
        $search = self::$requestedController.'/'.self::$requestedAction;
        #echo '<br>search['.$search.']<br>';
        $pos = strpos($_SERVER['REQUEST_URI'] , $search, 0 ); // $pos = 7, not 0

        $Urlbase = substr($_SERVER['REQUEST_URI'],0,$pos);
        #echo '<br>'.$_SERVER['REQUEST_URI'].' == ['.$Urlbase.']';
        if($Urlbase == ''){
            self::$web_root = $_SERVER['REQUEST_URI'];    
        }else{
            self::$web_root = substr($_SERVER['REQUEST_URI'],0,$pos);
        }
    }


    public function handle(){

        if (self::$requestedController == 'Default')
        {
            $controller = new \{{NAMESPACE}}\Controllers\{{NAMESPACE}}DefaultController();
            echo  $controller->index(array('actionType' => 'viewForm'));
        }else{
            $controllerName = self::$requestedController.'Controller';
            $controllerClass = "\\RYMAPP\\Controllers\\".$controllerName;

            if (class_exists($controllerClass))
            {

                if (self::$requestedAction == '')
                {
                    self::$requestedAction='index';
                }
                
                if (self::$api)
                {
                    self::$requestedParams['actionType'] = 'API';
                }else{
                    self::$requestedParams['actionType'] = 'viewForm';
                }
                //exit(); 
                $action = self::$requestedAction;
                if( method_exists($controllerClass, $action  )){
                    $controller = new $controllerClass();
                    echo  $controller->$action(self::$requestedParams);
                    exit();
                }elseif(array_key_exists(self::$requestedAction,self::$publiFolders)){
                    $file = \{{NAMESPACE}}\MVC\Controllers\MVCController::publicPath().str_replace('../','', join("/",self::$url) );
                    if(file_exists($file)){
                        echo file_get_contents($file);
                        exit();
                    }else{
                        header('HTTP/1.1 404 Not Found');
                        die('404 - Page not found');     
                    }
                }else{
                    header('HTTP/1.1 404 Not Found');
                    die('404 - Page <strong>'.$controllerClass.'/'.self::$requestedAction.'</strong> not found');
                }

            }else{
                header('HTTP/1.1 404 Not Found');
                die('404 - Page  <strong>'.$controllerClass.'/'.self::$requestedAction.'</strong> not found');
            }
        }
    }
}
