<?php

class Moogento_Email_Model_Core_Email_Templateattach extends Ebizmarts_Mandrill_Model_Email_Template
{
    public function send($email, $name = null, array $variables = array())
    {
        $helper = Mage::helper('mandrill');

        //Check if should use Mandrill Transactional Email Service
        if (FALSE === $helper->useTransactionalService()) {
            return parent::send($email, $name, $variables);
        }

        if (!$this->isValidForSend()) {
            Mage::logException(new Exception('This letter cannot be sent.')); // translation is intentionally omitted
            return false;
        }

        $emails = array_values((array)$email);

        if (count($this->_bcc) > 0) {
            $bccEmail = $this->_bcc[0];
        } else {
            $bccEmail = '';
        }

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


        try {

            $message = array(
                'subject' => $this->getProcessedTemplateSubject($variables),
                'from_name' => $this->getSenderName(),
                'from_email' => $this->getSenderEmail(),
                'to_email' => $emails,
                'to_name' => $names,
                'bcc_address' => $bccEmail,
                'headers' => array(
                    'Reply-To' => $this->replyTo
                )
            );

            if ($this->isPlain()) {
                $message['text'] = $text;
            } else {
                $message['html'] = $text;
            }

            $tTags = $this->_getTemplateTags();
            if (!empty($tTags)) {
                $message['tags'] = $tTags;
            }

            if ($attachments = $mail->getAttachments()) {
                $email['attachments'] = $attachments;
            }

            $sent = $mail->sendEmail($message);
            if ($mail->errorCode) {
                return false;
            }
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }

        return true;
    }

}