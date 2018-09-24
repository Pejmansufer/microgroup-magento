// this value will hold the first column filtered by
// so that all of the options do not dissapear under
// that group. Otherwise once you select one option
// all else would disappear under that group
firstFilterSel = null;

function filter() {
    // First, check to see if this is the first filter option checked
    var numChecked = $j('#bundled-sidebar-toggle input:checked').length;
    if(numChecked == 0) {
	firstFilterSel = null;
    } else if (numChecked == 1) {
	firstFilterSel = $j('#bundled-sidebar-toggle input:checked').attr('data-grp');
    }
    
    // Get the column numbers for each filter group,
    // E.g. the first column 'Guage' will be set to 0
    var pos = {};
    $j('#sub-products thead th').each(function() {
	var attr = $j(this).attr('data-grp');
	if (typeof attr !== typeof undefined && attr !== false) {
	    pos[attr] = $j(this).index();
	}
    });

    // show all rows and all filter options, we will narrow them down
    // later in this function
    $j('#sub-products tbody tr').show();
    $j('#bundled-sidebar-toggle li').show()

    // figure out what boxes have been checked and save them in object
    var selected = {};
    $j('#bundled-sidebar-toggle input:checked').each(function() {
	//console.log($j(this).attr('data-grp') + ' ' + $j(this).attr('data-val'));
	var data_grp = $j(this).attr('data-grp');
	var data_val = $j(this).attr('data-val');

	if (typeof selected[data_grp] === typeof undefined || selected[data_grp] === false) {
	    selected[data_grp] = [];
	}
	selected[data_grp].push(data_val);
    });

    // loop through each table row and each entry in selected
    // to see if the row should be hidden
    $j('#sub-products tbody tr').each(function() {
	// hide this is initally set to zero, and we add one to every filter group
	// it applies to . If by the end that number is equal to the amount of
	// filters, then we do not hide this. This makes it so all filters need to apply
	var hide_this = 0;
	var total_options = 0;
	// loop through the column that we have checked a filter
	for (var key in selected) {
	    if (selected.hasOwnProperty(key)) {
		total_options++;
		// this gets the column we should look at
		var col_pos = pos[key];
		// this gets the value of the column in the current row
		var col_val = $j(this).children().eq(col_pos).html();
		// these are the options we are looking for
		var options = selected[key];
		for(var i = 0; i < options.length; i++) {
		    if(options[i].replace(/"/g,'') == col_val.replace(/"/g,'')) {
			hide_this++;
			break;
		    }
		}
	    }
	}
	if(hide_this!=total_options) $j(this).hide();
    });

    // create an object to hold the new count values
    var newCount = {};
    for (var key in pos) {
	if (pos.hasOwnProperty(key)) {
	    newCount[key] = {};
	}
    }    
    // NOW we are going to loop through the visible rows to get the
    // count of distinct values. This is so the left hand filter
    // can be updated
    $j('#sub-products tbody tr:visible').each(function() {
	// we're going to loop through each relevant columns
	for (var key in pos) {
	    if (pos.hasOwnProperty(key)) {
		// get value of table cell
		var new_val = $j(this).children().eq(pos[key]).html();
		// check to see if this is the first time this value as been seen
		if (typeof newCount[key][new_val] === typeof undefined
		    || newCount[key][new_val] === false) {
		    newCount[key][new_val] = 0;
		}
		newCount[key][new_val]++;
	    }
	}
    });

    // Now, we loop through the filters, hiding irrelevant checkboxes
    // and updating the count
    $j('#bundled-sidebar-toggle input').each(function() {
	var data_grp = $j(this).attr('data-grp');
	var data_val = $j(this).attr('data-val');

	// this this isn't a checkbox in the first group filtered by
	if(data_grp!=firstFilterSel) {
	    if (typeof newCount[data_grp][data_val] === typeof undefined
		|| newCount[data_grp][data_val] === false) {
		$j(this).parent('li').hide();
	    } else {
		//update count
		$j(this).siblings('.count').html('('+newCount[data_grp][data_val]+')')
	    }
	}
    });

    // Finally, we are going to add a clear button for each filter group
    $j('#bundled-sidebar-toggle div').each(function() {
	if($j(this).next('ul').find('input:checked').length == 0) {
	    $j(this).children('a').addClass('hidden');
	}else{
	    $j(this).children('a').removeClass('hidden');
	}
    });
}
function clearFilter(grp) {
    $j('#bundled-sidebar-toggle ul[data-grp='+grp+'] input').each(function() {
	$j(this).attr('checked', false);
    });
    $j('#bundled-sidebar-toggle div[data-grp='+grp+']').removeClass('active');
    $j('#bundled-sidebar-toggle ul[data-grp='+grp+']').addClass('no-display');
    $j('#bundled-sidebar-toggle div[data-grp='+grp+'] a').addClass('hidden');
    $j('#bundled-sidebar-toggle ul[data-grp='+grp+'] li').show()

    filter();
}

function modifyCart() {
    return false;
}


