<?php
/**
 * @category    Inovarti
 * @package     Inovarti_Iugu
 * @copyright   Copyright (c) 2014 Inovarti. (http://www.inovarti.com.br)
 */
class Inovarti_Iugu_Block_Form_Cc extends Mage_Payment_Block_Form_Cc
{
    const MIN_INSTALLMENT_VALUE = 5;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('iugu/form/cc.phtml');
    }

    /**
     * Retrieve availables installments
     *
     * @return array
     */
    public function getInstallmentsAvailables(){
        $maxInstallments = (int)Mage::getStoreConfig('payment/iugu_cc/max_installments');
        $minInstallmentValue = (float)Mage::getStoreConfig('payment/iugu_cc/min_installment_value');
        $maxInstallmentsWithoutInterest = (int)Mage::getStoreConfig('payment/iugu_cc/max_installments_without_interest');
        if ($minInstallmentValue < self::MIN_INSTALLMENT_VALUE) {
            $minInstallmentValue = self::MIN_INSTALLMENT_VALUE;
        }

        $quote = Mage::helper('checkout')->getQuote();
        $total = $quote->getGrandTotal();

        $installments = floor($total / $minInstallmentValue);
        if ($installments > $maxInstallments) {
            $installments = $maxInstallments;
        } elseif ($installments < 1) {
            $installments = 1;
        }

        $options = array();
        for ($i=1; $i <= $installments; $i++) {
            if ($i == 1) {
                $label = $this->__('Pay in full - %s', $quote->getStore()->formatPrice($total, false));
            } else {
                $installmentAmount = round($total/$i, 2);
                if ($i > $maxInstallmentsWithoutInterest) {
                    $label = $this->__('%sx - %s with interest', $i, $quote->getStore()->formatPrice($installmentAmount, false));
                } else {
                    $label = $this->__('%sx - %s without interest', $i, $quote->getStore()->formatPrice($installmentAmount, false));
                }
            }
            $options[$i] = $label;
        }
        return $options;
    }

    /**
     * Retrieve credit card expire months
     *
     * @return array
     */
    public function getCcMonths()
    {
        $months = $this->getData('cc_months');
        if (is_null($months)) {
            $months[0] =  $this->__('Month');
            for ($i=1; $i <= 12; $i++) {
                $months[$i] = str_pad($i, 2, '0', STR_PAD_LEFT);
            }
            $this->setData('cc_months', $months);
        }
        return $months;
    }
}
