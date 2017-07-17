<?php

class Biztech_Productpromotion_Model_System_Config_Enabledisable{

    public function toOptionArray()
    {
        $options = array(
            array('value' => 0, 'label'=>Mage::helper('productpromotion')->__('No')),
        );
        $websites = Mage::helper('productpromotion')->getAllWebsites();
        if(!empty($websites)){
        	$options[] = array('value' => 1, 'label'=>Mage::helper('productpromotion')->__('Yes'));
        }
        return $options;
    }

}