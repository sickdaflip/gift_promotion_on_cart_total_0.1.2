<?php

class Biztech_Productpromotion_IndexController extends Mage_Core_Controller_Front_Action {

    public function setGiftAction() {
        try {

            $promotionGift = $this->getRequest()->getParam('productId'); //get gift product id to remove its price from cart.
            $promotionAvailedId = $this->getRequest()->getParam('promotionId'); //used to check whether promotion is once used.


            $this->setPromotion($promotionGift, $promotionAvailedId);

            if ($this->getRequest()->getParam('cartPage')) {
                echo true;
            }

            if ($this->getRequest()->getParam('checkoutPage')) {
                
                $id = $this->getRequest()->getParam('productId'); // set product id
                $qty = '1'; // set product qty
                $_product = Mage::getModel('catalog/product')->load($id);
                $cart = Mage::getModel('checkout/cart');
                $cart->init();
                $cart->addProduct($_product, array('qty' => $qty));
                $cart->save();
                Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

                $result['redirectUrl'] = $this->getRequest()->getParam('redirectUrl');               
                $result = $this->getResponse()->setBody(json_encode($result));
                
                return $result;
            }
        } catch (Exception $e) {
            return;
        }
    }

    protected function setPromotion($promotionGift, $promotionAvailedId) {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $quote->setPromotionGift($promotionGift);
        $quote->setPromotionAvailedId($promotionAvailedId);
        $quote->save();
        return;
    }

    public function addAction() {

        $cart = Mage::getModel('checkout/cart');
        $params = $this->getRequest()->getParams();
        $response = array();
        try {

            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = Mage::getModel('catalog/product')->load($params['product']);
            $related = $this->getRequest()->getParam('related_product');

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $promotionGift = $params['product'];  //get gift product id to remove its price from cart.
            $promotionAvailedId = $params['promotionId']; //used to check whether promotion is once used.

            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $quote->setPromotionGift($promotionGift);
            $quote->setPromotionAvailedId($promotionAvailedId);
            $quote->setPromotionAvailed(true);
            $quote->save();
            
            $cart->save();
            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
            
            return true;
        } catch (Exception $e) {
            return;
        }
    }

    public function optionsAction() {
        $productId = $this->getRequest()->getParam('product_id');
        // Prepare helper and params
        $viewHelper = Mage::helper('catalog/product_view');

        $params = new Varien_Object();
        $params->setCategoryId(false);
        $params->setSpecifyOptions(false);

        // Render page
        try {
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch (Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                Mage::logException($e);
                $this->_forward('noRoute');
            }
        }
    }

}
?>

