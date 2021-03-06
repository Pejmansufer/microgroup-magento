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
* File        Region.php
* @category   Moogento
* @package    pickPack
* @copyright  Copyright (c) 2014 Moogento <info@moogento.com> / All rights reserved.
* @license    http://www.moogento.com/License.html
*/ 


class Moogento_ShipEasy_Block_Adminhtml_Widget_Grid_Column_Renderer_Region
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $value = parent::render($row);

        if (Mage::getStoreConfigFlag('moogento_shipeasy/grid/region_show_abbreviation')) {
            $country = $row->getCountry();
            $region = Mage::getModel('directory/region')->loadByName($value, $country);
            if ($region->getId()) {
                $value = $region->getCode();
            }
        }

        return $value;
    }
}
