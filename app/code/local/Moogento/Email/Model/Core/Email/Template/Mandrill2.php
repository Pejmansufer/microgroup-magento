<?php

class Moogento_Email_Model_Core_Email_Templateattach extends Ebizmarts_Mandrill_Model_Email_Template
{
    public function send($email, $name = null, array $variables = array())
    {
        $storeId = Mage::app()->getStore()->getId();
        if (!Mage::getStoreConfig(Ebizmarts_Mandrill_Model_System_Config::ENABLE, $storeId)) {
            return parent::send($email, $name, $variables);
        }
        if (!$this->isValidForSend()) {
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

        // Get message
        $this->setUseAbsoluteLinks(true);
        $variables['email'] = reset($emails);
        $variables['name'] = reset($names);
        $message = $this->getProcessedTemplate($variables, true);

        $email = array('subject' => $this->getProcessedTemplateSubject($variables), 'to' => array());

        $mail = $this->getMail();

        $preparedAttachments = Mage::helper('moogento_email')->prepareAttachments($message);

        $message = $preparedAttachments['text'];

        foreach ($preparedAttachments['attachments'] as $attachmentData) {
            $mail->createAttachment($attachmentData['content'],
                'application/pdf',
                Zend_Mime::DISPOSITION_ATTACHMENT,
                Zend_Mime::ENCODING_BASE64,
                $attachmentData['filename']);
        }

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
        foreach ($mail->getBcc() as $bcc) {
            $email['to'][] = array(
                'email' => $bcc,
                'type' => 'bcc'
            );
        }

        $email['from_name'] = $this->getSenderName();
        $email['from_email'] = $this->getSenderEmail();
        $email['headers'] = $mail->getHeaders();

        if (isset($variables['tags']) && count($variables['tags'])) {
            $email ['tags'] = $variables['tags'];
        }

        if (isset($variables['tags']) && count($variables['tags'])) {
            $email ['tags'] = $variables['tags'];
        } else {
            $templateId = (string)$this->getId();
            $templates = parent::getDefaultTemplates();
            if (isset($templates[$templateId])) {
                $email ['tags'] = array(substr($templates[$templateId]['label'], 0, 50));
            } else {
                if ($this->getTemplateCode()) {
                    $email ['tags'] = array(substr($this->getTemplateCode(), 0, 50));
                } else {
                    $email ['tags'] = array(substr($templateId, 0, 50));
                }
            }
        }

        if ($attachments = $mail->getAttachments()) {
            $email['attachments'] = $attachments;
        }
        if ($this->isPlain())
            $email['text'] = $message;
        else {
            $email['html'] = $message;
        }

        try {
            $result = $mail->messages->send($email);
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
        return true;

    }
}