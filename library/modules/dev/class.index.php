<?php

class index extends Codup\main
{

    function index ()
    {

    }

    function action ()
    {
        $this->stack("dev:action");
        $this->db->select(array('translation', array('*'), ''));
        
        $this->view->text = "test";
        $this->view->origin = __class__;
        $this->view->file = __file__;
    }
}
?>
