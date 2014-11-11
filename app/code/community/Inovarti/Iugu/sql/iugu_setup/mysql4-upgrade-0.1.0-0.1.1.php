<?php
/**
 * @category    Inovarti
 * @package     Inovarti_Iugu
 * @copyright   Copyright (c) 2014 Inovarti. (http://www.inovarti.com.br)
 */

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$this->startSetup();

$installer->addAttribute('customer', 'iugu_customer_id', array(
    'type'     => 'varchar',
    'input'    => 'hidden',
    'visible'  => false,
    'required' => false
));

$this->endSetup();