/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    design
 * @package     base_default
 * @copyright   Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if(typeof Product=='undefined') {
    var Product = {};
}
/**************************** BUNDLE PRODUCT **************************/
Product.Bundle = Class.create();
Product.Bundle.prototype = {
    initialize: function(config){
        this.config = config;

        // Set preconfigured values for correct price base calculation
        if (config.defaultValues) {
            for (var option in config.defaultValues) {
                if (this.config['options'][option].isMulti) {
                    var selected = new Array();
                    for (var i = 0; i < config.defaultValues[option].length; i++) {
                        selected.push(config.defaultValues[option][i]);
                    }
                    this.config.selected[option] = selected;
                } else {
                    this.config.selected[option] = new Array(config.defaultValues[option] + "");
                }
            }
        }

        this.reloadPrice();
    },
    changeSelection: function(selection){
        var parts = selection.id.split('-');
        if (this.config['options'][parts[2]].isMulti) {
            selected = new Array();
            if (selection.tagName == 'SELECT') {
                for (var i = 0; i < selection.options.length; i++) {
                    if (selection.options[i].selected && selection.options[i].value != '') {
                        selected.push(selection.options[i].value);
                    }
                }
            } else if (selection.tagName == 'INPUT') {
                selector = parts[0]+'-'+parts[1]+'-'+parts[2];
                selections = $$('.'+selector);
                for (var i = 0; i < selections.length; i++) {
                    if (selections[i].checked && selections[i].value != '') {
                        selected.push(selections[i].value);
                    }
                }
            }
            this.config.selected[parts[2]] = selected;
        } else {
            if (selection.value != '') {
                this.config.selected[parts[2]] = new Array(selection.value);
            } else {
                this.config.selected[parts[2]] = new Array();
            }

            this.reloadPriceRow(parts[2]);
        }
        this.reloadPrice();
    },

    reloadPriceRow: function(qty_id) {
	var qty_box =  $j('#bundle-option-'+qty_id+'-qty-input');
	var qty = parseInt(qty_box.val());
	var selection_value = qty_box.parents('tr').find('.bundle-option-select').val();

	this.showQtyInput(qty_id, qty, true);

	
        var tierPriceElement = $('bundle-option-' + qty_id + '-tier-prices'),
            tierPriceHtml = '';

	if(selection_value in this.config.options[qty_id].selections) {
	    var info = this.config.options[qty_id].selections[selection_value];
            if (selection_value != '' && this.config.options[qty_id].selections[selection_value].customQty == 1) {
		tierPriceHtml = info.price;
		for (var i = 0; i<info.tierPrice.length; i++) {
		    if(qty >= parseInt(info.tierPrice[i].price_qty)) {
			tierPriceHtml = info.tierPrice[i].price;
		    }
		}
            }
	    tierPriceElement.update(tierPriceHtml);
	}
    },
    
    reloadPrice: function() {
        var calculatedPrice = 0;
        var dispositionPrice = 0;
        var includeTaxPrice = 0;

        for (var option in this.config.selected) {
            if (this.config.options[option]) {
                for (var i=0; i < this.config.selected[option].length; i++) {
                    var prices = this.selectionPrice(option, this.config.selected[option][i]);
                    calculatedPrice += Number(prices[0]);
                    dispositionPrice += Number(prices[1]);
                    includeTaxPrice += Number(prices[2]);
                }
            }
        }

        //Tax is calculated in a different way for the the TOTAL BASED method
        //We round the taxes at the end. Hence we do the same for consistency
        //This variable is set in the bundle.phtml
        if (taxCalcMethod == CACL_TOTAL_BASE) {
            var calculatedPriceFormatted = calculatedPrice.toFixed(10);
            var includeTaxPriceFormatted = includeTaxPrice.toFixed(10);
            var tax = includeTaxPriceFormatted - calculatedPriceFormatted;
            calculatedPrice = includeTaxPrice - Math.round(tax * 100) / 100;
        }

        //make sure that the prices are all rounded to two digits
        //this is needed when tax calculation is based on total for dynamic
        //price bundle product. For fixed price bundle product, the rounding
        //needs to be done after option price is added to base price
        if (this.config.priceType == '0') {
            calculatedPrice = Math.round(calculatedPrice*100)/100;
            dispositionPrice = Math.round(dispositionPrice*100)/100;
            includeTaxPrice = Math.round(includeTaxPrice*100)/100;

        }

        var event = $(document).fire('bundle:reload-price', {
            price: calculatedPrice,
            priceInclTax: includeTaxPrice,
            dispositionPrice: dispositionPrice,
            bundle: this
        });
        if (!event.noReloadPrice) {
            optionsPrice.specialTaxPrice = 'true';
            optionsPrice.changePrice('bundle', calculatedPrice);
            optionsPrice.changePrice('nontaxable', dispositionPrice);
            optionsPrice.changePrice('priceInclTax', includeTaxPrice);
            optionsPrice.reload();
        }

        return calculatedPrice;
    },

    selectionPrice: function(optionId, selectionId) {
        if (selectionId == '' || selectionId == 'none') {
            return 0;
        }
        var qty = null;
        var tierPriceInclTax, tierPriceExclTax;
        if (this.config.options[optionId].selections[selectionId].customQty == 1 && !this.config['options'][optionId].isMulti) {
            if ($('bundle-option-' + optionId + '-qty-input')) {
                qty = $('bundle-option-' + optionId + '-qty-input').value;
            } else {
                qty = 1;
            }
        } else {
            //qty = this.config.options[optionId].selections[selectionId].qty;
	    qty = 0;
        }
        if (this.config.priceType == '0' || 1) {
            price = this.config.options[optionId].selections[selectionId].price;
            tierPrice = this.config.options[optionId].selections[selectionId].tierPrice;

            for (var i=0; i < tierPrice.length; i++) {
                if (Number(tierPrice[i].price_qty) <= qty && Number(tierPrice[i].price) <= price) {
                    price = tierPrice[i].price;
                    tierPriceInclTax = tierPrice[i].priceInclTax;
                    tierPriceExclTax = tierPrice[i].priceExclTax;
                }
            }
        } else {
            selection = this.config.options[optionId].selections[selectionId];
            if (selection.priceType == '0') {
                price = selection.priceValue;
            } else {
                price = (this.config.basePrice*selection.priceValue)/100;
            }
        }
        //price += this.config.options[optionId].selections[selectionId].plusDisposition;
        //price -= this.config.options[optionId].selections[selectionId].minusDisposition;
        //return price*qty;
        var disposition = this.config.options[optionId].selections[selectionId].plusDisposition +
            this.config.options[optionId].selections[selectionId].minusDisposition;

        if (this.config.specialPrice) {
            newPrice = (price*this.config.specialPrice)/100;
            price = Math.min(newPrice, price);
        }

        selection = this.config.options[optionId].selections[selectionId];
        if (tierPriceInclTax !== undefined && tierPriceExclTax !== undefined) {
            priceInclTax = tierPriceInclTax;
            price = tierPriceExclTax;
        } else if (selection.priceInclTax !== undefined) {
            priceInclTax = selection.priceInclTax;
            price = selection.priceExclTax !== undefined ? selection.priceExclTax : selection.price;
        } else {
            priceInclTax = price;
        }

        if (this.config.priceType == '1' || taxCalcMethod == CACL_TOTAL_BASE) {
            var result = new Array(price*qty, disposition*qty, priceInclTax*qty);
            return result;
        }
        else if (taxCalcMethod == CACL_UNIT_BASE) {
            price = (Math.round(price*100)/100).toString();
            disposition = (Math.round(disposition*100)/100).toString();
            priceInclTax = (Math.round(priceInclTax*100)/100).toString();
            var result = new Array(price*qty, disposition*qty, priceInclTax*qty);
            return result;
        } else { //taxCalcMethod == CACL_ROW_BASE)
            price = (Math.round(price*qty*100)/100).toString();
            disposition = (Math.round(disposition*qty*100)/100).toString();
            priceInclTax = (Math.round(priceInclTax*qty*100)/100).toString();
            var result = new Array(price, disposition, priceInclTax);
            return result;
        }
    },

    populateQty: function(optionId, selectionId){
        if (selectionId == '' || selectionId == 'none') {
            this.showQtyInput(optionId, '0', false);
            return;
        }
        if (this.config.options[optionId].selections[selectionId].customQty == 1) {
            //this.showQtyInput(optionId, this.config.options[optionId].selections[selectionId].qty, true);
            this.showQtyInput(optionId, 0, true);
        } else {
            //this.showQtyInput(optionId, this.config.options[optionId].selections[selectionId].qty, false);
	    this.showQtyInput(optionId, 0, false);
        }
    },

    showQtyInput: function(optionId, value, canEdit) {
        elem = $('bundle-option-' + optionId + '-qty-input');
	
	if(isNaN(value)) value = 0;

        elem.value = value;
        elem.disabled = !canEdit;

            elem.removeClassName('qty-disabled');

    },

    changeOptionQty: function (element, event) {
	var checkQty = true;
        if (typeof(event) != 'undefined') {
            if (event.keyCode == 8 || event.keyCode == 46) {
                checkQty = false;
            }
        }
        if (checkQty && (Number(element.value) == 0 || isNaN(Number(element.value)))) {
            element.value = 0;
        }

        parts = element.id.split('-');
	this.reloadPriceRow(parts[2]);
        optionId = parts[2];
        if (!this.config['options'][optionId].isMulti) {
            selectionId = this.config.selected[optionId][0];
            this.config.options[optionId].selections[selectionId].qty = element.value*1;
            this.reloadPrice();
        }
    },

    validationCallback: function (elmId, result){
        if (elmId == undefined || $(elmId) == undefined) {
            return;
        }
        var container = $(elmId).up('ul.options-list');
        if (typeof container != 'undefined') {
            if (result == 'failed') {
                container.removeClassName('validation-passed');
                container.addClassName('validation-failed');
            } else {
                container.removeClassName('validation-failed');
                container.addClassName('validation-passed');
            }
        }
    }
}
