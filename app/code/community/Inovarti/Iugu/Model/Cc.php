<?php
/**
 * @category    Inovarti
 * @package     Inovarti_Iugu
 * @copyright   Copyright (c) 2014 Inovarti. (http://www.inovarti.com.br)
 */
class Inovarti_Iugu_Model_Cc extends Mage_Payment_Model_Method_Abstract
{
    const MIN_INSTALLMENT_VALUE = 5;

    protected $_code = 'iugu_cc';

    protected $_formBlockType = 'iugu/form_cc';
    protected $_infoBlockType = 'iugu/info_cc';

    protected $_isGateway                   = true;
    protected $_canAuthorize                = true;
    protected $_canCapture                  = true;
    protected $_canRefund                   = true;
    protected $_canUseForMultishipping      = false;
    protected $_canUseInternal              = false;

    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        $info->setInstallments($data->getInstallments())
            ->setInstallmentDescription($data->getInstallmentDescription())
            ->setIuguToken($data->getIuguToken())
            ->setIuguCustomerPaymentMethodId($data->getIuguCustomerPaymentMethodId())
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
        $result = Mage::getSingleton('iugu/api')->refund($payment->getIuguInvoiceId());

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
        $order = $payment->getOrder();

        $payer = Mage::helper('iugu')->getPayerInfoFromOrder($payment->getOrder());
        $items = Mage::helper('iugu')->getItemsFromOrder($payment->getOrder());

        // Verify if needs add interest
        $interestRate = $this->getInterestRate($payment->getInstallments());
        $totalWithInterest = $this->calcTotalWithInterest($amount, $interestRate);
        if ($totalWithInterest - $amount > 0) {
            $item = new Varien_Object();
            $item->setDescription(Mage::helper('iugu')->__('Interest'));
            $item->setQuantity(1);
            $item->setPriceCents(Mage::helper('iugu')->formatAmount($totalWithInterest - $amount));
            $items[] = $item;
        }

        // Save Payment method
        if (!$payment->getIuguCustomerPaymentMethodId() && $payment->getIuguSave()) {
            $data = new Varien_Object();
            $data->setToken($payment->getIuguToken());
            $data->setCustomerId(Mage::helper('iugu')->getCustomerId());
            $data->setDescription(Mage::getModel('core/date')->timestamp(time()));
            $result = Mage::getSingleton('iugu/api')->savePaymentMethod($data);
            if ($result->getId()) {
                $payment->setIuguCustomerPaymentMethodId($result->getId());
            }
        }

        // Set Charge Data
        $data = new Varien_Object();
        if ($payment->getIuguCustomerPaymentMethodId()) {
            $data->setCustomerPaymentMethodId($payment->getIuguCustomerPaymentMethodId());
        } else {
            $data->setToken($payment->getIuguToken());
        }
        $data->setMonths($payment->getInstallments())
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
        $result = Mage::getSingleton('iugu/api')->charge($data);
        if (!$result->getSuccess()) {
            Mage::throwException(Mage::helper('iugu')->__('Transaction failed, please try again or contact the card issuing bank.'));
        }

        // Set iugu info
        $payment->setIuguInvoiceId($result->getInvoiceId())
            ->setIuguTotalWithInterest($totalWithInterest)
            ->setIuguUrl($result->getUrl())
            ->setIuguPdf($result->getPdf())
            ->setTransactionId($result->getInvoiceId())
            ->setIsTransactionClosed(0)
            ->setTransactionAdditionalInfo(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,array('message' => $result->getMessage()))
        ;

        return $this;
    }

    /**
     * @param float $amount
     * @return array
     */
    public function getInstallmentOptions($amount = null)
    {
        $quote = $this->getInfoInstance()->getQuote();
        if (is_null($amount)) {
            $amount = $quote->getGrandTotal();
        }

        $maxInstallments = (int)$this->getConfigData('max_installments');
        $minInstallmentValue = (float)$this->getConfigData('min_installment_value');

        if ($minInstallmentValue < self::MIN_INSTALLMENT_VALUE) {
            $minInstallmentValue = self::MIN_INSTALLMENT_VALUE;
        }

        $installments = floor($amount / $minInstallmentValue);
        if ($installments > $maxInstallments) {
            $installments = $maxInstallments;
        } elseif ($installments < 1) {
            $installments = 1;
        }

        $options = array();
        for ($i=1; $i <= $installments; $i++) {
            if ($i == 1) {
                $label = Mage::helper('iugu')->__('Pay in full - %s', $quote->getStore()->formatPrice($amount, false));
            } else {
                $interestRate = $this->getInterestRate($i);
                $installmentAmount = $this->calcInstallmentAmount($amount, $i, $interestRate);
                if ($interestRate > 0) {
                    $label = Mage::helper('iugu')->__('%sx - %s with interest', $i, $quote->getStore()->formatPrice($installmentAmount, false));
                } else {
                    $label = Mage::helper('iugu')->__('%sx - %s without interest', $i, $quote->getStore()->formatPrice($installmentAmount, false));
                }
            }
            $options[$i] = $label;
        }
        return $options;
    }

    /**
     * @param int $installments
     * @return float
     */
    public function getInterestRate($installments)
    {
        $interestMap = unserialize($this->getConfigData('interest_rate'));
        usort($interestMap, array($this, '_sortInterestRateByInstallments'));
        $interestMap = array_reverse($interestMap, true);
        $interestRate = 0;
        foreach ($interestMap as $item) {
            if ($installments <= $item['installments']) {
                $interestRate = $item['interest'];
            }
        }
        return (float)$interestRate/100;
    }

    /**
     * @param float $amount
     * @param int $installments
     * @param float $rate
     * @return float
     */
    public function calcInstallmentAmount($amount, $installments, $rate = 0.0)
    {
        if ($rate > 0){
            $result = $this->calcTotalWithInterest($amount, $rate) / $installments;
        } else {
            $result = $amount / $installments;
        }
        return round($result, 2);
    }

    /**
     * @param float $amount
     * @param float $rate
     * @return float
     */
    public function calcTotalWithInterest($amount, $rate = 0.0)
    {
        return $amount + ($amount * $rate);
    }

    protected function _sortInterestRateByInstallments($a, $b)
    {
        if ($a['installments'] == $b['installments']) {
            return 0;
        }
        return ($a['installments'] < $b['installments']) ? -1 : 1;
    }
}
