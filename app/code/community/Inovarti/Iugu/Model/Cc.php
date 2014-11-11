<?php
/**
 * @category    Inovarti
 * @package     Inovarti_Iugu
 * @copyright   Copyright (c) 2014 Inovarti. (http://www.inovarti.com.br)
 */
class Inovarti_Iugu_Model_Cc extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'iugu_cc';

    protected $_formBlockType = 'iugu/form_cc';
    protected $_infoBlockType = 'iugu/info_cc';

    protected $_isGateway                   = true;
    protected $_canAuthorize                = true;
    protected $_canCapture                  = true;
    protected $_canRefund                   = true;
    protected $_canUseForMultishipping 		= false;

    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        $info->setInstallments($data->getInstallments())
            ->setInstallmentDescription($data->getInstallmentDescription())
            ->setIuguToken($data->getIuguToken())
            ->setIuguSave($data->getIuguSave())
        ;
        return $this;
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $this->_place($payment, $amount);
        return $this;
    }

    public function refund(Varien_Object $payment, $amount)
    {
        $iugu = Mage::getModel('iugu/api');

        $result = $iugu->refund($payment->getIuguInvoiceId());

        $payment->setTransactionId($payment->getIuguInvoiceId() . '-' . Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND)
            ->setParentTransactionId($payment->getIuguInvoiceId())
            ->setIsTransactionClosed(1)
            ->setShouldCloseParentTransaction(1)
            ->setTransactionAdditionalInfo(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,array('status' => $result->getStatus()))
        ;

        return $this;
    }

    protected function _place($payment, $amount)
    {
        $iugu = Mage::getModel('iugu/api');
        $order = $payment->getOrder();

        $items = Mage::helper('iugu')->getItemsFromOrder($payment->getOrder());
        $payer = Mage::helper('iugu')->getPayerInfoFromOrder($payment->getOrder());

        $data = new Varien_Object();
        $data->setToken($payment->getIuguToken())
            ->setMonths($payment->getInstallments())
            ->setEmail($order->getCustomerEmail())
            ->setItems($items)
            ->setPayer($payer)
        ;

        // Discount
        if ($order->getBaseDiscountAmount()) {
            $data->setDiscountCents(Mage::helper('iugu')->formatAmount(abs($order->getBaseDiscountAmount())));
        }

        // Tax
        if ($order->getBaseTaxAmount()) {
            $data->setTaxCents($this->formatAmount($order->getBaseTaxAmount()));
        }

        // Charge
        $result = $iugu->charge($data);
        if (!$result->getSuccess()) {
            Mage::throwException(Mage::helper('iugu')->__('Transaction failed, please try again or contact the card issuing bank.'));
        }

        // Set iugu info
        $payment->setIuguInvoiceId($result->getInvoiceId())
            ->setIuguUrl($result->getUrl())
            ->setIuguPdf($result->getPdf())
            ->setTransactionId($result->getInvoiceId())
            ->setIsTransactionClosed(0)
            ->setTransactionAdditionalInfo(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,array('message' => $result->getMessage()))
        ;

        return $this;
    }
}
