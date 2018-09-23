/**
 * Moogento
 *
 * SOFTWARE LICENSE
 *
 * This source file is covered by the Moogento End User License Agreement
 * that is bundled with this extension in the file License.html
 * It is also available online here:
 * http://www.moogento.com/License.html
 *
 * NOTICE
 *
 * If you customize this file please remember that it will be overwrtitten
 * with any future upgrade installs.
 * If you'd like to add a feature which is not in this software, get in touch
 * at www.moogento.com for a quote.
 *
 * ID          pe+sMEDTrtCzNq3pehW9DJ0lnYtgqva4i4Z=
 * Version     3.0.10
 * File        orderView.js
 * @category   Moogento
 * @package    shipEasy
 * @copyright  Copyright (c) 2014 Moogento <info@moogento.com> / All rights reserved.
 * @license    http://www.moogento.com/License.html
 */
jQuery(document).ready(function ($) {
    
    $('#anchor-content').on('click', '#customer_info .customer_name .value.non-edit .edit-icon', function (e) {
        var parent_td = $(this).closest('.non-edit');
        parent_td.removeClass('non-edit');
        parent_td.addClass('for-edit');
        parent_td.find("#select_customer").focus();
    });
    
    $('#anchor-content').on('focusout', '#customer_info .customer_name .value.for-edit #select_customer', function (e) {
        var parent_td = $(this).closest('.for-edit');
        parent_td.removeClass('for-edit');
        parent_td.addClass('non-edit');
    });
    
    $('#anchor-content').on('change', '#customer_info #select_customer', function (e) {
        var parent_td = $(this).closest('.for-edit');
        var select_cust = parent_td.find('#select_customer');
        var url_data = select_cust.data('changeurl');
        var order_id = select_cust.data('orderid');
        var customer_id = select_cust.val();
        $.ajax({
            type: 'POST',
            url: url_data,
            dataType: 'json',
            data: {
                orderid: order_id,
                customerid: customer_id,
                form_key: FORM_KEY
            },
            success: function (data) {
                window.location.reload();
            }
        });
        parent_td.removeClass('for-edit');
        parent_td.addClass('non-edit');
    });
    
});