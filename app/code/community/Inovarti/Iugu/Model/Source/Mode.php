<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Iugu_Model_Source_Mode
{
    const MODE_TEST = 'test';
    const MODE_LIVE = 'live';

    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::MODE_TEST,
                'label' => Mage::helper('iugu')->__('Test')
            ),
            array(
                'value' => self::MODE_LIVE,
                'label' => Mage::helper('iugu')->__('Live')
            ),
        );
    }
}
