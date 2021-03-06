<?php 
/** 
* Moogento
* 
* SOFTWARE LICENSE
* 
* This source file is covered by the Moogento End User License Agreement
* that is bundled with this extension in the file License.html
* It is also available online here:
* https://www.moogento.com/License.html
* 
* NOTICE
* 
* If you customize this file please remember that it will be overwrtitten
* with any future upgrade installs. 
* If you'd like to add a feature which is not in this software, get in touch
* at www.moogento.com for a quote.
* 
* ID          pe+sMEDTrtCzNq3pehW9DJ0lnYtgqva4i4Z=
* File        Subtotalvaluealignxpos.php
* @category   Moogento
* @package    pickPack
* @copyright  Copyright (c) 2014 Moogento <info@moogento.com> / All rights reserved.
* @license    https://www.moogento.com/License.html
*/ 

class Moogento_Pickpack_Block_Subtotalvaluealignxpos extends Moogento_Pickpack_Block_Adminhtml_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setStyle('width:40px;')
            ->setName($element->getName() . '[]');
        if ($element->getValue()) {
            $value = $element->getValue();
        } else {
            $value = 520;
        }
        $from = $element->setValue(isset($value) ? $value : null)->getElementHtml();
        return 'Totals value X-Pos: ' . $from . '   ' . Mage::helper('adminhtml')->__('pt');
    }
}
