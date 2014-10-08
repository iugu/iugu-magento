<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Iugu_Model_Source_Status
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Inovarti_Iugu_Model_Api::INVOICE_STATUS_DRAFT,
                'label' => Mage::helper('iugu')->__('Draft')
            ),
            array(
                'value' => Inovarti_Iugu_Model_Api::INVOICE_STATUS_PENDING,
                'label' => Mage::helper('iugu')->__('Pending')
            ),
            array(
                'value' => Inovarti_Iugu_Model_Api::INVOICE_STATUS_PARTIALLY_PAID,
                'label' => Mage::helper('iugu')->__('Partially Paid')
            ),
            array(
                'value' => Inovarti_Iugu_Model_Api::INVOICE_STATUS_PAID,
                'label' => Mage::helper('iugu')->__('Paid')
            ),
            array(
                'value' => Inovarti_Iugu_Model_Api::INVOICE_STATUS_CANCELED,
                'label' => Mage::helper('iugu')->__('Canceled')
            ),
            array(
                'value' => Inovarti_Iugu_Model_Api::INVOICE_STATUS_REFUNDED,
                'label' => Mage::helper('iugu')->__('Refunded')
            ),
            array(
                'value' => Inovarti_Iugu_Model_Api::INVOICE_STATUS_EXPIRED,
                'label' => Mage::helper('iugu')->__('Expired')
            ),
        );
    }

    public function getOptionLabel($value)
    {
        foreach ($this->toOptionArray() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
