<?php

class Emja_TaxRelief_Model_Source_Config_Tax_Allowgroups
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('adminhtml')->__('All Groups')),
            array('value'=>1, 'label'=>Mage::helper('adminhtml')->__('Specific Groups')),
        );
    }
}