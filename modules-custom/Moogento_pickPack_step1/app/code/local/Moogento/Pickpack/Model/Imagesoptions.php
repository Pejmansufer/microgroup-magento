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
* File        Imagesoptions.php
* @category   Moogento
* @package    pickPack
* @copyright  Copyright (c) 2014 Moogento <info@moogento.com> / All rights reserved.
* @license    https://www.moogento.com/License.html
*/ 

class Moogento_Pickpack_Model_Imagesoptions
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'thumbnail', 'label' => Mage::helper('pickpack')->__('Thumbnail')),
            array('value' => 'small_image', 'label' => Mage::helper('pickpack')->__('Small Image')),
            array('value' => 'image', 'label' => Mage::helper('pickpack')->__('Main Image')),
            array('value' => 'gallery', 'label' => Mage::helper('pickpack')->__('Gallery Images (Max 2)'))
        );
    }

}
