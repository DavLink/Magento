<?php
namespace Sbmedical\TrackingAgent\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddAgent implements ObserverInterface
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
        $agent = $this->_request->getPost('agent');
        try {
            $trackId = $observer->getEvent()->getTrack()->getEntityId();
            $logger->info('Eliminados: Agent es '.$agent.' Track_id es '. $trackId);

            $tableName = $this->_resource->getTableName('tracking_agent');

            $connection= $this->_resource->getConnection();
            
            $agentToSave = $agent;		
            $query = "INSERT INTO $tableName (id, entity_track_id, agent) VALUES (0,$trackId,'$agentToSave')";
            $connection->query($query);        
        } catch (\Exception $e) {
            $logger->info('Excepción: '.  $e->getMessage());
        }
    }
}