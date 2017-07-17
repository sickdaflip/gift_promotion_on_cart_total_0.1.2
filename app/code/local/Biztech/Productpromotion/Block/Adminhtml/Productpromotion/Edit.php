<?php

class Biztech_Productpromotion_Block_Adminhtml_Productpromotion_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
  public function __construct() {
    parent::__construct();

    $this->_objectId = 'id';
    $this->_blockGroup = 'productpromotion';
    $this->_controller = 'adminhtml_productpromotion';

    $this->_updateButton('save', 'label', Mage::helper('productpromotion')->__('Save Promotion'));
    $this->_updateButton('delete', 'label', Mage::helper('productpromotion')->__('Delete Promotion'));

    $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
            ), -100);

    $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productpromotion_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'productpromotion_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'productpromotion_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
  }

  public function getHeaderText() {
    if( Mage::registry('productpromotion_data') && Mage::registry('productpromotion_data')->getId() ) {
      return Mage::helper('productpromotion')->__("Edit Promotion '%s'", $this->htmlEscape(Mage::registry('productpromotion_data')->getTitle()));
    } else {
      return Mage::helper('productpromotion')->__('Add Promotion');
    }
  }
  public function getPromotion() {
    if(Mage::registry('promotion'))
      return Mage::registry('promotion');
    else
      return Mage::getModel('productpromotion/productpromotion')->load((int) $this->getRequest()->getParam('id', 0));
  }

  public function getProductsJson() {
    $products = $this->getPromotion()->getProductsPosition();
    if (!empty($products)) {
      return Zend_Json::encode($products);
    }

    return '{}';
  }

  public function isAjax() {
    return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
  }

  public function getJsObjectName(){
    return $this->getId() . 'JsObject';
  }
}