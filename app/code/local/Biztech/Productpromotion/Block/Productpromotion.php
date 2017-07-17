<?php

class Biztech_Productpromotion_Block_Productpromotion extends Mage_Core_Block_Template {

    public function __construct() {
        parent::__construct();
    }

    public function _prepareLayout() {
        parent::_prepareLayout();
        return $this;
    }


    public function getProductpromotion() {
        if (!$this->hasData('productpromotion')) {
            $this->setData('productpromotion', Mage::registry('productpromotion'));
        }
        return $this->getData('productpromotion');
    }
    
     public function getCartdifference($price) //difference between promotion price and cart grand total
    {
         $quote = Mage::getModel('checkout/cart')->getQuote();
    
        $quoteSubTotal = $quote->getGrandTotal();
    
        $cartPriceDifference = ($price - $quoteSubTotal);
        return $cartPriceDifference;
    }

}
