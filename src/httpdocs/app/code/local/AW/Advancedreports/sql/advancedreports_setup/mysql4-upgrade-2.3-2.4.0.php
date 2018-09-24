<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Advancedreports
 * @version    2.7.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

/* @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this->startSetup();
/* Remove all aggregated data */
/* @var $helper AW_Advancedreports_Helper_Data */
$helper = Mage::helper('advancedreports');
$helper->getAggregator()->cleanCache();
$installer->getConnection()->changeColumn($this->getTable('advancedreports/aggregation'), 'expired', 'expired', 'DATE DEFAULT NULL');
$installer->endSetup();