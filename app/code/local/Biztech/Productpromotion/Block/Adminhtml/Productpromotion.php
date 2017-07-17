<?php
class Biztech_Productpromotion_Block_Adminhtml_Productpromotion extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_productpromotion';
    $this->_blockGroup = 'productpromotion';
    $this->_headerText = Mage::helper('productpromotion')->__('Gift Promotion Manager');
    $this->_addButtonLabel = Mage::helper('productpromotion')->__('Add Promotion');
    /*$flag = Mage::helper('productpromotion')->isEnable();
    if($flag){
        parent::__construct();
    }
    else{
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productpromotion')->__('Product Promotion extension is not enabled. Please enable it from System > Configuration.'));
        $this->_removeButton('add');
        
    }*/
   
    parent::__construct();
    }
}