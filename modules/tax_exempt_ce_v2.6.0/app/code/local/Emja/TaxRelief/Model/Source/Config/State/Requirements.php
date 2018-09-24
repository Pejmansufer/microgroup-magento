<?php
class Emja_TaxRelief_Model_Source_Config_State_Requirements
{

    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Not required')),
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Required')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Per design'))
        );
    }

}
