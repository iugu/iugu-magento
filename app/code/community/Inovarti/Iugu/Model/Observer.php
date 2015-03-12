<?php
/**
 * @category    Inovarti
 * @package     Inovarti_Iugu
 * @copyright   Copyright (c) 2014 Inovarti. (http://www.inovarti.com.br)
 */

class Inovarti_Iugu_Model_Observer
{
    public function addJs(Varien_Event_Observer $observer)
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getEvent()->getBlock();
        $blockType = $block->getType();
        $targetBlocks = array(
            'checkout/onepage_payment',
            'aw_onestepcheckout/onestep_form_paymentmethod',
        );
        if (in_array($blockType, $targetBlocks) && Mage::getStoreConfig('payment/iugu_cc/active')) {
            /** @var $transport Varien_Object */
            $transport = $observer->getEvent()->getTransport();
            $html = $transport->getHtml();
            $preHtml = $block->getLayout()
                ->createBlock('core/template')
                ->setTemplate('iugu/checkout/payment/js.phtml')
                ->toHtml();
            $transport->setHtml($preHtml . $html);
        }
    }

    public function addTotal(Varien_Event_Observer $observer)
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Checkout_Block_Onepage_Review_Info) {
            /** @var $transport Varien_Object */
            $transport = $observer->getEvent()->getTransport();
            $reviewHtml = $transport->getHtml();
            $totalHtml = $block->getLayout()
                ->createBlock('iugu/checkout_cart_total')
                ->toHtml();

            $html = str_replace('</tfoot>', $totalHtml . '</tfoot>', $reviewHtml);
            $transport->setHtml($html);
        }
    }
}
