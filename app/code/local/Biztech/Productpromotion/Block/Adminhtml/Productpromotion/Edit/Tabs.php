<?php

class Biztech_Productpromotion_Block_Adminhtml_Productpromotion_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('productpromotion_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('productpromotion')->__('Promotion Information'));
      
    }

    protected function _beforeToHtml() {
        $store = $this->getRequest()->getParam('store', 0);
        $this->addTab('form_section', array(
            'label' => Mage::helper('productpromotion')->__('Promotion Information'),
            'title' => Mage::helper('productpromotion')->__('Promotion Information'),
            'content' => $this->getLayout()->createBlock('productpromotion/adminhtml_productpromotion_edit_tab_form')->toHtml(),
        ));

        $this->addTab('related_product_section', array(
            'label' => Mage::helper('productpromotion')->__('Promotional Products'),
            'title' => Mage::helper('productpromotion')->__('Promotional Products'),
            'url' => $this->getUrl('*/*/product', array('_current' => true)),
            'class' => 'ajax',
        ));

        return parent::_beforeToHtml();
    }

}

?>
