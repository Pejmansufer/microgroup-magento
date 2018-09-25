<?php

class Mage4Helpdev_Disablefrontend_Model_Observer
{
    public function httpResponseSendBefore(Varien_Event_Observer $observer)
    {
        $response = $observer->getResponse();
        $html = "<body><p>Site is temporarily disabled now.</p></body>";
		
		$response->setBody($html);
    }
}