<?php

class Biztech_Productpromotion_Block_Adminhtml_Productpromotion_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('productpromotion_form', array('legend' => Mage::helper('productpromotion')->__('Promotion Information')));
        $store = Mage::registry('productpromotion_data')->getData('store_id');
        

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('productpromotion')->__('Promotion Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));
        $fieldset->addField('promotion_price', 'text', array(
            'label' => Mage::helper('productpromotion')->__('Promotion Price'),
            'class' => 'validate-greater-than-zero',
            'required' => true,
            'name' => 'promotion_price',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('productpromotion')->__('Promotion Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('productpromotion')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('productpromotion')->__('Disabled'),
                ),
            ),
        ));



        $fieldset->addField('store_id', 'hidden', array(
            'label' => Mage::helper('productpromotion')->__('Store Id'),
            'required' => false,
            'name' => 'store_id',
            'value' => $store,
        ));

        if (Mage::getSingleton('adminhtml/session')->getProductpromotionData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getProductpromotionData());
            Mage::getSingleton('adminhtml/session')->setProductpromotionData(null);
        } elseif (Mage::registry('productpromotion_data')) {
            Mage::registry('productpromotion_data')->setData('store_id', $store);
            $form->setValues(Mage::registry('productpromotion_data')->getData());
        }

        return parent::_prepareForm();
    }

}
