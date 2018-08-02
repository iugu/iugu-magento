<?php
/**
 * @category    Inovarti
 * @package     Inovarti_Iugu
 * @copyright   Copyright (c) 2014 Inovarti. (http://www.inovarti.com.br)
 */

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('quote_payment', 'iugu_boleto_name', array('type' => Varien_Db_Ddl_Table::TYPE_VARCHAR));
$installer->addAttribute('quote_payment', 'iugu_boleto_cpf_cnpj', array('type' => Varien_Db_Ddl_Table::TYPE_VARCHAR));

$installer->addAttribute('order_payment', 'iugu_boleto_name', array('type' => Varien_Db_Ddl_Table::TYPE_VARCHAR));
$installer->addAttribute('order_payment', 'iugu_boleto_cpf_cnpj', array('type' => Varien_Db_Ddl_Table::TYPE_VARCHAR));
$installer->addAttribute('order_payment', 'iugu_boleto_url', array('type' => Varien_Db_Ddl_Table::TYPE_VARCHAR));

$this->endSetup();
