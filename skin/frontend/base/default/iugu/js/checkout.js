/**
 * @category    Inovarti
 * @package     Inovarti_Iugu
 * @copyright   Copyright (c) 2014 Inovarti. (http://www.inovarti.com.br)
 */

if (typeof OSCPayment !== "undefined") {
	console.log("Configuring Checkout for OSC");

	OSCPayment._savePayment = OSCPayment.savePayment;
	OSCPayment.savePayment = function() {
		console.log("Iugu: savePayment");

		OSCForm.placeOrderButton.disable();

		if (OSCForm.validate()) {
			console.log(OSCPayment.currentMethod);

			if (OSCPayment.currentMethod == 'iugu_cc') {

				OSCForm.showPleaseWaitNotice();

				Iugu.createPaymentToken($(OSCForm.form.form), function(data) {
					OSCForm.hidePleaseWaitNotice();
					if (data.errors) {
						OSCForm.enablePlaceOrderButton();
					} else {
						$(OSCPayment.currentMethod+'_iugu_token').value = data.id;
						this._savePayment();
					}
				}.bind(this));
			}
			else {
				this._savePayment();
			}
		}
	}
}
else {
	// Default Magento Checkout
	Payment.prototype._save = Payment.prototype.save;
	Payment.prototype.save = function() {
			if (checkout.loadWaiting!=false) return;
			var validator = new Validation(this.form);
			if (this.validate() && validator.validate()) {
					if (this.currentMethod == 'iugu_cc') {
							// Valida CC Owner: precisa ter nome e sobrenome, ou seja, precisa ter um espaço em branco dividindo a string em duas partes.
							if($("iugu_cc_cc_owner").value.trim().split(" ").length < 2) {
								alert("Preencha nome e sobrenome");
								Form.Element.select($("iugu_cc_cc_owner"));
								return;
							}
							var $installments = $(payment.currentMethod+'_installments');
							if ($installments.selectedIndex > 0) {
									$(payment.currentMethod+'_installment_description').value = $installments.options[$installments.selectedIndex].text;
							} else {
									$(payment.currentMethod+'_installment_description').value = '';
							}
							this.iugu_cc_data = {};
							var fields = ['installments', 'installment_description', 'cc_type', 'cc_number', 'cc_owner', 'expiration', 'expiration_yr', 'cc_cid'];
							fields.each(function(field){
									this.iugu_cc_data[field] = $(this.currentMethod+'_'+field).value;
							}.bind(this));
					} else if (this.currentMethod == 'iugu_boleto') {
						if($("tipo_fisica").checked) {
							if(!valida_cpf($("iugu_boleto_cpf_cnpj").value)) {
								alert("CPF inválido");
								Form.Element.select($("iugu_boleto_cpf_cnpj"));
							  return;
							}
						}
						else if($("tipo_juridica").checked) {
							if(!valida_cnpj($("iugu_boleto_cpf_cnpj").value)) {
								alert("CNPJ inválido");
							  Form.Element.select($("iugu_boleto_cpf_cnpj"));
							  return;
						  }
						}
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
			} else if (this.currentMethod == 'iugu_boleto') {
					this.iugu_boleto_data = {};
					var fields = ['name', 'cpf_cnpj'];
					fields.each(function(field){
							this.iugu_boleto_data[field] = $(this.currentMethod+'_'+field).value;
					}.bind(this));
					this._save();
			} else
			{
					this._save();
			}
	};
}

function valida_cnpj(valor) {
  result = true;
  valor = valor.replace(/\D/g, '');
  if(valor.toString().length != 14 || /^(\d)\1{13}$/.test(valor)) return false;
  s = valor;
  c = s.substr(0, 12);
  var dv = s.substr(12, 2);
  var d1 = 0;
  for (i = 0; i < 12; i++) d1 += c.charAt(11 - i) * (2 + (i % 8));
  if (d1 == 0) var result = false;
  d1 = 11 - (d1 % 11);
  if (d1 > 9) d1 = 0;
  if (dv.charAt(0) != d1) var result = false;
  d1 *= 2;
  for (i = 0; i < 12; i++) d1 += c.charAt(11 - i) * (2 + ((i + 1) % 8));
  d1 = 11 - (d1 % 11);
  if (d1 > 9) d1 = 0;
  if (dv.charAt(1) != d1) var result = false;
  return result;
}

function valida_cpf(valor) {
  result = true;
  valor = valor.replace(/\D/g, '');
  if(valor.toString().length != 11 || /^(\d)\1{10}$/.test(valor)) return false;
  s = valor;
  c = s.substr(0, 9);
  var dv = s.substr(9, 2);
  var d1 = 0;
  for (i = 0; i < 9; i++) d1 += c.charAt(i) * (10 - i);
  if (d1 == 0) var result = false;
  d1 = 11 - (d1 % 11);
  if (d1 > 9) d1 = 0;
  if (dv.charAt(0) != d1) var result = false;
  d1 *= 2;
  for (i = 0; i < 9; i++) d1 += c.charAt(i) * (11 - i);
  d1 = 11 - (d1 % 11);
  if (d1 > 9) d1 = 0;
  if (dv.charAt(1) != d1) var result = false;
  return result;
}