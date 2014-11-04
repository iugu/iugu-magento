/**
 * @category    Inovarti
 * @package     Inovarti_Iugu
 * @copyright   Copyright (c) 2014 Inovarti. (http://www.inovarti.com.br)
 */

Validation.creditCartTypes = $H({
    'DC': [new RegExp('^3(?:0[0-5]|[68][0-9])[0-9]{11}$'), new RegExp('^[0-9]{3}$'), true, new RegExp('^3(?:0[0-5]|[68][0-9])')],
    'AE': [new RegExp('^3[47][0-9]{13}$'), new RegExp('^[0-9]{4}$'), true, new RegExp('^3[47]')],
    'VI': [new RegExp('^4[0-9]{12}([0-9]{3})?$'), new RegExp('^[0-9]{3}$'), true, new RegExp('^4')],
    'MC': [new RegExp('^5[1-5][0-9]{14}$'), new RegExp('^[0-9]{3}$'), true, new RegExp('^5[1-5]')]
});

Validation.add('validate-iugu-cc-number', 'Please enter a valid credit card number.', function(v, elm) {
    return Validation.get('validate-cc-number').test(v, elm) && Validation.get('validate-cc-type').test(v, elm);
});

Validation.add('validate-iugu-cc-exp', 'Incorrect credit card expiration date.', function(v, elm){
    var ccExpMonth   = v;
    var ccExpYear    = $(elm.id.substr(0,elm.id.indexOf('_expiration')) + '_expiration_yr').value;
    return ccExpMonth && ccExpYear && Validation.get('validate-cc-exp').test(v, elm);
});

Validation.add('validate-iugu-cc-cvn', 'Please enter a valid credit card verification number.', function(v, elm){
    var ccTypeContainer = $(elm.id.substr(0,elm.id.indexOf('_cc_cid')) + '_cc_type');
    if (!ccTypeContainer) {
        return true;
    }
    var ccType = ccTypeContainer.value;

    if (typeof Validation.creditCartTypes.get(ccType) == 'undefined') {
        return true;
    }

    var re = Validation.creditCartTypes.get(ccType)[1];
    return v.match(re) != null;
});
