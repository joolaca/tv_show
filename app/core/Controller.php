<?php

/**
 * base controller class
 */
class Controller
{

    protected function __construct()
    {
        //include necessary mondel
        foreach ($this->my_models as $model) {
            require APP .'model/'. $model.'.php';
        }
    }

    /**
     * @param string $file path
     * @param null $variables
     * @return string html
     */
    public function render($file, $variables= null){
        foreach ($variables as $key => $variable) {
            ${$key} = $variable;
        }
        ob_start();
        include(APP . 'views/'.$file.'.html');
        ob_get_contents();
        return ob_get_clean();
    }

}
