<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Iugu_Block_Checkout_Success_Payment_Boleto extends Inovarti_Iugu_Block_Checkout_Success_Payment_Default
{
    public function getBoletoUrl()
    {
        return $this->getPayment()->getIuguBoletoPdf();
    }
}
