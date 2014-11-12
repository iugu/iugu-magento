<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Iugu_Model_Api_Abstract
{
    const VERSION   = '1';
    const ENDPOINT  = 'https://api.iugu.com';

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
     * Send the HTTP request and return an HTTP response object
     *
     * @param string $url
     * @param Varien_Object $data
     * @param string $method
     * @return Varien_Object
     */
    public function request($url, $data=null, $method='GET')
    {
        $config = array(
            'adapter' => 'Zend_Http_Client_Adapter_Curl',
            'timeout' => 120
        );
        $client = new Zend_Http_Client($url, $config);
        $client->setAuth($this->getApiToken());
        $client->setMethod($method);
        if (!$data) {
            $data = new Varien_Object();
        }
        if (in_array($method, array(Zend_Http_Client::POST, Zend_Http_Client::PUT, Zend_Http_Client::DELETE))) {
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
     * Convert an Array to Varien_Object
     *
     * @param array
     * @return Varien_Object
     */
    protected function _parseObject(array $data)
    {
        $object = new Varien_Object();
        if ($this->_isAssoc($data)) {
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
        } else {
            $items = array();
            foreach ($data as $itemKey => $itemValue) {
                $items[$itemKey] = $this->_parseObject($itemValue);
            }
            $object->setData('items', $items);
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
