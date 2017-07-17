<?php

class Biztech_Productpromotion_Model_Productpromotiondata extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productpromotion/productpromotiondata');
    }
}