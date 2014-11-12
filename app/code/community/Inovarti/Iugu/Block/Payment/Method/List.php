<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Iugu_Block_Payment_Method_List extends Mage_Core_Block_Template
{
    protected $_items;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('iugu/payment_method/list.phtml');
    }

    public function getAddPaymentMethodUrl()
    {
        return $this->getUrl('iugu/payment_method/new');
    }

    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }

    public function getItems()
    {
        if (is_null($this->_items)) {
            $customer = $this->_getSession()->getCustomer();
            if ($customer->getIuguCustomerId()) {
                $result = Mage::getSingleton('iugu/api')->getPaymentMethodList($customer->getIuguCustomerId());
                if ($result->getItems()) {
                    $this->_items = $result->getItems();
                }
            }
            if (!$this->_items) {
                $this->_items = array();
            }
        }
        return $this->_items;
    }

    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
}
