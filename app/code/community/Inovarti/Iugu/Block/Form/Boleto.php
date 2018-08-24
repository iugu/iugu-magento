<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Iugu_Block_Form_Boleto extends Mage_Payment_Block_Form
{
    protected $_instructions;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('iugu/form/boleto.phtml');
    }

    public function getInstructions()
    {
        if (is_null($this->_instructions)) {
            $this->_instructions = $this->getMethod()->getConfigData('instructions');
        }
        return $this->_instructions;
    }

    public function getName()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return implode(' ', array($customer->getFirstname(), $customer->getMiddlename(), $customer->getLastname()));
    }

    public function getCpfCnpj()
    {
        $customer_session = Mage::getSingleton('customer/session')->getCustomer();
        $customer = Mage::getModel('customer/customer')->load($customer_session->getId());
        return $customer->getData('taxvat');
    }
}
