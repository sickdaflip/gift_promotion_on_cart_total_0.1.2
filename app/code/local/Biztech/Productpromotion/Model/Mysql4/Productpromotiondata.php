<?php

class Biztech_Productpromotion_Model_Mysql4_Productpromotiondata extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
       
        $this->_init('productpromotion/productpromotiondata', 'id');
    }
}