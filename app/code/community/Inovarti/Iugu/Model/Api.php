<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Iugu_Model_Api
{
    const VERSION   = '1';
    const ENDPOINT  = 'https://api.iugu.com';

    const PAYMENT_METHOD_BOLETO = 'bank_slip';

    const INVOICE_STATUS_DRAFT              = 'draft';
    const INVOICE_STATUS_PENDING            = 'pending';
    const INVOICE_STATUS_PARTIALLY_PAID     = 'partially_paid';
    const INVOICE_STATUS_PAID               = 'paid';
    const INVOICE_STATUS_CANCELED           = 'canceled';
    const INVOICE_STATUS_REFUNDED           = 'refunded';
    const INVOICE_STATUS_EXPIRED            = 'expired';

    protected $_apiToken;

    public function __construct()
    {
        $this->_apiToken = Mage::helper('iugu')->getApiToken();
    }

    /**
     * Set API Token
     *
     * @param string $token
     * @return Inovarti_Iugu_Model_Api
     */
    public function setApiToken($token)
    {
        $this->_apiToken = $token;
        return $this;
    }

    /**
     * Get API Token
     *
     * @return string
     */
    public function getApiToken()
    {
        if (!$this->_apiToken) {
            Mage::throwException(Mage::helper('iugu')->__('You need to configure API Token before performing requests.'));
        }
        return $this->_apiToken;
    }

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
     * @param int @id
     * @return Varien_Object
     */
    public function fetch($id)
    {
        $response = $this->request($this->getInvoiceUrl($id));
        return $response;
    }

    /**
     * Send the HTTP request and return an HTTP response object
     *
     * @param string $url
     * @param Varien_Object $data
     * @param string $method
     * @return Varien_Object
     */
    public function request($url, $data=null, $method='GET')
    {
        $client = new Varien_Http_Client($url, array('timeout'  => 120));
        $client->setAuth($this->getApiToken());
        $client->setMethod($method);
        if (!$data) {
            $data = new Varien_Object();
        }
        if ($method == Zend_Http_Client::POST) {
            // Fix: items[0] -> items[]
            $rawData = http_build_query($this->_parseArray($data));
            $rawData = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $rawData);
            $client->setRawData($rawData);
        } else {
            $client->setParameterGet($this->_parseArray($data));
        }
        $response = $client->request();
        $body = json_decode($response->getBody(), true);
        $result = $this->_parseObject($body);
        return $result;
    }

    /**
     * Retrieve base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $url = self::ENDPOINT . '/v' . self::VERSION;
        return $url;
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
     * Convert an Array to Varien_Object
     *
     * @param array
     * @return Varien_Object
     */
    protected function _parseObject(array $data)
    {
        $object = new Varien_Object();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($this->_isAssoc($value)) {
                    $object->setData($key, $this->_parseObject($value));
                } else {
                    $items = array();
                    foreach ($value as $itemKey => $itemValue) {
                        $items[$itemKey] = $this->_parseObject($itemValue);
                    }
                    $object->setData($key, $items);
                }
            } else {
                $object->setData($key, $value);
            }
        }
        return $object;
    }

    /**
     * Convert a Varien_Object to Array
     *
     * @param Varien_Object
     * @return array
     */
    protected function _parseArray(Varien_Object $object)
    {
        $array = array();
        foreach ($object->getData() as $key => $value) {
            if ($value instanceof Varien_Object) {
                $array[$key] = $this->_parseArray($value);
            } elseif (is_array($value)) {
                $items = array();
                foreach ($value as $itemKey => $itemValue) {
                    if ($itemValue instanceof Varien_Object) {
                        $items[$itemKey] = $this->_parseArray($itemValue);
                    } else {
                        $items[$itemKey] = $itemValue;
                    }
                }
                $array[$key] = $items;
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    }

    /**
     * Check if array is associative or sequential
     *
     * @param array $array
     * @return bool
     */
    protected function _isAssoc($array) {
      return (bool)count(array_filter(array_keys($array), 'is_string'));
    }
}
