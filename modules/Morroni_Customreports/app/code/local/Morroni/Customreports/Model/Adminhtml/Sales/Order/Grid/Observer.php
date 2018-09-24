<?php

class Morroni_Customreports_Model_Adminhtml_Sales_Order_Grid_Observer
{
    public function morroni_core_order_grid_actions($observer)
    {

        $block = $observer->getEvent()->getBlock();
        if(get_class($block) =='Mage_Adminhtml_Block_Widget_Grid_Massaction'
           && $block->getRequest()->getControllerName() == 'sales_order')
        {

            $block->addItem('morroni_separater_1', array(
                'label'      => '---------------',
                'url'        => ''
            ));

            $block->addItem('morroni_order_report', array(
                'label'      => 'MG Order Report',
                'url'        => Mage::app()->getStore()->getUrl('mgcustom/index/order'),
            ));
            $block->addItem('morroni_order_item_detail_report', array(
                'label'      => 'MG Order Item Detail Report',
                'url'        => Mage::app()->getStore()->getUrl('mgcustom/index/detail'),
            ));


            $block->addItem('morroni_separater_2', array(
                'label'      => '---------------',
                'url'        => ''
            ));

        }

    }
    
}

