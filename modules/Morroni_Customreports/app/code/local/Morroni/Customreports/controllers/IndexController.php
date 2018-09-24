<?php

class Morroni_Customreports_IndexController
    extends Mage_Adminhtml_Controller_Action
{

    private function outputCSV($data, $type) {
        $filename = 'MG_'.$type.'_'.date('Y-m-d_Hi', time()).'.csv';
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        $output = fopen('php://output', 'w');
        foreach($data as $datum) {
            fputcsv($output, $datum);
        }
    }

    public function orderAction()
    {
        setlocale(LC_MONETARY, 'en_US.UTF-8');

        $orderIds = $_POST['order_ids'];

        $data = array(array('Order #','Order Status','Order Date','Customer Email','Customer Group','Customer Name','First','Last','Company','Address','Address #2','Country','Region','City','Zip Code','Phone','Product Subtotal','Tax Amount','Discount Amount','Subtotal','Shipping and Handling','Grand Total','Total Paid','Total Refunded','Tax Refunded','Discount Amount Refunded','Subtotal Refunded','Refund Shipping','Adjustment Refund','Adjustment Fee','Grand Total Refund','CC last 4','CC type','Tax Exempt ID'));

        foreach($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);

            
            $customer_group = Mage::getModel('customer/group')->load($order['customer_group_id'])->getCode();
            $shipping_address = Mage::getModel('sales/order_address')->load($order['shipping_address_id']);
            $country = Mage::app()->getLocale()->getCountryTranslation($shipping_address['country_id']);
            $payment = $order->getPayment();

            $payment_additional = $payment['additional_information']['authorize_cards'];
            $payment_additional = array_shift($payment_additional);
            $aType = Mage::getSingleton('payment/config')->getCcTypes();
            if (isset($aType[$payment_additional['cc_type']])) {
                $cc_type = $aType[$payment_additional['cc_type']];
            } else {
                $cc_type = $payment_additional['cc_type'];
            }
            $created_at = date('M j, Y g:i:s A', strtotime($order['created_at']));

            //Order # | Order Status | Order Date | Customer Email | Customer Group | Customer Name | First | Last | Company | Address | Address #2 | Country | Region | City | Zip Code | Phone | Product Subtotal | Tax Amount | Discount Amount | Product Total | Subtotal | Shipping and Handling | Grand Total | Total Paid | Total Refunded | Tax Refunded | Discount Amount Refunded | Subtotal Refunded | Refund Shipping | Adjustment Refund | Adjustment Fee | Grand Total Refund | CC last 4 | CC type | Tax Exempt ID


            $data[] = array($order['increment_id'],
                            ucwords($order['status']),
                            $created_at,
                            $order['customer_email'],
                            $customer_group,
                            $order['customer_firstname'].' '.$order['customer_lastname'],
                            $order['customer_firstname'],
                            $order['customer_lastname'],
                            $shipping_address['company'],
                            $shipping_address['street'],
                            '',
                            $country,
                            $shipping_address['region'],
                            $shipping_address['city'],
                            "'".$shipping_address['postcode'],
                            "'".$order['phone'],
                            money_format('%.2n', $order['base_subtotal']),
                            money_format('%.2n', $order['base_tax_amount']),
                            money_format('%.2n', $order['base_discount_amount']),
                            money_format('%.2n', $order['base_subtotal']+$order['base_tax_amount']-$order['base_discount_amount']),
                            money_format('%.2n', $order['base_shipping_amount']),
                            money_format('%.2n', $order['base_grand_total']),
                            money_format('%.2n', $order->getTotalPaid()),
                            money_format('%.2n', isset($order['base_subtotal_refunded'])?$order['base_subtotal_refunded']:0),
                            money_format('%.2n', isset($order['base_tax_refunded'])?$order['base_tax_refunded']:0),
                            money_format('%.2n', isset($order['base_discount_refunded'])?$order['base_discount_refunded']:0),
                            money_format('%.2n', isset($order['base_subtotal_refunded'])?$order['base_subtotal_refunded']:0),
                            money_format('%.2n', isset($order['base_shipping_refunded'])?$order['base_shipping_refunded']:0),
                            money_format('%.2n', isset($order['base_adjustment_negative'])?$order['base_adjustment_negative']:0),
                            money_format('%.2n', isset($order['base_adjustment_fee'])?$order['base_adjustment_fee']:0),
                            money_format('%.2n', isset($order['base_total_refunded'])?$order['base_total_refunded']:0),
                            "'".$payment_additional['cc_last4'], 
                            $cc_type, 
                            isset($payment['tax_relief_code'])?"'".$payment['tax_relief_code']:''

            );

        }
        $this->outputCSV($data, 'Order_Report');
    }
    public function detailAction()
    {
        setlocale(LC_MONETARY, 'en_US.UTF-8');

        $orderIds = $_POST['order_ids'];

        $data = array(array('Order #','Order Status','Order Date','SKU','Customer Email','Customer Name','Customer Group','First','Last ','Company','Address','Address #2','Country','Region','City','Zip Code','Product Name','Qty. Ordered','Qty. Invoiced','Qty. Shipped','Qty. Refunded','Price','Subtotal','Discounts','Tax','Total','Total Incl. Tax'));

        foreach($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            
            $customer_group = Mage::getModel('customer/group')->load($order['customer_group_id'])->getCode();
            $shipping_address = Mage::getModel('sales/order_address')->load($order['shipping_address_id']);
            $country = Mage::app()->getLocale()->getCountryTranslation($shipping_address['country_id']);
            $created_at = date('M j, Y g:i:s A', strtotime($order['created_at']));
            
            foreach($order->getAllItems() as $item) {
                $data[] = array($order['increment_id'],
                                ucwords($order['status']),
                                $created_at,
                                $item['sku'],
                                $order['customer_email'],
                                $customer_group,
                                $order['customer_firstname'].' '.$order['customer_lastname'],
                                $order['customer_firstname'],
                                $order['customer_lastname'],
                                $shipping_address['company'],
                                $shipping_address['street'],
                                '',
                                $country,
                                $shipping_address['region'],
                                $shipping_address['city'],
                                $shipping_address['postcode'],
                                $item['name'],
                                $item['qty_ordered'],
                                $item['qty_invoiced'],
                                $item['qty_shipped'],
                                $item['qty_refunded'],
                                money_format('%.2n', $item['base_price']),
                                money_format('%.2n', $item['base_price']*$item['qty_ordered']),
                                money_format('%.2n', $item['base_discount_amount']),
                                money_format('%.2n', $item['base_tax_amount']),
                                money_format('%.2n', $item['base_row_total']),
                                money_format('%.2n', $item['base_row_total_incl_tax'])
                    
                );
            }

        }
        $this->outputCSV($data, 'Order_Item_Detail_Report');

    }


}