<?php
/** @var Mage_Sales_Model_Mysql4_Setup $installer */
$installer = $this;
$installer->startSetup();

/* @var $eavConfig Mage_Eav_Model_Config */
$eavConfig = Mage::getSingleton('eav/config');

$installer->addAttribute('customer', 'tax_exempt_number', array(
    'label'        => 'Tax Exempt Number',
    'visible'      => 1,
    'required'     => 0,
    'position'     => 1,
    'required'     => 0,
    'sort_order'   => 1000
));

//$installer->addAttribute('customer', 'tax_exempt_state', array(
//    'label'        => 'Tax Exempt State',
//    'visible'      => 1,
//    'required'     => 0,
//    'position'     => 1,
//    'required'     => 0,
//    'sort_order'   => 1001
//));

$forms = array(
    'adminhtml_customer', 'customer_account_edit'
);

$attribute = $eavConfig->getAttribute('customer', 'tax_exempt_number');
$attribute->setData('used_in_forms', $forms);
$attribute->save();

//$attribute = $eavConfig->getAttribute('customer', 'tax_exempt_state');
//$attribute->setData('used_in_forms', $forms);
//$attribute->save();

$installer->endSetup();