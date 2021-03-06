<?php 
/** 
* Moogento
* 
* SOFTWARE LICENSE
* 
* This source file is covered by the Moogento End User License Agreement
* that is bundled with this extension in the file License.html
* It is also available online here:
* http://www.moogento.com/License.html
* 
* NOTICE
* 
* If you customize this file please remember that it will be overwrtitten
* with any future upgrade installs. 
* If you'd like to add a feature which is not in this software, get in touch
* at www.moogento.com for a quote.
* 
* ID          pe+sMEDTrtCzNq3pehW9DJ0lnYtgqva4i4Z=
* File        Country.php
* @category   Moogento
* @package    pickPack
* @copyright  Copyright (c) 2014 Moogento <info@moogento.com> / All rights reserved.
* @license    http://www.moogento.com/License.html
*/ 



class Moogento_ShipEasy_Block_Adminhtml_Widget_Grid_Column_Renderer_Country
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $showas = Mage::getStoreConfig('moogento_shipeasy/grid/szy_country_show_type');
        if ($showas == 2) {
            return $row->getData('szy_country');
        } else{
            $block_flag = $this->getLayout()->createBlock('moogento_shipeasy/adminhtml_directory_country_flag');
            $block_flag->setCountryId($row->getData('szy_country'));
            return $block_flag->toHtml();
        }
    }

    public function renderExport(Varien_Object $row)
    {
        return $row->getData('szy_country');
    }
}
