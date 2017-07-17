<?php

class Biztech_Productpromotion_Adminhtml_ProductpromotionController extends Mage_Adminhtml_Controller_action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('productpromotion/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Gift Promotion Manager'), Mage::helper('adminhtml')->__('Gift Promotion Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $store = $this->getRequest()->getParam('store', 0);

        $model = Mage::getModel('productpromotion/productpromotion')->load($id);
        $model_promotion = Mage::getModel('productpromotion/productpromotiondata')->getCollection()->addFieldToFilter('store_id', $store)
                ->addFieldToFilter('productpromotion_id', $id)
                ->getData();


        if (count($model_promotion) == 0) {
            $model_promotion = Mage::getModel('productpromotion/productpromotiondata')->getCollection()->addFieldToFilter('store_id', 0)
                    ->addFieldToFilter('productpromotion_id', $model->getProductpromotionId())
                    ->getData();
        }

        $text_data = $model_promotion[0];
        $model = $model->setTitle($text_data['title'])
                ->setPromotionPrice($text_data['promotion_price'])
                ->setStatus($text_data['status'])
                ->setStoreId($text_data['store_id']);



        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (!empty($data)) {

                try {
                    $model->setData($data);
                    $model_promotion->setData($data);
                } catch (Exception $ex) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setFormData($data);
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
                }
            }

            Mage::register('productpromotion_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('productpromotion/items');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Gift Promotion Manager'), Mage::helper('adminhtml')->__('Gift Promotion Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Promotion News'), Mage::helper('adminhtml')->__('Promotion News'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('productpromotion/adminhtml_productpromotion_edit'))
                    ->_addLeft($this->getLayout()->createBlock('productpromotion/adminhtml_productpromotion_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productpromotion')->__('Promotion does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {

        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

            $model = Mage::getModel('productpromotion/productpromotion');
            $model_promotion = Mage::getModel('productpromotion/productpromotiondata');

            try {
                if (!$this->getRequest()->getParam('id')) {

                    if (!empty($data['links']['related_product'])) {
                        $related_product = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['related_product']);
                        $product_ids = implode(",", array_unique($related_product));
                    }
                    if (count($related_product) < 1) {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productpromotion')->__('Unable to add Promotion. Add minimum one product.'));
                        $this->_redirect('*/*/edit', array('id' => $productpromotion_id, 'store' => $data['store_id']));
                        return;
                    }

                    $model->setId();
                    $model->save();
                    $productpromotion_id = $model->getId();

                    $model_promotion->setData($data)->setProductpromotionId($productpromotion_id)->setProductId($product_ids)->setStoreId(0);
                    $model_promotion->save();

                    foreach (Mage::app()->getWebsites() as $website) {
                        foreach ($website->getGroups() as $group) {
                            $stores = $group->getStores();
                            foreach ($stores as $store) {
                                $model_promotion->setData($data)->setProductpromotionId($productpromotion_id)->setProductId($product_ids)->setStoreId($store->getId());
                                $model_promotion->save();
                            }
                        }
                    }
                } else {

                    $productpromotion_id = $this->getRequest()->getParam('id');

                    if ($data['store_id'] == 0) {

                        $model_data = Mage::getModel('productpromotion/productpromotiondata')->getCollection()->addFieldToFilter('productpromotion_id', $productpromotion_id)->addFieldToFilter('store_id', 0)->getData();


                        if (isset($data['links'])) {
                            $related_product = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['related_product']);
                        }
                        $product_ids = implode(",", array_unique($related_product));
                        if (count($related_product) < 1) {
                            $product_ids = $model_data[0]['product_id'];
                        }


                        $model_promotion->setData($data)->setId($model_data[0]['id'])->setProductId($product_ids)->setStoreId(0)->setProductpromotionId($model_data[0]['productpromotion_id']);
                        $model_promotion->save();


                        foreach (Mage::app()->getWebsites() as $website) {
                            foreach ($website->getGroups() as $group) {
                                $stores = $group->getStores();
                                foreach ($stores as $store) {
                                    $model_data = '';
                                    $model_data = Mage::getModel('productpromotion/productpromotiondata')->getCollection()->addFieldToFilter('store_id', $store->getId())->addFieldToFilter('productpromotion_id', $productpromotion_id)->getData();

                                    $model_promotion->setData($data)->setProductId($product_ids)->setId($model_data[0]['id'])->setStoreId($store->getId())->setProductpromotionId($model_data[0]['productpromotion_id']);
                                    $model_promotion->save();
                                }
                            }
                        }
                    } else {

                        $model_data = '';
                        $model_data = $model_promotion->getCollection()->addFieldToFilter('productpromotion_id', $productpromotion_id)->addFieldToFilter('store_id', $data['store_id'])->getData();

                        if (isset($data['links'])) {
                            $related_product = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['related_product']);
                        }
                        $product_ids = implode(",", array_unique($related_product));
                        if (count($related_product) < 1) {
                            $product_ids = $model_data[0]['product_id'];
                        }

                        $model_promotion->setData($data)->setProductId($product_ids)->setId($model_data[0]['id'])->setStoreId($data['store_id'])->setProductpromotionId($model_data[0]['productpromotion_id']);
                        $model_promotion->save();
                    }

                   $related_product_all = explode(',', $product_ids);
                   if (empty($data['links']['related_product']) && count($related_product_all) < 1) {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productpromotion')->__('Unable to update Promotion. Minimum one product required.'));
                        $this->_redirect('*/*/edit', array('id' => $productpromotion_id, 'store' => $data['store_id']));
                        return;
                    }
                }



                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('productpromotion')->__('Promotion was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $productpromotion_id, 'store' => $data['store_id']));
                    return;
                }

                $this->_redirect('*/*/', array('store' => $data['store_id']));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store' => $data['store_id']));
                return;
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productpromotion')->__('Unable to find Promotion to save.'));
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {

                $productpromotion = Mage::getModel('productpromotion/productpromotion')->load($this->getRequest()->getParam('id'));

                $productpromotion->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Promotion was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $productpromotionIds = $this->getRequest()->getParam('productpromotion');
        if (!is_array($productpromotionIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Promotion(s)'));
        } else {
            try {
                foreach ($productpromotionIds as $productpromotionId) {
                    $productpromotion = Mage::getModel('productpromotion/productpromotion')->load($productpromotionId);
                    $productpromotion->delete();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($productpromotionIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {

        $store_id = $this->getRequest()->getParam('store', 0);

        $productpromotionIds = $this->getRequest()->getParam('productpromotion');
        if (!is_array($productpromotionIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Promotion(s)'));
        } else {
            try {
                foreach ($productpromotionIds as $productpromotionId) {

                    $model_promotion = Mage::getModel('productpromotion/productpromotiondata');
                    $promotion_data = Mage::getModel('productpromotion/productpromotiondata')->getCollection()->addFieldToFilter('productpromotion_id', $productpromotionId);

                    if ($store_id == 0) {
                        $promotion = $promotion_data->addFieldToFilter('store_id', 0)->getData();
                        $model_promotion->setId($promotion[0]['id'])->setStoreId(0)
                                ->setStatus($this->getRequest()->getParam('status'))
                                ->setIsMassupdate(true)
                                ->save();


                        foreach (Mage::app()->getWebsites() as $website) {
                            foreach ($website->getGroups() as $group) {
                                $stores = $group->getStores();
                                foreach ($stores as $store) {
                                    $promotion_data = '';
                                    $promotion_data = Mage::getModel('productpromotion/productpromotiondata')->getCollection()->addFieldToFilter('productpromotion_id', $productpromotionId)->addFieldToFilter('store_id', $store->getId())->getData();

                                    $model_promotion->setId($promotion_data[0]['id'])->setStoreId($store->getId())
                                            ->setStatus($this->getRequest()->getParam('status'))
                                            ->setIsMassupdate(true)
                                            ->save();
                                }
                            }
                        }
                    } else {
                        $promotion = $promotion_data->addFieldToFilter('store_id', $store_id)->getData();
                        $model_promotion->setId($promotion[0]['id'])->setStoreId($store_id)
                                ->setStatus($this->getRequest()->getParam('status'))
                                ->setIsMassupdate(true)
                                ->save();
                    }
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($productpromotionIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _initPromotion($getRootInstead = false) {
        $promotionId = (int) $this->getRequest()->getParam('id', false);
        $promotion = Mage::getModel('productpromotion/productpromotion');

        if ($promotionId) {
            $promotion->load($promotionId);
        }


        Mage::register('promotion', $promotion);
        Mage::register('current_promotion', $promotion);
        return $promotion;
    }

    public function gridAction() {

        if (!$category = $this->_initPromotion(true)) {
            return;
        }


        $this->loadLayout();
        $products = $this->getRequest()->getPost('related_product');
        $promotion_products = Mage::getModel('productpromotion/productpromotiondata')->getCollection()
                ->addFieldToFilter('productpromotion_id', (int) $this->getRequest()->getParam('id'))
                ->addFieldToFilter('store_id', (int) $this->getRequest()->getParam('store'))
                ->getFirstItem()
                ->getData();

        $sel_products = explode(",", $promotion_products['product_id']);

        if (!is_null($products)) {
            $sel_products = array_merge($products, $sel_products);
        }

        $this->getLayout()->getBlock('related.grid')
                ->setRelatedProduct($sel_products);
        $this->renderLayout();
    }

    public function productAction() {

        $this->loadLayout();
        $this->getLayout()->getBlock('related.grid')
                ->setRelatedProduct($this->getRequest()->getPost('related_product', null));
        $this->renderLayout();
    }

}
