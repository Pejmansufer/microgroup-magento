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
* File        Flataddressyn.php
* @category   Moogento
* @package    pickPack
* @copyright  Copyright (c) 2015 Moogento <info@moogento.com> / All rights reserved.
* @license    https://www.moogento.com/License.html
*/ 

class Moogento_Pickpack_Model_Flataddressyn
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => Mage::helper('pickpack')->__('No (default)')),
            array('value' => '1', 'label' => Mage::helper('pickpack')->__('Yes, including email')),
            array('value' => '2', 'label' => Mage::helper('pickpack')->__('Yes, *not* including email')),
        );
    }

}
