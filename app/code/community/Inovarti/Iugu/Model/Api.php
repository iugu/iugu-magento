<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Iugu_Model_Api extends Inovarti_Iugu_Model_Api_Abstract
{
    const PAYMENT_METHOD_BOLETO = 'bank_slip';
    const PAYMENT_METHOD_CREDIT_CARD = 'credit_card';

    const INVOICE_STATUS_DRAFT              = 'draft';
    const INVOICE_STATUS_PENDING            = 'pending';
    const INVOICE_STATUS_PARTIALLY_PAID     = 'partially_paid';
    const INVOICE_STATUS_PAID               = 'paid';
    const INVOICE_STATUS_CANCELED           = 'canceled';
    const INVOICE_STATUS_REFUNDED           = 'refunded';
    const INVOICE_STATUS_EXPIRED            = 'expired';

    /**
     * Authorize or Authorize and Capture
     *
     * @param Varien_Object $data
     * @return Varien_Object
     */
    public function charge(Varien_Object $data)
    {
        $response = $this->request($this->getChargeUrl(), $data, Zend_Http_Client::POST);
        return $response;
    }

    /**
     * Retrieve invoice info
     *
     * @param string @id
     * @return Varien_Object
     */
    public function fetch($id)
    {
        $response = $this->request($this->getInvoiceUrl($id));
        return $response;
    }

    /**
     * Refund a previously captured invoice
     *
     * @param string $id
     * @return Varien_Object
     */
    public function refund($id)
    {
        $response = $this->request($this->getRefundUrl($id), null, Zend_Http_Client::POST);
        return $response;
    }

    /**
     * Add new customer
     *
     * @param Varien_Object $data
     * @return Varien_Object
     */
    public function saveCustomer(Varien_Object $data)
    {
        $response = $this->request($this->getCustomerUrl(), $data, Zend_Http_Client::POST);
        return $response;
    }

    /**
     * Retrive customer
     *
     * @param string $id
     * @return Varien_Object
     */
    public function getCustomer($id)
    {
        $response = $this->request($this->getCustomerUrl($id));
        return $response;
    }

    /**
     * Add new payment method
     *
     * @param Varien_Object $data
     * @return Varien_Object
     */
    public function savePaymentMethod(Varien_Object $data)
    {
        $response = $this->request($this->getPaymentMethodUrl($data->getCustomerId()), $data, Zend_Http_Client::POST);
        return $response;
    }

    /**
     * Retrive payment method
     *
     * @param string $customerId
     * @param string $paymentMethodId
     * @return Varien_Object
     */
    public function getPaymentMethod($customerId, $paymentMethodId)
    {
        $response = $this->request($this->getPaymentMethodUrl($customerId, $paymentMethodId));
        return $response;
    }

    /**
     * Delete payment method
     *
     * @param string $customerId
     * @param string $paymentMethodId
     * @return Varien_Object
     */
    public function deletePaymentMethod($customerId, $paymentMethodId)
    {
        $response = $this->request($this->getPaymentMethodUrl($customerId, $paymentMethodId), null, Zend_Http_Client::DELETE);
        return $response;
    }

    /**
     * Retrive payment method list
     *
     * @param string $customerId
     * @return Varien_Object
     */
    public function getPaymentMethodList($customerId)
    {
        $response = $this->request($this->getPaymentMethodUrl($customerId));
        return $response;
    }

    /**
     * Retrieve charge URL
     *
     * @return string
     */
    public function getChargeUrl()
    {
        $url = $this->getBaseUrl() . '/charge';
        return $url;
    }

    /**
     * Retrieve invoice URL
     *
     * @param string $id
     * @return string
     */
    public function getInvoiceUrl($id)
    {
        $url = $this->getBaseUrl() . '/invoices/' . $id;
        return $url;
    }

    /**
     * Retrieve refund URL
     *
     * @param string $id
     * @return string
     */
    public function getRefundUrl($id)
    {
        $url = $this->getBaseUrl() . '/invoices/' . $id . '/refund';
        return $url;
    }

    /**
     * Retrive customer URL
     *
     * @param string $id
     * @return string
     */
    public function getCustomerUrl($id=null)
    {
        $url = $this->getBaseUrl() . '/customers';
        if ($id) {
            $url .= '/' . $id;
        }
        return $url;
    }

    /**
     * Retrive payment method URL
     *
     * @param string $customerId
     * @param string $paymentMethodId
     * @return string
     */
    public function getPaymentMethodUrl($customerId, $paymentMethodId=null)
    {
        $url = $this->getCustomerUrl($customerId) . '/payment_methods';
        if ($paymentMethodId) {
            $url .= '/' . $paymentMethodId;
        }
        return $url;
    }
}
