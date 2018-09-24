<?php
class Moogento_Email_Helper_Data extends Mage_Core_Helper_Abstract
{
    const ATTACH_INVOICE_MARKER = 'attach_invoice';
    const ATTACH_PACKINGSHEET_MARKER = 'attach_packingsheet';

    public function isInstalled($moduleName)
    {
        return Mage::getConfig()->getModuleConfig($moduleName)->is('active', 'true');
    }

    public function needsExtension()
    {
        //6
        if ($this->isInstalled('Junaidbhura_Mandrill')) {
            return 'Mandrill/Junaidbhura';
        }
        //4
        if ($this->isInstalled('Aitoc_Aitemails')) {
            return 'Aitemails';
        }

        //3
        if ($this->isInstalled('AW_Customsmtp')) {
            return 'Customsmtp';
        }

        //2
        if ($this->isInstalled('Ebizmarts_Mandrill')) {
            $storeId = Mage::app()->getStore()->getId();
            if ((Mage::helper('core')->isModuleEnabled('mandrill')
                && Mage::getStoreConfig(Ebizmarts_Mandrill_Model_System_Config::ENABLE, $storeId))
                || (Mage::helper('core')->isModuleEnabled('Ebizmarts_Mandrill')
                && Mage::getStoreConfig(Ebizmarts_Mandrill_Model_System_Config::ENABLE, $storeId))
            ) {
                $mandrill_version = (int)Mage::getConfig()->getNode()->modules->Ebizmarts_Mandrill->version;
                if ($mandrill_version < 2) {
                    return 'Mandrill';
                } else {
                    return 'Mandrill2';
                }
            }
        }

        //5
        if ($this->isInstalled('Aschroder_Email')
            && Mage::getStoreConfig('system/aschroder_email/option') != "disabled") {
            return 'Aschroder';
        }

        if ($this->isInstalled('Aschroder_SMTPPro')
            && (Mage::getStoreConfig('system/smtppro/option') != "disabled"
            || Mage::helper('core')->isModuleEnabled('smtppro'))
            ) {
            return 'Smtppro';
        }

        return false;
    }

    public function prepareAttachments($emailText)
    {
        $result = array(
            'text' => $emailText,
            'attachments' => array(),
        );

        //{{attach_invoice({{var order.increment_id}})}}
        $pattern = '/{{' . self::ATTACH_INVOICE_MARKER . '\((.*?)\)}}/im';
        $orderInvoiceIds = array();
        $orderInvoiceIdsMatches = array();

        try {
            if (preg_match_all($pattern, $emailText, $orderInvoiceIdsMatches)) {
                foreach ($orderInvoiceIdsMatches[1] as $id) {
                    $orderInvoiceIds[] = trim($id);
                }
                array_unique($orderInvoiceIds);
                if (count($orderInvoiceIds) > 0) {
                    $collection = Mage::getResourceModel('sales/order_collection')
                        ->addAttributeToSelect('entity_id')
                        ->addAttributeToFilter('increment_id', array('in' => $orderInvoiceIds));
                    $orderInvoiceIds = array();
                    foreach ($collection as $order) {
                        $orderInvoiceIds[] = $order->getId();
                    }

                    if (count($orderInvoiceIds)) {
                        $pdf = Mage::getModel('pickpack/sales_order_pdf_invoices_default')->getPdfDefault($orderInvoiceIds, 'order', 'invoice');
                        $pdfName = Mage::getStoreConfig('pickpack_options/file_name/invoice_pdf_name');
                        $result['attachments'][] = array(
                            'content' => $pdf->render(),
                            'filename' => $pdfName . implode('_', $orderInvoiceIds) . '.pdf',
                        );
                    }
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        //{{attach_packingsheet({{var order.increment_id}})}}
        $pattern = '/{{' . self::ATTACH_PACKINGSHEET_MARKER . '\((.*?)\)}}/im';
        $orderPackIds = array();
        $orderPackIdsMatches = array();

        try {
            if (preg_match_all($pattern, $emailText, $orderPackIdsMatches)) {
                foreach ($orderPackIdsMatches[1] as $id) {
                    $orderPackIds[] = trim($id);
                }
                array_unique($orderPackIds);
                if (count($orderPackIds) > 0) {
                    $collection = Mage::getResourceModel('sales/order_collection')
                        ->addAttributeToSelect('entity_id')
                        ->addAttributeToFilter('increment_id', array('in' => $orderPackIds));
                    $orderPackIds = array();
                    foreach ($collection as $order) {
                        $orderPackIds[] = $order->getId();
                    }

                    if (count($orderPackIds)) {
                        $pdf = Mage::getModel('pickpack/sales_order_pdf_invoices_default')->getPdfDefault($orderInvoiceIds, 'order', 'pack');
                        $pdfName = Mage::getStoreConfig('pickpack_options/file_name/packsheet_pdf_name');
                        $result['attachments'][] = array(
                            'content' => $pdf->render(),
                            'filename' => $pdfName . implode('_', $orderPackIds) . '.pdf',
                        );
                    }
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        $pattern = '/\<pickpack_email>(.*?)\<\/pickpack_email>/im';

        $result['text'] = preg_replace($pattern, '', $emailText);

        return $result;
    }
}
