<?php

class Application
{

    /**
     * "Start" the application
     */
    public function __construct()
    {
        session_start();

        //route service
        foreach (ROUTES as $url => $command) {
            if($url == $_GET['menu1']){
                $command = explode('@',$command);
                require APP .'controllers/'. $command[0].'.php';

                $controller = new $command[0]();
                echo $controller->{$command[1]}();
            }else{
                echo "404";
            }
        }
    }

}
