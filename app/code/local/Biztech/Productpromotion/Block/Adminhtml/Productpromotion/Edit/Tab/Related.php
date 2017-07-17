<?php

class Biztech_Productpromotion_Block_Adminhtml_Productpromotion_Edit_Tab_Related extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('relatedGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setDefaultFilter(array('related_product' => 1));
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);
    }

    protected function _addColumnFilterToCollection($column) {
        if ($column->getId() == 'related_product') {

            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection() {
        $product = $this->_getSelectedProducts();

        $typeArray = array('simple', 'configurable');

        $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('sku')
                ->addAttributeToSelect('price')
                ->addAttributeToFilter('type_id', array('in' => $typeArray))
                ->addAttributeToFilter('visibility', array("neq" => 1))
                ->addAttributeToFilter('status', array("eq" => 1))
                ->addStoreFilter($this->getRequest()->getParam('store'))
                ->joinField('position', 'catalog/category_product', 'position', 'product_id=entity_id', 'category_id=' . (int) $this->getRequest()->getParam('id', 0), 'left');

        $this->setCollection($collection);

        if (!Mage::registry('promotion')) {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
        }
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {


        $this->addColumn('related_product', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'related_product[]',
            'onclick' => 'test_product()',
            'values' => $this->_getSelectedProducts(),
            'align' => 'center',
            'index' => 'entity_id',
            'field_name' => 'related_product[]'
        ));


        $this->addColumn('entity_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'sortable' => true,
            'width' => '60px',
            'index' => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'index' => 'name',
            'sortable' => true
        ));
        $this->addColumn('sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'width' => '120px',
            'index' => 'sku',
            'sortable' => true
        ));
        $this->addColumn('price', array(
            'header' => Mage::helper('catalog')->__('Price'),
            'type' => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index' => 'price',
            'sortable' => true
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    protected function _getProduct() {
        return Mage::registry('productpromotion_data');
    }

    protected function _getSelectedProducts() {

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
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
        return $sel_products;
    }

    public function getRelProducts() {

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
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
        return $sel_products;
    }

}

?>
