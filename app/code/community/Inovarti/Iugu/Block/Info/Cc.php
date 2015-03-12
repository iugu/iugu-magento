<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Iugu_Block_Info_Cc extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('iugu/info/cc.phtml');
    }

    /**
     * @return string
     */
    public function getInvoiceId()
    {
        return $this->getInfo()->getIuguInvoiceId();
    }

    /**
     * @return string
     */
    public function getInstallments()
    {
        return $this->getInfo()->getInstallments();
    }

    /**
     * @return string
     */
    public function getInstallmentDescription()
    {
        return $this->getInfo()->getInstallmentDescription();
    }

    /**
     * @return float
     */
    public function getAmountOrdered()
    {
        return $this->getInfo()->getBaseAmountOrdered();
    }

    /**
     * @return float
     */
    public function getTotalWithInterest()
    {
        return $this->getInfo()->getIuguTotalWithInterest();
    }
}
