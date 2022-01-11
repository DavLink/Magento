<?php
namespace Sbmedical\TrackingAgent\Observer;
use Magento\Framework\Event\ObserverInterface;

class RemoveAgent implements ObserverInterface
{
    protected $_request;
    protected $_resource;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        $this->_request = $request;
        $this->_resource = $resource;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/trackingAgent.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        try {
            $trackId = $observer->getEvent()->getTrack()->getEntityId();

            $tableName = $this->_resource->getTableName('tracking_agent');

            $connection= $this->_resource->getConnection();
            	
            $select = "SELECT agent FROM $tableName WHERE entity_track_id = $trackId";
            $row = $connection->fetchOne($select);	
            $query = "DELETE FROM $tableName WHERE entity_track_id = $trackId";
            $connection->query($query);        
        } catch (\Exception $e) {
            $logger->info('Excepción: '.  $e->getMessage());
        }
    }
}