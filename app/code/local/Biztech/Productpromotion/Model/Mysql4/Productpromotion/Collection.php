<?php

class Biztech_Productpromotion_Model_Mysql4_Productpromotion_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productpromotion/productpromotion');
    }
}