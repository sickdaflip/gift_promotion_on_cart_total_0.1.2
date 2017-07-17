<?php
    /**
    * Magento
    *
    * NOTICE OF LICENSE
    *
    * This source file is subject to the Open Software License (OSL 3.0)
    * that is bundled with this package in the file LICENSE.txt.
    * It is also available through the world-wide-web at this URL:
    * http://opensource.org/licenses/osl-3.0.php
    * If you did not receive a copy of the license and are unable to
    * obtain it through the world-wide-web, please send an email
    * to license@magentocommerce.com so we can send you a copy immediately.
    *
    * DISCLAIMER
    *
    * Do not edit or add to this file if you wish to upgrade Magento to newer
    * versions in the future. If you wish to customize Magento for your
    * needs please refer to http://www.magentocommerce.com for more information.
    *
    * @category    Mage
    * @package     Mage_Checkout
    * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
    * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
    */

    /**
    * Shopping cart model
    *
    * @category    Mage
    * @package     Mage_Checkout
    * @author      Magento Core Team <core@magentocommerce.com>
    */
    require_once 'Mage/Checkout/Model/Cart.php';
    class Biztech_Productpromotion_Model_Cart extends Mage_Checkout_Model_Cart
    {

        public function updateItems($data)
        {
            Mage::dispatchEvent('checkout_cart_update_items_before', array('cart'=>$this, 'info'=>$data));

            /* @var $messageFactory Mage_Core_Model_Message */
            $messageFactory = Mage::getSingleton('core/message');
            $session = $this->getCheckoutSession();
            $qtyRecalculatedFlag = false;
            foreach ($data as $itemId => $itemInfo) {
                $item = $this->getQuote()->getItemById($itemId);
                if (!$item) {
                    continue;
                }
                
                
                 $quote = Mage::getModel('checkout/cart')->getQuote();
                 $promotionGift = $quote->getPromotionGift();
              
                if($item->getProduct()->getId()==$promotionGift) {
                    $quote->setPromotionGift(NULL);
                    $quote->setPromotionAvailedId(NULL);
                    $quote->setPromotionAvailed(false);
                    $quote->save();
                }
               

                if (!empty($itemInfo['remove']) || (isset($itemInfo['qty']) && $itemInfo['qty']=='0')) {
                   
                    if($item->getProduct()->getId()== $promotionGift){
                        $quote->setPromotionGift(NULL);
                        $quote->setPromotionAvailedId(NULL);
                        $quote->setPromotionAvailed(false);
                        $quote->save();
                    }
                  
                    $this->removeItem($itemId);
                    continue;
                }

                $qty = isset($itemInfo['qty']) ? (float) $itemInfo['qty'] : false;
                if ($qty > 0) {
                    $item->setQty($qty);

                    $itemInQuote = $this->getQuote()->getItemById($item->getId());

                    if (!$itemInQuote && $item->getHasError()) {
                        Mage::throwException($item->getMessage());
                    }

                    if (isset($itemInfo['before_suggest_qty']) && ($itemInfo['before_suggest_qty'] != $qty)) {
                        $qtyRecalculatedFlag = true;
                        $message = $messageFactory->notice(Mage::helper('checkout')->__('Quantity was recalculated from %d to %d', $itemInfo['before_suggest_qty'], $qty));
                        $session->addQuoteItemMessage($item->getId(), $message);
                    }
                }
            }

            if ($qtyRecalculatedFlag) {
                $session->addNotice(
                    Mage::helper('checkout')->__('Some products quantities were recalculated because of quantity increment mismatch')
                );
            }

            Mage::dispatchEvent('checkout_cart_update_items_after', array('cart'=>$this, 'info'=>$data));
            return $this;
        }

        /**
        * Remove item from cart
        *
        * @param   int $itemId
        * @return  Mage_Checkout_Model_Cart
        */
        public function removeItem($itemId)
        {
            $productDetails = Mage::getModel('sales/quote_item')->getCollection()
            ->addFieldToFilter('quote_id', $this->getQuote()->getId())
            ->addFieldToFilter('item_id', $itemId)
            ->addFieldToSelect('product_id')
            ->getData();

            $product_id     = $productDetails[0]['product_id'];
            
            $quote = Mage::getModel('checkout/cart')->getQuote();
            $promotionGift = $quote->getPromotionGift();
            
            
            if($product_id == $promotionGift){
               $quote->setPromotionGift(NULL);
               $quote->setPromotionAvailedId(NULL);
               $quote->setPromotionAvailed(false);
               $quote->save();
            }
            
            $this->getQuote()->removeItem($itemId);
            return $this;
        }
    }
