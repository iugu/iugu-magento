/**
 * @category    Inovarti
 * @package     Inovarti_Iugu
 * @copyright   Copyright (c) 2014 Inovarti. (http://www.inovarti.com.br)
 */

Validation.creditCartTypes = $H({
    'EL': [null, null, true, new RegExp('^(506[0-9]|431274|438935|451416|457393|45763[1-2]|506(699|7[0-6][0-9]|77[0-8])|509\d{3}|504175|627780|636297|636368|65003[1-3]|6500(3[5-9]|4[0-9]|5[0-1])|6504(0[5-9]|[1-3][0-9])|650(4[8-9][0-9]|5[0-2][0-9]|53[0-8])|6505(4[1-9]|[5-8][0-9]|9[0-8])|6507(0[0-9]|1[0-8])|65072[0-7]|6509(0[1-9]|1[0-9]|20)|6516(5[2-9]|[6-7][0-9])|6550([0-1][0-9]|2[1-9]|[3-4][0-9]|5[0-8]))')],
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
