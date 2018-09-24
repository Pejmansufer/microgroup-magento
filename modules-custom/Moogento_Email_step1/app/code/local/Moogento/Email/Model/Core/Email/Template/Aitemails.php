<?php

class Moogento_Email_Model_Core_Email_Template_Attach extends Aitoc_Aitemails_Model_Rewrite_CoreEmailTemplate
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
            //          $bccEmail = $this->_bcc[0];
            $bccEmail = $this->_bcc;
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
                'headers' => array('Reply-To' => $this->replyTo)
            );

            if ($this->isPlain()) {
                $message['text'] = $text;
            } else {
                if (false === strpos($text, '<html>')) {
                    $text = '<html>' . $text;
                }
                if (false === strpos($text, '</html>')) {
                    $text = $text . '</html>';
                }
                $message['html'] = $text;
            }
            if (isset($variables['tags']) && count($variables['tags'])) {
                $message ['tags'] = $variables['tags'];
            } else {
                $templateId = (string)$this->getId();
                $templates = parent::getDefaultTemplates();
                if (isset($templates[$templateId])) {
                    $message ['tags'] = array(substr($templates[$templateId]['label'], 0, 50));
                } else {
                    if ($this->getTemplateCode()) {
                        $message ['tags'] = array(substr($this->getTemplateCode(), 0, 50));
                    } else {
                        $message ['tags'] = array(substr($templateId, 0, 50));
                    }
                }
            }

            // adding attachments
            $attachmentCollection = Mage::getModel('aitemails/aitattachment')->getTemplateAttachments($this->getId());
            if (count($attachmentCollection) > 0) {
                foreach ($attachmentCollection as $attachment) {
                    if ($attachment->getAttachmentFile()) {
                        $sFileExt = substr($attachment->getAttachmentFile(), strrpos($attachment->getAttachmentFile(), '.'));
                        if ($attachment->getData('store_title')) {
                            $sFileName = $this->normalizeFilename($attachment->getData('store_title')) . $sFileExt;
                        } else {
                            $sFileName = substr($attachment->getAttachmentFile(), 1 + strrpos($attachment->getAttachmentFile(), '/'));
                        }
                        $att = $mail->createAttachment(file_get_contents(Aitoc_Aitemails_Model_Aitattachment::getBasePath() . $attachment->getAttachmentFile()));
                        $att->filename = $sFileName;
                    }
                }
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