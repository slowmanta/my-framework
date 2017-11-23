<?php
/**
 * Created by PhpStorm.
 * User: Pino
 * Date: 11/20/17
 * Time: 11:02 PM
 */

namespace App\Core;

class Router
{

    private $routers = [];

    function __construct()
    {
        #code ...
    }

    private function getRequestURL()
    {
        $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        $url = str_replace('/public', '', $url);
        $url = $url === '' || empty($url) ? '/' : $url;
        return $url;
    }

    private function getRequestMethod()
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        return $method;
    }

    public function addRoute($method, $url, $action)
    {
        $this->routers[] = [$method, $url, $action];
    }

    public function get($url, $action)
    {
        $this->addRoute('GET', $url, $action);
    }

    public function post($url, $action)
    {
        $this->addRoute('POST', $url, $action);

    }

    public function any($url, $action)
    {
        $this->addRoute('GET|POST', $url, $action);

    }

    public function map()
    {
        $checkRoute = false;
        $requestURL = $this->getRequestURL();
        $requestMethod = $this->getRequestMethod();
        $routers = $this->routers;
        $params = [];
        foreach ($routers as $route) {
            list($method, $url, $action) = $route;

            if(strpos($method,$requestMethod) === FALSE){
                continue;
            }

            if($url === '*'){
                $checkRoute = true;
            }elseif(strpos($url,'{') === FALSE){
                if(strcmp(strtolower($url),strtolower($requestURL)) === 0){
                    $checkRoute = true;
                }else{
                    continue;
                }
            }elseif(strpos($url,'}') === FALSE){
                continue;
            }else{
                $routeParams = explode('/',$url);
                $requestParams = explode('/',$requestURL);

                if(count($routeParams) != count($requestParams)){
                    continue;
                }

                foreach( $routeParams as $k => $rp){
                    if(preg_match('/^{\w+}$/',$rp)){
                        $params[] = $requestParams[$k];
                    }
                }
                $checkRoute = true;
            }

            if($checkRoute === true){

                if(is_callable($action)){;
                    call_user_func_array($action,$params);
                    return;
                }elseif(is_string($action)) {
                    $this->compieRoute($action,$params);
                    return;
                }

            }else{
                continue;
            }
        }
        return;
    }

    private function compieRoute($action,$params){
        if(count(explode('@',$action)) !== 2){
            die('Router Error !!!');
        }
        list($className,$methodName) = explode('@',$action);

        $classNameSpace = "App\\Controller\\".$className;

        if(class_exists($classNameSpace)){
            $object = new $classNameSpace;
            if(method_exists($classNameSpace,$methodName)){
                call_user_func_array([$object,$methodName],$params);
            }else{
                die('Method '. $methodName. ' Not Found');
            }
        }else{
            die('Class ' . $className . 'Not Found');
        }

    }

    function run()
    {
        $this->map();

    }
}