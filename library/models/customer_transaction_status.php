<?php
namespace models;
use Codup;
class customer_transaction_status extends model  
{
    public $id;
    public $status;
    public function getPrimary() 
    {
        return "id";
    }
    public function getPrimaryValue() 
    {
        return $this->id;
    }
}
