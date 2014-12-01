<?php
/**
 * @category    Inovarti
 * @package     Inovarti_Iugu
 * @copyright   Copyright (c) 2014 Inovarti. (http://www.inovarti.com.br)
 */
class Inovarti_Iugu_NotificationController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $event = $this->getRequest()->getParam('event');
        if ($event == 'invoice.status_changed') {
            $data = $this->getRequest()->getParam('data');
            $orderId = $this->_getOrderIdByIuguInvoiceId($data['id']);
            if (!$orderId) {
                $this->_forward('404');
            }
            $order = Mage::getModel('sales/order')->load($orderId);
            $iuguInvoice = Mage::getSingleton('iugu/api')->fetch($data['id']);
            if ($iuguInvoice->getStatus() == Inovarti_Iugu_Model_Api::INVOICE_STATUS_PAID) {
                if (!$order->canInvoice()) {
                    Mage::throwException($this->__('The order does not allow creating an invoice.'));
                }

                $invoice = Mage::getModel('sales/service_order', $order)
                    ->prepareInvoice()
                    ->register()
                    ->pay();

                $invoice->setEmailSent(true);
                $invoice->getOrder()->setIsInProcess(true);

                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder())
                    ->save();

                $invoice->sendEmail();

                $this->getResponse()->setBody('ok');
                return;
            }
        }
        $this->_forward('404');
    }

    protected function _getOrderIdByIuguInvoiceId($invoiceId)
    {
        $resource = Mage::getSingleton("core/resource");
        $connection = $resource->getConnection("core_write");
        $select = $connection->select()
            ->from(array('p' => $resource->getTableName('sales/order_payment')), array('parent_id'))
            ->where('iugu_invoice_id = ?', $invoiceId)
            ->limit(1);
        $orderId = $connection->fetchOne($select);
        return $orderId;
    }
}
