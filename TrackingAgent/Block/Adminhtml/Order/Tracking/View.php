<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Invoice tracking control form
 */
namespace Sbmedical\TrackingAgent\Block\Adminhtml\Order\Tracking;

/**
 * @api
 * @since 100.0.2
 */
class View extends \Magento\Shipping\Block\Adminhtml\Order\Tracking\View
{
    protected $_authSession;
    protected $_resource;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->_authSession = $authSession;
        $this->_resource = $resource;
        parent::__construct($context, $shippingConfig, $registry,$carrierFactory, $data);
    }

    public function getCurrentUser()
    {
        return $this->_authSession->getUser()->getUsername();
    }
    public function getAgent($trackId){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/trackingAgent.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        try{
        $tableName = $this->_resource->getTableName('tracking_agent');
        $connection= $this->_resource->getConnection();
        $select = "SELECT agent FROM ".$tableName." WHERE entity_track_id = ".$trackId."";
        $row = $connection->fetchOne($select);	
            
        return trim($row);
        } catch (\Exception $e) {
            $logger->info('Excepción: '.  $e->getMessage());
        }
    }
}
