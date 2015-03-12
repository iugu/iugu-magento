<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Iugu_Block_Checkout_Cart_Total extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('iugu/checkout/cart/total.phtml');
    }

    public function getAmount()
    {
        $amount = $this->_getQuote()->getBaseGrandTotal();
        $payment = $this->_getPayment();
        if ($payment->getMethod() == 'iugu_cc') {
            $installments = $payment->getInstallments();
            $interestRate = $payment->getMethodInstance()->getInterestRate($installments);
            $installmentAmount = $payment->getMethodInstance()->calcInstallmentAmount($amount, $installments, $interestRate);
            $amount = $installmentAmount * $installments;
        }

        return $amount;
    }

    protected function _getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    protected function _getPayment()
    {
        return $this->_getQuote()->getPayment();
    }

    protected function _toHtml()
    {
        if ($this->getAmount() == $this->_getQuote()->getBaseGrandTotal()) {
            return '';
        }
        return parent::_toHtml();
    }
}
