<?php

    class Biztech_Productpromotion_Model_System_Config_Frontend_Displaysettings 
    {
        public function toOptionArray()
        {
            return array(                
                array('value' => 'proceedtocheckout',
                      'label' => Mage::helper('productpromotion')->__('Display Promotions on Checkout Page')),
                array('value' => 'cartpage',
                      'label' => Mage::helper('productpromotion')->__('Display Promotions on Cart Page')),
            );
        }

}
