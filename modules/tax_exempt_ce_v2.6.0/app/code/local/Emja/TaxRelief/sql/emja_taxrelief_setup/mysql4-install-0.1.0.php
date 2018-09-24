<?php

$installer = $this;

$installer->startSetup();

$installer->addAttribute('quote_payment', 'tax_relief_code', array('is_user_defined' => 1));
$installer->addAttribute('quote_payment', 'tax_relief_state', array('is_user_defined' => 1));

$installer->addAttribute('order_payment', 'tax_relief_code',  array('is_user_defined' => 1));
$installer->addAttribute('order_payment', 'tax_relief_state', array('is_user_defined' => 1));

$installer->endSetup();