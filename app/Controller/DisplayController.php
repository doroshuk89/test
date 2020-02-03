<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controller;

/**
 * Description of DisplayController
 *
 * @author root
 */
class DisplayController extends MainController {
    
    //Основной блок с данными для отображения
    protected $mainbar;
    protected $lang;


    public function __construct($container) { 
            parent::__construct($container);  
            $this->title = 'WifiCisco | ';
            $this->lang = $this->getLang();
           
    }
    
    public function getLang () {
            $data = require_once $_SERVER['DOCUMENT_ROOT'].'/lang/'.$_SESSION['lang'].'.php';        
        return $data;
    }

    protected function display() {       
            echo $this->view->render('data.php', [   
                                                            'mainbar' => $this->mainbar,
                                                            'text' => 'Main Template', 
                                                            'langTempl' => $this->lang
                                                        ]);   
        } 
        
    protected function JsonResponse () {
            
        }
}