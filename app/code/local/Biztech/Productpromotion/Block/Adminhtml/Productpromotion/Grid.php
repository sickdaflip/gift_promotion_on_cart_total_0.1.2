    <?php

class Biztech_Productpromotion_Block_Adminhtml_Productpromotion_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productpromotionGrid');
        $this->setDefaultSort('productpromotion_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        
        $store = $this->getRequest()->getParam('store',0);
        $collection = Mage::getModel('productpromotion/productpromotion')->getCollection();
       
        $collection->getSelect()->joinLeft('productpromotion_data',
                'main_table.productpromotion_id = productpromotion_data.productpromotion_id AND productpromotion_data.store_id='.$store,
                array('title','promotion_price','store_id','status'));  
            
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {


        $this->addColumn('productpromotion_id', array(
            'header' => Mage::helper('productpromotion')->__('Promotion ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'productpromotion_id',
            'filter_index' => 'main_table.productpromotion_id',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('productpromotion')->__('Promotion Title'),
            'align' => 'left',
            'index' => 'title',
        ));

        $this->addColumn('promotion_price', array(
            'header' => Mage::helper('productpromotion')->__('Promotion Price'),
            'align' => 'left',
            'type' => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index' => 'promotion_price',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('productpromotion')->__('Promotion Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('productpromotion')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('productpromotion')->__('Edit'),
                    'url' => array('base' => '*/*/edit','params' => array('store' => $this->getRequest()->getParam('store',0))),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

 

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('productpromotion_id');
        
        $this->getMassactionBlock()->setFormFieldName('productpromotion');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('productpromotion')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('productpromotion')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('productpromotion/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('productpromotion')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('productpromotion')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'store' => $this->getRequest()->getParam('store',0)));
    }

}
