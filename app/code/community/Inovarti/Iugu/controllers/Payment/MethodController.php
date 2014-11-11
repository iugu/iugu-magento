<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */

class Inovarti_Iugu_Payment_MethodController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        if ($block = $this->getLayout()->getBlock('iugu_payment_method_list')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(Mage::helper('iugu')->__('My Credit Cards'));
        }
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('iugu/payment_method');
        }
        $this->renderLayout();
    }

    public function saveAction()
    {
        $data = new Varien_Object($this->getRequest()->getParam('payment', array()));
        $data->setCustomerId(Mage::helper('iugu')->getCustomerId());
        $data->setDescription(Mage::getModel('core/date')->timestamp(time()));
        $iugu = Mage::getModel('iugu/api');
        $result = $iugu->savePaymentMethod($data);
        if ($result->getErrors()) {
            Mage::getSingleton('customer/session')->addError($this->__('An error occurred while saving the credit card.'));
        } else {
            Mage::getSingleton('customer/session')->addSuccess(Mage::helper('iugu')->__('Credit card has been saved.'));
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($paymentMethodId = $this->getRequest()->getParam('id')) {
            $customerId = Mage::helper('iugu')->getCustomerId();
            $iugu = Mage::getModel('iugu/api');
            $result = $iugu->deletePaymentMethod($customerId, $paymentMethodId);
            if ($result->getErrors()) {
                Mage::getSingleton('customer/session')->addError($this->__('An error occurred while deleting the credit card.'));
            } else {
                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('iugu')->__('Credit card has been deleted.'));
            }
        } else {
            Mage::getSingleton('customer/session')->addSuccess(Mage::helper('iugu')->__('Unable to find a credit card to delete.'));
        }
        $this->_redirect('*/*/');
    }
}
