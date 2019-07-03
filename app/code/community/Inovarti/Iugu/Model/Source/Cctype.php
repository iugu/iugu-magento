<?php
/**
 * @category    Inovarti
 * @package     Inovarti_Iugu
 * @copyright   Copyright (c) 2014 Inovarti. (http://www.inovarti.com.br)
 */
class Inovarti_Iugu_Model_Source_Cctype extends Mage_Payment_Model_Source_Cctype
{
    public function getAllowedTypes()
    {
        return array('VI', 'MC', 'AE', 'DC', 'EL');
    }

    public function getTypeByBrand($brand)
    {
        $brand = strtolower($brand);
        $data = array(
            'visa'          => 'VI',
            'mastercard'    => 'MC',
            'amex'          => 'AE',
            'diners'        => 'DC',
            'elo'           => 'EL',
        );

        $type = isset($data[$brand]) ? $data[$brand] : null;
        return $type;
    }
}
