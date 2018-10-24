<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Iugu_Block_Info_Boleto extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('iugu/info/boleto.phtml');
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
    public function getInvoiceUrl()
    {
        return $this->getInfo()->getIuguPdf();
    }
}
