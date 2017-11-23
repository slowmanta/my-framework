<?php
/**
 * Created by PhpStorm.
 * User: Pino
 * Date: 11/20/17
 * Time: 11:01 PM
 */

namespace App\Core;

use App\Core\Router as Router;

class App{

    private $router;

    function __construct()
    {
        $this->router = new Router();

        $this->router->get('/','HomeController@index');

        $this->router->get('/test/{id}',function ($id){
            echo 'this is Test : ' . $id;
        });

        $this->router->any('*',function (){
            echo "<h1>Error 404 Not Found</h1>";
            echo "The page that you have requested could not be found.";
            exit();
        });
    }

    function run(){
        $this->router->run();
    }
}