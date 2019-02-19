<?php
/**
 * Zodinet
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Zodinet.com license that is
 * available through the world-wide-web at this URL:
 * https://www.zodinet.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Zodinet
 * @package     Zodinet_GiaoHangNhanh
 * @copyright   Copyright (c) 2012 Zodinet (https://www.zodinet.com/)
 * @license     https://www.zodinet.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create rewardpoints table and fields
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('momo/queue')};

CREATE TABLE {$this->getTable('momo/queue')} (
    `id` int(10) unsigned NOT NULL auto_increment,
    `request_id` varchar(20) NOT NULL,
    `order_id` varchar(20) NOT NULL,
    `created_time` datetime NULL,
    `update_time` datetime NULL,
    `extra_data` text NULL,
    `status` varchar(10) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup();

