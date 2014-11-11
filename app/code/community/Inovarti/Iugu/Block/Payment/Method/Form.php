<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Iugu_Block_Payment_Method_Form extends Mage_Core_Block_Template
{
    protected $_method;
    protected $_paymentMethod;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('iugu/payment_method/form.phtml');
    }

    public function getTitle()
    {
        if ($title = $this->getData('title')) {
            return $title;
        }
        if ($this->getPaymentMethod()->getId()) {
            $title = Mage::helper('iugu')->__('Edit Credit Card');
        }
        else {
            $title = Mage::helper('iugu')->__('Add New Credit Card');
        }
        return $title;
    }

    public function getPaymentMethod()
    {
        if (is_null($this->_paymentMethod)) {
            if ($paymentMethodId = $this->getRequest()->getParam('id')) {
                $customerId = Mage::helper('iugu')->getCustomerId();
                $iugu = Mage::getModel('iugu/api');
                $this->_paymentMethod = $iugu->getPaymentMethod($customerId, $paymentMethodId);
            } else {
                $this->_paymentMethod = new Varien_Object();
            }
        }
        return $this->_paymentMethod;
    }

    public function getCcAvailableTypes()
    {
        $types = Mage::getSingleton('payment/config')->getCcTypes();
        $availableTypes = Mage::getStoreConfig('payment/iugu_cc/cctypes');
        if ($availableTypes) {
            $availableTypes = explode(',', $availableTypes);
            foreach ($types as $code=>$name) {
                if (!in_array($code, $availableTypes)) {
                    unset($types[$code]);
                }
            }
        }
        return $types;
    }

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

    public function getCcYears()
    {
        $years = $this->getData('cc_years');
        if (is_null($years)) {
            $years = Mage::getSingleton('payment/config')->getYears();
            $years = array(0=>$this->__('Year'))+$years;
            $this->setData('cc_years', $years);
        }
        return $years;
    }

    public function getSavePaymentMethodUrl()
    {
        return $this->getUrl('iugu/payment_method/save');
    }

    public function getBackUrl()
    {
        return $this->getUrl('iugu/payment_method');
    }
}
