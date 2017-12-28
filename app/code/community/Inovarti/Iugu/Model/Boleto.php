<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Iugu_Model_Boleto extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'iugu_boleto';

    protected $_formBlockType = 'iugu/form_boleto';
    protected $_infoBlockType = 'iugu/info_boleto';

    protected $_isGateway                   = true;
    protected $_canUseForMultishipping      = false;
    protected $_isInitializeNeeded          = true;

    public function assignData($data)
    {
        $info = $this->getInfoInstance();
        $info->setInstallments(null)
            ->setInstallmentDescription(null)
        ;
        return $this;
    }

    public function initialize($paymentAction, $stateObject)
    {
        $payment = $this->getInfoInstance();
        $order = $payment->getOrder();
        $this->_place($payment, $order->getBaseTotalDue());
        return $this;
    }

    public function _place(Mage_Sales_Model_Order_Payment $payment, $amount)
    {
        $order = $payment->getOrder();
        $items = Mage::helper('iugu')->getItemsFromOrder($payment->getOrder());
        $payer = Mage::helper('iugu')->getPayerInfoFromOrder($payment->getOrder());

        $data = new Varien_Object();
        $data->setMethod(Inovarti_Iugu_Model_Api::PAYMENT_METHOD_BOLETO)
            ->setEmail($order->getCustomerEmail())
            ->setItems($items)
            ->setPayer($payer)
            ->setNotificationUrl(Mage::getUrl('iugu/notification'));

        // Discount
        if ($order->getBaseDiscountAmount()) {
            $data->setDiscountCents(Mage::helper('iugu')->formatAmount(abs($order->getBaseDiscountAmount())));
        }

        // Tax
        if ($order->getBaseTaxAmount()) {
            $data->setTaxCents($this->formatAmount($order->getBaseTaxAmount()));
        }

        /**
        *
        * Altera a data de vencimento do boleto para 1 dia útil após a data atual.
        * Boleto utiliza o método Mage::getSingleton('iugu/api')->charge($data) para criar o boleto IUGU.
        * O parâmetro necessário é o bank_slip_extra_days.
        *
        */
        $data->setBankSlipExtraDays($this->somaDiasUteis((int)$this->getConfigData('due_date')));

        $result = Mage::getSingleton('iugu/api')->charge($data);
        if ($result->getErrors()) {
            $messages = array();
            if (is_array($result->getErrors())) {
                foreach ($result->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        $messages[] = $field . ': ' . $error . '.';
                    }
                }
            } else {
                $messages[] = $result->getErrors();
            }
            Mage::throwException(implode("\n", $messages));
        }

        // iugu info
        $payment->setIuguInvoiceId($result->getInvoiceId())
            ->setIuguUrl($result->getUrl())
            ->setIuguPdf($result->getPdf())
        ;

        return $this;
    }

    /**
    * Soma a quantidade de dias úteis
    *
    */
    private function somaDiasUteis($intDia) {
        $intDias = $intDia;
        for($i=1; $i<=$intDia; $i++) {
            $dataAtual = mktime(0, 0, 0, date('m'), date('d') + $i, date('Y'));
            if(date("N", $dataAtual)=="6") {
                $intDias+=2;
            } else if(date("N", $dataAtual)=="7") {
                $intDias++;
            }
        }
        $intDia=$intDias;
        for($i=1; $i<=$intDia; $i++) {
            $dataAtual = mktime(0, 0, 0, date('m'), date('d') + $i, date('Y'));
            if(date("N", $dataAtual)<=5) {
                if($this->diaFeriado($i)) {
                    $intDias++;
                }
            }
        }
        return $intDias;
    }

    /**
    * Calcula os feriados
    *
    */
    private function diaFeriado($intDias) {
        $intDia = mktime(0, 0, 0, date('m'), date('d') + $intDias, date('Y'));
        $ano    = intval(date('Y', $intDia));

        $pascoa     = easter_date($ano); // Limite de 1970 ou após 2037 da easter_date PHP consulta http://www.php.net/manual/pt_BR/function.easter-date.php
        $dia_pascoa = date('j', $pascoa);
        $mes_pascoa = date('n', $pascoa);
        $ano_pascoa = date('Y', $pascoa);
        $feriados = array(
        // Tatas Fixas dos feriados Nacionail Basileiras
        mktime(0, 0, 0, 1,  1,   $ano), // Confraternização Universal - Lei nº 662, de 06/04/49
        mktime(0, 0, 0, 4,  21,  $ano), // Tiradentes - Lei nº 662, de 06/04/49
        mktime(0, 0, 0, 5,  1,   $ano), // Dia do Trabalhador - Lei nº 662, de 06/04/49
        mktime(0, 0, 0, 9,  7,   $ano), // Dia da Independência - Lei nº 662, de 06/04/49
        mktime(0, 0, 0, 10,  12, $ano), // N. S. Aparecida - Lei nº 6802, de 30/06/80
        mktime(0, 0, 0, 11,  2,  $ano), // Todos os santos - Lei nº 662, de 06/04/49
        mktime(0, 0, 0, 11, 15,  $ano), // Proclamação da republica - Lei nº 662, de 06/04/49
        mktime(0, 0, 0, 12, 25,  $ano), // Natal - Lei nº 662, de 06/04/49

        // These days have a date depending on easter
        mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 48,  $ano_pascoa),//2ºferia Carnaval
        mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 47,  $ano_pascoa),//3ºferia Carnaval 
        mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 2 ,  $ano_pascoa),//6ºfeira Santa  
        mktime(0, 0, 0, $mes_pascoa, $dia_pascoa     ,  $ano_pascoa),//Pascoa
        mktime(0, 0, 0, $mes_pascoa, $dia_pascoa + 60,  $ano_pascoa),//Corpus Cirist
        );

        return in_array($intDia, $feriados);
    }
}