<?php
namespace Sbmedical\ExportCustomColumn\Plugin\Admin\Order;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;
use Magento\User\Model\ResourceModel\User\Collection as UserCollection;

class Grid extends \Magento\Framework\Data\Collection
{
    protected $coreResource;

    protected $adminUsers;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        ResourceConnection $coreResource,
        UserCollection $adminUsers
    ) {
        parent::__construct($entityFactory);
        $this->coreResource = $coreResource;
        $this->adminUsers = $adminUsers;
    }

    public function beforeLoad($printQuery = false, $logQuery = false)
    {
        if ($printQuery instanceof Collection) {
            $collection = $printQuery;

            $joined_tables = array_keys(
                $collection->getSelect()->getPart('from')
            );
            
            
            $tableName = $collection->getTable('sales_order_duxdetail');
            $tableName2 = $collection->getTable('sales_invoice_grid');
                $collection->getSelect()
                    ->columns(
                        array(
                            'notes' => new \Zend_Db_Expr('(SELECT GROUP_CONCAT(`notes` SEPARATOR " & ") FROM `'.$tableName.'` WHERE `'.$tableName.'`.`order_id` = main_table.`entity_id` GROUP BY `'.$tableName.'`.`order_id`)'),
                            'invoice_statuses' => new \Zend_Db_Expr('(SELECT REPLACE(GROUP_CONCAT(CONCAT(`increment_id`, ") ", `state`) SEPARATOR ". "), ") 2", ") Paid")  FROM `'.$tableName2.'` WHERE `'.$tableName2.'`.`order_id` = main_table.`entity_id` GROUP BY `'.$tableName2.'`.`order_id`)')

                        )
                    );

        }
    }
}