<?php

namespace Netpower\Momo\Setup;

use \Magento\Framework\DB\Ddl\Table;
use \Magento\Framework\Setup\InstallSchemaInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.3') < 0) {
            $installer = $setup;
            $installer->startSetup();
            if (!$installer->tableExists('sales_order_momo')) {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('sales_order_momo')
                )
                    ->addColumn(
                        'entity_id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'nullable' => false,
                            'primary' => true,
                            'unsigned' => true,
                        ],
                        'Entity ID auto increment'
                    )->addColumn(
                    'momo_id',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false,
                        'unique' => true,
                    ],
                    'requestId, OrderId both are momo_id'
                )->addColumn(
                    'status',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Payment Status'
                )->addColumn(
                    'pay_type',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Payment Type Momo'
                )->addColumn(
                    'transaction_id',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Transaction ID from MoMo response'
                )->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false,
                        'unsigned' => true,
                    ],
                    'Order ID from sales_order table entity_ID'
                )->addForeignKey(
                    $installer->getFkName('sales_order_momo', 'order_id', 'sales_order', 'entity_id'),
                    'order_id',
                    $installer->getTable('sales_order'),
                    'entity_id',
                    Table::ACTION_CASCADE
                );
                $installer->getConnection()->createTable($table);
            }

            if (!$installer->tableExists('sales_order_momo_queue')) {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('sales_order_momo_queue')
                )
                    ->addColumn(
                        'id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'unsigned' => true,
                            'nullable' => false,
                            'primary' => true,
                        ],
                        'ID'
                    )
                    ->addColumn(
                        'request_id',
                        Table::TYPE_TEXT,
                        null,
                        ['nullable' => false, 'default' => ''],
                        'request_id'
                    )
                    ->addColumn(
                        'order_id',
                        Table::TYPE_TEXT,
                        null,
                        ['nullable' => false, 'default' => ''],
                        'order_id'
                    )
                    ->addColumn(
                        'status',
                        Table::TYPE_TEXT,
                        null,
                        ['nullable' => false, 'default' => ''],
                        'status'
					)
					->addColumn(
                        'error',
                        Table::TYPE_TEXT,
                        null,
                        ['nullable' => false, 'default' => ''],
                        'error'
                    )
                    ->addColumn(
                        'sales_order_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false,
                            'unsigned' => true,
                        ],
                        'Order ID from sales_order table entity_ID'
                    )
                    ->addForeignKey(
                        $installer->getFkName('sales_order_momo_queue', 'sales_order_id', 'sales_order', 'entity_id'),
                        'sales_order_id',
                        $installer->getTable('sales_order'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    );
                $installer->getConnection()->createTable($table);
            }
            $installer->endSetup();
        }
    }
}
