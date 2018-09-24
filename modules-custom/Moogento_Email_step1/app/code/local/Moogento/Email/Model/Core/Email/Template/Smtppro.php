<?php

class Moogento_Email_Model_Core_Email_Template_Attach extends Mage_Core_Model_Email_Template
{
    public function send($email, $name = null, array $variables = array())
    {
        try {
            Mage::log('SMTPPro is enabled, sending email in Aschroder_SMTPPro_Model_Email_Template');
            if (!$this->isValidForSend()) {
                Mage::log('SMTPPro: Email not valid for sending - check template, and smtp enabled/disabled setting');
                Mage::logException(new Exception('This letter cannot be sent.')); // translation is intentionally omitted
                return false;
            }

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

            $mail = $this->getMail();
            $smtp_helper = Mage::helper('smtppro');
            if (method_exists($smtp_helper, 'getDevelopmentMode')) {
                $dev = $smtp_helper->getDevelopmentMode();

                if ($dev == "contact") {

                    $email = Mage::getStoreConfig('contacts/email/recipient_email', $this->getDesignConfig()->getStore());
                    Mage::log("Development mode set to send all emails to contact form recipient: " . $email);

                } elseif ($dev == "supress") {

                    Mage::log("Development mode set to supress all emails.");
                    # we bail out, but report success
                    return true;
                }
            }

            // In Magento core they set the Return-Path here, for the sendmail command.
            // we assume our outbound SMTP server (or Gmail) will set that.

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

            if (method_exists($smtp_helper, 'isReplyToStoreEmail')) {
                if ($smtp_helper->isReplyToStoreEmail() && !array_key_exists('Reply-To', $mail->getHeaders())) {

                    // Patch for Zend upgrade
                    // Later versions of Zend have a method for this, and disallow direct header setting...
                    if (method_exists($mail, "setReplyTo")) {
                        $mail->setReplyTo($this->getSenderEmail(), $this->getSenderName());
                    } else {
                        $mail->addHeader('Reply-To', $this->getSenderEmail());
                    }
                    Mage::log('ReplyToStoreEmail is enabled, just set Reply-To header: ' . $this->getSenderEmail());
                }

            }

            $transport = Mage::helper('smtppro')->getTransport($this->getDesignConfig()->getStore());

            try {

                Mage::log('About to send email');
                $mail->send($transport); // Zend_Mail warning..
                Mage::log('Finished sending email');

                // Record one email for each receipient
                foreach ($emails as $key => $email) {
                    $smtp_helper = Mage::helper('smtppro');
                    // Mage::dispatchEvent('smtppro_email_after_send', array(
                    if (method_exists($smtp_helper, 'logEmailSent'))
                        Mage::helper('smtppro')->logEmailSent($email, $this->getTemplateId(), $this->getProcessedTemplateSubject($variables), $text, !$this->isPlain());
                    else
                        if (method_exists($smtp_helper, 'log'))
                            Mage::helper('smtppro')->log($email, $this->getTemplateId(), $this->getProcessedTemplateSubject($variables), $text, !$this->isPlain());
                    //Mage::helper('smtppro')->logEmailSent($email,$this->getTemplateId(),$this->getProcessedTemplateSubject($variables),$text,!$this->isPlain());
                }

                $this->_mail = null;
            } catch (Exception $e) {
                Mage::logException($e);
                return false;
            }

            return true;
        } catch (Exception $e) {
            try {
                $_helper = Mage::helper('smtppro');
                // If it's not enabled, just return the parent result.
                if (!$_helper->isEnabled()) {
                    $_helper->log('SMTP Pro is not enabled, fall back to parent class');
                    return parent::send($email, $name, $variables);
                }


                // As per parent class - except addition of before and after send events

                if (!$this->isValidForSend()) {
                    $_helper->log('Email is not valid for sending, this is a core error that often means there\'s a problem with your email templates.');
                    Mage::logException(new Exception('This letter cannot be sent.')); // translation is intentionally omitted
                    return false;
                }

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

                ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
                ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

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

                    $transport = new Varien_Object();
                    Mage::dispatchEvent('aschroder_smtppro_template_before_send', array(
                        'mail' => $mail,
                        'template' => $this,
                        'variables' => $variables,
                        'transport' => $transport
                    ));

                    if ($transport->getTransport()) { // if set by an observer, use it
                        $mail->send($transport->getTransport());
                    } else {
                        $mail->send();
                    }

                    foreach ($emails as $key => $email) {
                        $smtp_helper = Mage::helper('smtppro');
                        // Mage::dispatchEvent('smtppro_email_after_send', array(
                        if (method_exists($smtp_helper, 'logEmailSent'))
                            Mage::helper('smtppro')->logEmailSent($email, $this->getTemplateId(), $this->getProcessedTemplateSubject($variables), $text, !$this->isPlain());
                        else
                            if (method_exists($smtp_helper, 'log'))
                                Mage::helper('smtppro')->log($email, $this->getTemplateId(), $this->getProcessedTemplateSubject($variables), $text, !$this->isPlain());
                        //Mage::helper('smtppro')->logEmailSent($email,$this->getTemplateId(),$this->getProcessedTemplateSubject($variables),$text,!$this->isPlain());
                    }

                    $this->_mail = null;
                } catch (Exception $e) {
                    $this->_mail = null;
                    Mage::logException($e);
                    return false;
                }

                return true;
            } catch (Exception $e) {

                Mage::logException($e);
                return false;
            }
        }
    }
}