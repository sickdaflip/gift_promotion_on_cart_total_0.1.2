<?php

class Biztech_Productpromotion_Block_Adminhtml_Productpromotion_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {

      $form = new Varien_Data_Form(array(
          'id' => 'edit_form',
          'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
          'method' => 'post',
          'enctype' => 'multipart/form-data'
       )
      );
      
      $form->addField('in_category_products', 'hidden', array(
                    'name'      => 'category_products',
                    'value'     => "456",
                )
        );
        
      $form->addField('selected_products1', 'hidden', array(
                    'name'      => 'selected_products[]',
                    'value'     => "129",
                )
        );

      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
}