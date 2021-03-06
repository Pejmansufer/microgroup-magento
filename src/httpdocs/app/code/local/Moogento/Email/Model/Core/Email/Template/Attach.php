<?php
if (!class_exists("Moogento_Email_Model_Core_Email_Template_Attach")) {
    $needsExtension = Mage::helper('moogento_email')->needsExtension();

    if ($needsExtension) {
        require_once 'Moogento/Email/Model/Core/Email/Template/' . $needsExtension . '.php';
    } else {
        class Moogento_Email_Model_Core_Email_Template_Attach extends Mage_Core_Model_Email_Template
        {
            public function send($email, $name = null, array $variables = array())
            {
                $emails = array_values((array)$email);
                $names = is_array($name) ? $name : (array)$name;
                $names = array_values($names);
                foreach ($emails as $key => $email) {
                    if (!isset($names[$key])) {
                        $names[$key] = substr($email, 0, strpos($email, '@'));
                    }
                }

                $variables['email'] = reset($emails);
                $variables['name'] = reset($names);

                @ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
                @ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

                $mail = $this->getMail();

                $setReturnPath = Mage::getStoreConfig(self::XML_PATH_SENDING_SET_RETURN_PATH);
                switch ($setReturnPath) {
                    case 1:
                        $returnPathEmail = $this->getSenderEmail();
                        break;
                    case 2:
                        $returnPathEmail = Mage::getStoreConfig(self::XML_PATH_SENDING_RETURN_PATH_EMAIL);
                        break;
                    default:
                        $returnPathEmail = null;
                        break;
                }

                if ($returnPathEmail !== null) {
                    $mailTransport = new Zend_Mail_Transport_Sendmail("-f" . $returnPathEmail);
                    Zend_Mail::setDefaultTransport($mailTransport);
                }

                foreach ($emails as $key => $email) {
                    $mail->addTo($email, '=?utf-8?B?' . base64_encode($names[$key]) . '?=');
                }

                $this->setUseAbsoluteLinks(true);
                $text = $this->getProcessedTemplate($variables, true);

                $preparedAttachments = Mage::helper('moogento_email')->prepareAttachments($text);

                $text = $preparedAttachments['text'];

                foreach ($preparedAttachments['attachments'] as $attachmentData) {
                    $mail->createAttachment($attachmentData['content'],
                        'application/pdf',
                        Zend_Mime::DISPOSITION_ATTACHMENT,
                        Zend_Mime::ENCODING_BASE64,
                        $attachmentData['filename']);
                }

                if ($this->isPlain()) {
                    $mail->setBodyText($text);
                } else {
                    $mail->setBodyHTML($text);
                }

                $mail->setSubject('=?utf-8?B?' . base64_encode($this->getProcessedTemplateSubject($variables)) . '?=');
                $mail->setFrom($this->getSenderEmail(), $this->getSenderName());
                try {
                    if (!$this->isValidForSend()) {
                        Mage::logException(new Exception('This letter cannot be sent.')); // translation is intentionally omitted
                        return false;
                    }
                    $mail->send();
                    $this->_mail = null;
                } catch (Exception $e) {
                    $this->_mail = null;
                    Mage::logException($e);
                    return false;
                }
                return true;
            }
        }
    }
}