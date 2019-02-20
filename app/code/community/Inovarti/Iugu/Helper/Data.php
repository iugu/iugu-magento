<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Iugu_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_SETTINGS_MODE            = 'payment/iugu_settings/mode';
    const XML_PATH_SETTINGS_ACCOUNT_ID      = 'payment/iugu_settings/account_id';

    public function getMode()
    {
        $mode = Mage::getStoreConfig(self::XML_PATH_SETTINGS_MODE);
        return $mode;
    }

    public function getAccountId()
    {
        $accountId = Mage::getStoreConfig(self::XML_PATH_SETTINGS_ACCOUNT_ID);
        return $accountId;
    }

    public function getApiToken()
    {
        $apiToken = Mage::getStoreConfig('payment/iugu_settings/api_token_' . $this->getMode());
        return $apiToken;
    }

    public function getPhonePrefix($telephone)
    {
        $telephone = Zend_Filter::filterStatic($telephone, 'Digits');
        $prefix = substr($telephone, 0, 2);
        return $prefix;
    }

    public function getPhone($telephone)
    {
        $telephone = Zend_Filter::filterStatic($telephone, 'Digits');
        $phone = substr($telephone, 2);
        return $phone;
    }

    public function formatAmount($amount)
    {
        return number_format($amount, 2, '', '');
    }

    public function getItemsFromOrder($order)
    {
        $items = array();
        foreach ($order->getAllVisibleItems() as $data) {
            $item = new Varien_Object();
            $item->setDescription($data->getName());
            $item->setQuantity($data->getQtyOrdered());
            $item->setPriceCents($this->formatAmount($data->getBasePrice()));
            $items[] = $item;
        }

        // Shipping
        if ($order->getBaseShippingAmount() > 0) {
            $item = new Varien_Object();
            $item->setDescription($this->__('Shipping & Handling') . ' (' . $order->getShippingDescription() . ')');
            $item->setQuantity(1);
            $item->setPriceCents($this->formatAmount($order->getBaseShippingAmount()));
            $items[] = $item;
        }

        return $items;
    }

    public function getPayerInfoFromOrder($order)
    {
        $billingAddress = $order->getBillingAddress();

        //payer.address can't be blank fixed, we use 4 street fields, so our district is the 4, if you use less, i haven't tested 
        $address = new Varien_Object();
        $address->setStreet($billingAddress->getStreet(1));
        $address->setNumber($billingAddress->getStreet(2));
        if (!empty($billingAddress->getStreet(4)))
            $address->setDistrict($billingAddress->getStreet(4));
        else
            $address->setDistrict($billingAddress->getStreet(3));
        $address->setCity($billingAddress->getCity());
        $address->setState($billingAddress->getRegionCode());
        $address->setCountry('Brasil');
        $address->setZipCode(Zend_Filter::filterStatic($billingAddress->getPostcode(), 'Digits'));

        $payer = new Varien_Object();
        $payer->setPhonePrefix($this->getPhonePrefix($billingAddress->getTelephone()));
        $payer->setPhone($this->getPhone($billingAddress->getTelephone()));
        $payer->setEmail($order->getCustomerEmail());
        $payer->setAddress($address);
        Mage::dispatchEvent('iugu_get_payer_info_from_order_after', array('order' => $order, 'payer_info' => $payer));

        return $payer;
    }

    public function getCustomerId($createIfNotExists = true)
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customer->getIuguCustomerId()) {
            // Verify if customer really exists and try create again
            $result = Mage::getSingleton('iugu/api')->getCustomer($customer->getIuguCustomerId());
            if (!$result->getId()) {
                $customer->setIuguCustomerId('');
                $customer->save();
                return $this->getCustomerId();
            }
        } elseif ($createIfNotExists) {
            $customerData = new Varien_Object();
            $customerData->setEmail($customer->getEmail());
            $customerData->setName($customer->getName());
            $customerData->setNotes(Mage::app()->getWebsite()->getName());
            try {
                $result = Mage::getSingleton('iugu/api')->saveCustomer($customerData);
                $customer->setIuguCustomerId($result->getId());
                $customer->save();
            } catch(Exception $e) {
                Mage::throwException($e->getMessage());
            }
        }
        return $customer->getIuguCustomerId();
    }
}
