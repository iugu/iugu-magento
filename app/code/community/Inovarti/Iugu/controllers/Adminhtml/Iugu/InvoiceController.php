<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */

class Inovarti_Iugu_Adminhtml_Iugu_InvoiceController extends Mage_Adminhtml_Controller_Action
{
    public function viewAction()
    {
        $id = $this->getRequest()->getParam('id');
        $iugu = Mage::getModel('iugu/api');
        $result = array();
        $result['success'] = false;
        try {
            $invoice = $iugu->fetch($id);
            if ($invoice->getId()) {
                $result['content_html'] = $this->_getInvoiceHtml($invoice);
                $result['success'] = true;
            } else {
                Mage::throwException($invoice->getErrors());
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error_message'] = $e->getMessage();
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _getInvoiceHtml($invoice)
    {
        $this->loadLayout();
        $blockType = $invoice->getBankSlip() ? 'boleto' : 'cc';
        $block = $this->getLayout()->createBlock('iugu/adminhtml_invoice_view_'. $blockType);
        $block->setInvoice($invoice);
        return $block->toHtml();
    }
}
