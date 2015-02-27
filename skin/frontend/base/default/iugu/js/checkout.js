/**
 * @category    Inovarti
 * @package     Inovarti_Iugu
 * @copyright   Copyright (c) 2014 Inovarti. (http://www.inovarti.com.br)
 */

// Default Magento Checkout
Payment.prototype._save = Payment.prototype.save;
Payment.prototype.save = function() {
    if (checkout.loadWaiting!=false) return;
    var validator = new Validation(this.form);
    if (this.validate() && validator.validate()) {
        if (this.currentMethod == 'iugu_cc') {
            this.iugu_cc_data = {};
            var fields = ['installments', 'cc_type', 'cc_number', 'cc_owner', 'expiration', 'expiration_yr', 'cc_cid'];
            fields.each(function(field){
                this.iugu_cc_data[field] = $(this.currentMethod+'_'+field).value;
            }.bind(this));
        } else {
            this.iugu_cc_data = null; //clear data
        }
        this._save();
    }
};

Review.prototype._save = Review.prototype.save;
Review.prototype.save = function() {
    var skipToken = $(payment.currentMethod+'_iugu_customer_payment_method_id')
        && $(payment.currentMethod+'_iugu_customer_payment_method_id').value != "";
    if (payment.currentMethod == 'iugu_cc' && !skipToken) {
        var $installments = $(payment.currentMethod+'_installments');
        if ($installments.selectedIndex > 0) {
            $(payment.currentMethod+'_installment_description').value = $installments.options[$installments.selectedIndex].text;
        } else {
            $(payment.currentMethod+'_installment_description').value = '';
        }
        checkout.setLoadWaiting('review');
        Iugu.createPaymentToken($(payment.form), function(data) {
            checkout.setLoadWaiting(false);
            if (data.errors) {
                alert(JSON.stringify(data.errors));
            } else {
                $(payment.currentMethod+'_iugu_token').value = data.id;
                this._save();
            }
        }.bind(this));
    } else {
        this._save();
    }
};
