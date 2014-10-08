<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Iugu_Block_Checkout_Success_Payment_Default extends Mage_Core_Block_Template
{
    public function setPayment(Varien_Object $payment)
    {
        $this->setData('payment', $payment);
        return $this;
    }

    public function getPayment()
    {
        return $this->_getData('payment');
    }

    public function getOrder()
    {
        return $this->getPayment()->getOrder();
    }
}
