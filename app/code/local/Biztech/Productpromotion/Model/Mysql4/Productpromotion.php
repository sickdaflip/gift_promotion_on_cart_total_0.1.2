<?php

class Biztech_Productpromotion_Model_Mysql4_Productpromotion extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        
        $this->_init('productpromotion/productpromotion', 'productpromotion_id');
    }
}