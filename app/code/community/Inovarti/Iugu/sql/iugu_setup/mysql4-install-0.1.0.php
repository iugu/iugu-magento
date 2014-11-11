<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

// Quote Payment
$entity = 'quote_payment';
$attributes = array(
    'iugu_token'                => array('type' => Varien_Db_Ddl_Table::TYPE_TEXT),
    'iugu_save'                 => array('type' => Varien_Db_Ddl_Table::TYPE_SMALLINT),
    'installments'              => array('type' => Varien_Db_Ddl_Table::TYPE_SMALLINT),
    'installment_description'   => array('type' => Varien_Db_Ddl_Table::TYPE_VARCHAR),
);

foreach ($attributes as $attribute => $options) {
    $installer->addAttribute($entity, $attribute, $options);
}

// Order Payment
$entity = 'order_payment';
$attributes = array(
    'iugu_invoice_id'           => array('type' => Varien_Db_Ddl_Table::TYPE_VARCHAR),
    'iugu_url'                  => array('type' => Varien_Db_Ddl_Table::TYPE_VARCHAR),
    'iugu_pdf'                  => array('type' => Varien_Db_Ddl_Table::TYPE_VARCHAR),
    'installments'              => array('type' => Varien_Db_Ddl_Table::TYPE_SMALLINT),
    'installment_description'   => array('type' => Varien_Db_Ddl_Table::TYPE_VARCHAR),
);

foreach ($attributes as $attribute => $options) {
    $installer->addAttribute($entity, $attribute, $options);
}

$installer->endSetup();
