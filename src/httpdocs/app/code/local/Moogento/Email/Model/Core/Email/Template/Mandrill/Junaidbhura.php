<?php

class Moogento_Email_Model_Core_Email_Template_Attach extends Mage_Core_Model_Email_Template
{
    private $_bcc_array = array();

    /**
     * Override parent's addBcc() function
     *
     * @param string|array     The BCC email(s)
     * @return object     Current object
     */
    public function addBcc( $bcc ) {
        // Check if extension is enabled and API key is entered
        if ( Mage::getStoreConfig( 'mandrill/mandrill/active' ) && Mage::getStoreConfig( 'mandrill/mandrill/api_key' ) != '' ) {

            if ( is_array( $bcc ) ) {
                foreach ( $bcc as $email ) {
                    $this->_bcc_array[] = $email;
                }
            }
            elseif ( $bcc ) {
                $this->_bcc_array[] = $bcc;
            }
            return $this;

        }
        else {
            // Extension is not enabled, use parent's function
            return parent::addBcc( $bcc );
        }

    }

    public function send($email, $name = null, array $variables = array())
    {
        try {
            if (Mage::getStoreConfig('mandrill/mandrill/active') && Mage::getStoreConfig('mandrill/mandrill/api_key') != '') {

                if (!$this->isValidForSend()) {
                    Mage::logException(new Exception('This letter cannot be sent.'));
                    return false;
                }

                // Set up names and email addresses
                $emails = array_values((array)$email);
                $names = is_array($name) ? $name : (array)$name;
                $names = array_values($names);
                foreach ($emails as $key => $email) {
                    if (!isset($names[$key])) {
                        $names[$key] = substr($email, 0, strpos($email, '@'));
                    }
                }

                // Get message
                $this->setUseAbsoluteLinks(true);
                $variables['email'] = reset($emails);
                $variables['name'] = reset($names);
                $text = $message_pre = $this->getProcessedTemplate($variables, true);
                // Prepare email
                $email = array('subject' => $this->getProcessedTemplateSubject($variables), 'to' => array());
                $mail = $this->getMail();

                $this->setUseAbsoluteLinks(true);
                $text = $this->getProcessedTemplate($variables, true);

                $preparedAttachments = Mage::helper('moogento_email')->prepareAttachments($text);

                $message = $preparedAttachments['text'];

                foreach ($preparedAttachments['attachments'] as $attachmentData) {
                    $mail->createAttachment($attachmentData['content'],
                        'application/pdf',
                        Zend_Mime::DISPOSITION_ATTACHMENT,
                        Zend_Mime::ENCODING_BASE64,
                        $attachmentData['filename']);
                }

                // Initialize Mandrill
                $mandrill = new Junaidbhura_Mandrill(Mage::getStoreConfig('mandrill/mandrill/api_key'));

                for ($i = 0; $i < count($emails); $i++) {
                    if (isset($names[$i])) {
                        $email['to'][] = array(
                            'email' => $emails[$i],
                            'name' => $names[$i]
                        );
                    } else {
                        $email['to'][] = array(
                            'email' => $emails[$i],
                            'name' => ''
                        );
                    }
                }

                for ($i = 0; $i < count($this->_bcc_array); $i++) {
                    $email['to'][] = array(
                        'email' => $this->_bcc_array[$i],
                        'name' => ''
                    );
                }

                if (Mage::getStoreConfig('mandrill/mandrill/from_name') != '')
                    $email['from_name'] = Mage::getStoreConfig('mandrill/mandrill/from_name');
                else
                    $email['from_name'] = $this->getSenderName();

                if (Mage::getStoreConfig('mandrill/mandrill/from_email') != '')
                    $email['from_email'] = Mage::getStoreConfig('mandrill/mandrill/from_email');
                else
                    $email['from_email'] = $this->getSenderEmail();

                if ($attachments = $mail->getAttachments()) {
                    $email['attachments'] = $attachments;
                }
                if ($this->isPlain())
                    $email['text'] = $message;
                else {
                    $email['html'] = $message;
                }

                // Send the email!
                try {
                    $result = $mandrill->messages->send($email);
                } catch (Exception $e) {
                    // Oops, some error in sending the email!
                    Mage::logException($e);
                    return false;
                }

                // Woo hoo! Email sent!
                return true;

            } else {
                // Extension is not enabled, use parent's function
                return parent::send($email, $name, $variables);
            }

        } catch (Exception $e) {
            Mage::logException($e);
            return parent::send($email, $name, $variables);
        }
    }
}