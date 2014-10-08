<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */

abstract class Inovarti_Iugu_Block_Adminhtml_Invoice_View_Abstract extends Mage_Adminhtml_Block_Template
{
    protected $_viewBlockType;
    protected $_invoice;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('iugu/invoice/view/' . $this->_viewBlockType . '.phtml');
    }

    public function getInvoice()
    {
        return $this->_invoice;
    }

    public function setInvoice($invoice)
    {
        $this->_invoice = $invoice;
    }

    public function getDueDate()
    {
        return date('d/m/Y', strtotime($this->getInvoice()->getDueDate()));
    }

    public function getStatusLabel()
    {
        return Mage::getModel('iugu/source_status')->getOptionLabel($this->getInvoice()->getStatus());
    }

    public function getTotal()
    {
        return $this->getInvoice()->getTotal();
    }
}
