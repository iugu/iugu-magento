<?php
/**
 * @category    Inovarti
 * @package     Inovarti_Iugu
 * @copyright   Copyright (c) 2014 Inovarti. (http://www.inovarti.com.br)
 */
class Inovarti_Iugu_Model_Source_Installment
{
    public function toOptionArray()
    {
        $options = array();
        for ($i=2; $i <= 12; $i++) {
            $options[] = array('value' => $i, 'label' => $i . 'x');
        }
        return $options;
    }
}
