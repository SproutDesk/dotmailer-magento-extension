<?php

class Dotdigitalgroup_Email_Block_Adminhtml_Abandoned_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('id');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ddg_automation/abandoned')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id', array(
                'header' => Mage::helper('ddg')->__('ID'),
                'index' => 'id',
                'type' => 'number',
                'escape' => true,
            )
        )->addColumn(
            'quote_id', array(
                'header' => Mage::helper('ddg')->__('Quote ID'),
                'width' => '20px',
                'index' => 'quote_id',
                'type' => 'number',
                'escape' => true,
            )
        )->addColumn(
            'customer_id', array(
                'header' => Mage::helper('ddg')->__('Customer ID'),
                'align' => 'left',
                'width' => '50px',
                'index' => 'customer_id',
                'type' => 'number',
                'escape' => true
            )
        )->addColumn(
            'is_active', array(
                'header' => Mage::helper('ddg')->__('Is Active'),
                'align' => 'left',
                'index' => 'is_active',
                'type' => 'number',
                'escape' => true
            )
        )->addColumn(
            'quote_updated_at', array(
                'header' => Mage::helper('ddg')->__('Quote updated at'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'quote_updated_at',
                'type' => 'datetime'
            )
        )->addColumn(
            'abandoned_cart_number', array(
                'header' => Mage::helper('ddg')->__('Abandoned cart number'),
                'align' => 'left',
                'index' => 'abandoned_cart_number',
                'type' => 'number',
                'escape' => true
            )
        )->addColumn(
            'items_count', array(
                'header' => Mage::helper('ddg')->__('Items count'),
                'align' => 'left',
                'index' => 'items_count',
                'type' => 'number',
                'escape' => true
            )
        )->addColumn(
            'items_ids', array(
                'header' => Mage::helper('ddg')->__('Item ids'),
                'align' => 'left',
                'index' => 'items_ids',
                'type' => 'number',
                'escape' => true
            )
        )->addColumn(
            'created_at', array(
                'header' => Mage::helper('ddg')->__('Created At'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'created_at',
                'type' => 'datetime'
            )

        )->addColumn(
            'updated_at', array(
                'header' => Mage::helper('ddg')->__('Updated at'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'updated_at',
                'type' => 'datetime'
            )
        )->addColumn(
            'store_id', array(
                'header'  => Mage::helper('customer')->__('Store'),
                'align'   => 'center',
                'width'   => '80px',
                'type'    => 'options',
                'options' => Mage::getSingleton('adminhtml/system_store')
                    ->getStoreOptionHash(true),
                'index'   => 'store_id'
            )
        );

        $this->addExportType('*/*/exportCsv', Mage::helper('ddg')->__('CSV'));

        return parent::_prepareColumns();
    }

    /**
     * Get the store.
     *
     * @return Mage_Core_Model_Store
     * @throws Exception
     */
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);

        return Mage::app()->getStore($storeId);
    }

    /**
     * Prepare the grid massaction.
     *
     * @return $this|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('email_contact_id');
        $this->getMassactionBlock()->setFormFieldName('contact');
        $this->getMassactionBlock()->addItem(
            'delete', array(
                'label' => Mage::helper('ddg')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('ddg')->__('Are you sure?'))
        );
        $this->getMassactionBlock()->addItem(
            'resend', array(
                'label' => Mage::helper('ddg')->__('Resend'),
                'url' => $this->getUrl('*/*/massResend'),

            )
        );

        return $this;
    }

    /**
     * Custom callback action for the subscribers/contacts.
     *
     * @param $collection
     * @param $column
     */
    public function filterCallbackContact($collection, $column)
    {
        $field = $column->getFilterIndex() ? $column->getFilterIndex()
            : $column->getIndex();
        $value = $column->getFilter()->getValue();

        if ($value == 'null') {
            $collection->addFieldToFilter($field, array('null' => true));
        } else {
            $collection->addFieldToFilter($field, array('notnull' => true));
        }
    }

    /**
     * Edit the row.
     *
     * @param $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit', array('id' => $row->getEmailContactId())
        );
    }

    /**
     * Grid url.
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

}