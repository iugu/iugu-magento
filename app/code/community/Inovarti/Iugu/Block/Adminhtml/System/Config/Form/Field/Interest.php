<?php
/**
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Iugu_Block_Adminhtml_System_Config_Form_Field_Interest
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        $this->addColumn('installments', array(
            'label' => Mage::helper('iugu')->__('Up To'),
            'style' => 'width:120px',
        ));

        $this->addColumn('interest', array(
            'label' => Mage::helper('iugu')->__('Interest Rate'),
            'style' => 'width:120px',
        ));

        $this->_addAfter = false;

        parent::__construct();
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }

        $column     = $this->_columns[$columnName];
        $elementName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
        $extraParams = ' data-value="#{' . $columnName . '}" ' .
            (isset($column['style']) ? ' style="'.$column['style'] . '"' : '');

        if ($columnName == 'installments') {
            return $this->getInstallmentsSelectHtml($elementName, $extraParams);
        }
        return parent::_renderCellTemplate($columnName);
    }

    /**
     * @param $name
     * @param $extraParams
     * @return string
     */
    public function getInstallmentsSelectHtml($name, $extraParams)
    {
        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setName($name)
            ->setClass('select-installments')
            ->setExtraParams($extraParams)
            ->setOptions(Mage::getSingleton('iugu/source_installment')->toOptionArray());

        return $select->getHtml();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $html .= Mage::helper('adminhtml/js')->getScript(
            "$$('.select-installments').each(function(el){ el.value = el.readAttribute('data-value'); });\n"
        );

        return $html;
    }
}
