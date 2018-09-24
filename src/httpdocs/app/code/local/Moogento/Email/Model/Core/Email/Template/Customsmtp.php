<?php

class Moogento_Email_Model_Core_Email_Templateattach extends AW_Customsmtp_Model_Email_Template
{
    public function sendMail(AW_Customsmtp_Model_Mail $mailDataModel, $storeId)
    {
        $config = array(
            'port' => Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_PORT, $storeId),
            'auth' => Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_AUTH, $storeId),
            'username' => Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_LOGIN, $storeId),
            'password' => Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_PASSWORD, $storeId),
        );

        $needSSL = Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_SSL, $storeId);
        if (!empty($needSSL)) {
            $config['ssl'] = Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_SSL, $storeId);
        }

        $transport = new Zend_Mail_Transport_Smtp(
            Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_HOST, $storeId), $config
        );
        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host', $storeId));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port', $storeId));

        $mail = $this->getMail();

        $mail->setSubject('=?utf-8?B?' . base64_encode($mailDataModel->getSubject()) . '?=');

        /* Starts from 1.10.1.1 version "TO" holds array values */
        if (!empty($this->_saveRange)) {
            foreach ($this->_saveRange as $range) {
                $mail->addTo($range['email'], '=?utf-8?B?' . base64_encode($range['name']) . '?=');
            }
        } else {
            $mail->addTo(
                $mailDataModel->getToEmail(), '=?utf-8?B?' . base64_encode($mailDataModel->getToName()) . '?='
            );
        }

        if (!array_key_exists('Reply-To', $mail->getHeaders())) {
            $mail->setReplyTo($mailDataModel->getFromEmail(), $mailDataModel->getFromName());
        }

        $mail->setFrom($mailDataModel->getFromEmail(), $mailDataModel->getFromName());

        $text = $mailDataModel->getBody();

        $preparedAttachments = Mage::helper('moogento_email')->prepareAttachments($text);

        $text = $preparedAttachments['text'];

        foreach ($preparedAttachments['attachments'] as $attachmentData) {
            $mail->createAttachment($attachmentData['content'],
                'application/pdf',
                Zend_Mime::DISPOSITION_ATTACHMENT,
                Zend_Mime::ENCODING_BASE64,
                $attachmentData['filename']);
        }

        if ($mailDataModel->getIsPlain()) {
            $mail->setBodyText($text);
        } else {
            $mail->setBodyHTML($text);
        }

        $this->setUseAbsoluteLinks(true);

        try {

            $mail->send($transport); //add $transport object as parameter
            $this->_mail = null;
        } catch (Exception $e) {
            throw($e);
        }
        return true;
    }
}