<?php

class Biztech_Productpromotion_Model_Observer extends Mage_Core_Model_Abstract {

    public function modifyPrice(Varien_Event_Observer $observer) {
        $quote = $observer->getEvent()->getQuote();
        $quoteid = $quote->getId();

        $cartTotal = 0;

        $promotionGift = $quote->getPromotionGift();
        $promotionAvailed = $quote->getPromotionAvailed();
        $promotionAvailedId = $quote->getPromotionAvailedId();
        $item = '';
        if ($quoteid) {
            foreach ($quote->getAllItems() as $cartItem) {
                if ($cartItem->getProduct()->getId() == $promotionGift) {
                    $item = $cartItem;

                    if ($cartItem->getQty() > 1)
                        $cartTotal += (Mage::helper('tax')->getPrice($cartItem->getProduct(), $cartItem->getProduct()->getFinalPrice()) *
                                ($cartItem->getQty() - 1));
                } else {
                    $cartTotal += (Mage::helper('tax')->getPrice($cartItem->getProduct(), $cartItem->getProduct()->getFinalPrice()) * $cartItem->getQty());
                }
            }

            $promotionId = $promotionAvailedId;
            $store_id = Mage::app()->getStore()->getStoreId();

            $promotionCollection = Mage::getModel('productpromotion/productpromotiondata')->getCollection()
                    ->addFieldToSelect("promotion_price")
                    ->addFieldToFilter('productpromotion_id', array('eq' => $promotionId))
                    ->addFieldToFilter('store_id', $store_id)
                    ->load();

            $promotionPrice = $promotionCollection->getData();

            $quoteSubTotal = $quote->getGrandTotal();
            $promotion_price = '';
            if(count($promotionPrice)){
             $promotion_price = $promotionPrice[0]['promotion_price']; 
            }

            if ($quoteSubTotal >= $promotion_price) {
                if ($item != null) {

                    $item = ($item->getParentItem() ? $item->getParentItem() : $item);
                    $qty = $item->getQty();

                    $discountAmountWithoutTax = $item->getProduct()->getFinalPrice();
                    $discountAmount = Mage::helper('tax')->getPrice($item->getProduct(), $discountAmountWithoutTax);
                    if ($discountAmount > 0) {
                        $total = $quote->getGrandTotal();
                        $baseTotal = $quote->getBaseGrandTotal();

                        $quote->setSubtotal(0);
                        $quote->setBaseSubtotal(0);

                        $quote->setSubtotalWithDiscount(0);
                        $quote->setBaseSubtotalWithDiscount(0);

                        $quote->setGrandTotal(0);
                        $quote->setBaseGrandTotal(0);

                        $canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');

                        foreach ($quote->getAllAddresses() as $address) {
                            $address->setSubtotal(0);
                            $address->setBaseSubtotal(0);

                            $address->setGrandTotal(0);
                            $address->setBaseGrandTotal(0);

                            $address->collectTotals();

                            $quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
                            $quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());

                            $quote->setSubtotalWithDiscount(
                                    (float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
                            );

                            $quote->setBaseSubtotalWithDiscount(
                                    (float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
                            );

                            $quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
                            $quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());
                            $quote->save();


                            $quote->setGrandTotal($quote->getGrandTotal() - $discountAmount)
                                    ->setBaseGrandTotal($quote->getBaseGrandTotal() - $discountAmount)
                                    ->setSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
                                    ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
                                    ->save();

                            if ($address->getAddressType() == $canAddItems) {
                                $address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount() - $discountAmount);
                                $address->setGrandTotal((float) $address->getGrandTotal() - $discountAmount);
                                $address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount() - $discountAmount);
                                $address->setBaseGrandTotal((float) $address->getBaseGrandTotal() - $discountAmount);

                               if ($address->getDiscountAmount()) {
                                    $address->setDiscountAmount(($address->getDiscountAmount() - $discountAmount));
                                    $address->setDiscountDescription($address->getDiscountDescription() . ', Promotional Gift');
                                    $address->setBaseDiscountAmount(($address->getBaseDiscountAmount() - $discountAmount));
                                } else {
                                    $address->setDiscountAmount(-($discountAmount));
                                    $address->setDiscountDescription('Promotional Gift');
                                    $address->setBaseDiscountAmount(-($discountAmount));
                                }

                                $address->setTaxAmount($address->getTaxAmount() - ($item->getTaxAmount() / $qty));
                                $address->setBaseTaxAmount($address->getBaseTaxAmount() - ($item->getBaseTaxAmount() / $qty));

                                $address->save();
                                $item->setDiscountAmount($discountAmount);
                                $item->setBaseDiscountAmount($discountAmount);
                                $quote->setPromotionAvailed(true);
                            }
                        }
                    }
                }
            } else {
                $quote->setPromotionAvailed(false);
            }
        }
    }

    public function checkKey($observer) {
        $key = Mage::getStoreConfig('biztech_productpromotion/activation/key');
        Mage::helper('productpromotion')->checkKey($key);
    }

}

?>
