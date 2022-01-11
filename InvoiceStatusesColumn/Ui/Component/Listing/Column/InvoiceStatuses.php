<?php
namespace Sbmedical\InvoiceStatusesColumn\Ui\Component\Listing\Column;

use \Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Registry;
use Magento\Framework\App\ResourceConnection;

class InvoiceStatuses extends Column
{
	protected $_resource;
	protected $_orderRepository;

	public function __construct(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		ResourceConnection $resource,
		OrderRepositoryInterface $orderRepository,
		array $components = [],
		array $data = []
		)
	{
		$this->_orderRepository = $orderRepository;
		$this->_resource = $resource;
		parent::__construct($context, $uiComponentFactory, $components, $data);
	}

	public function prepareDataSource(array $dataSource)
	{
		if(isset($dataSource['data']['items']))
		{
			foreach ($dataSource['data']['items'] as & $item) {
				
				$order  = $this->_orderRepository->get($item["entity_id"]);
                $invoice_details = $order->getInvoiceCollection();
                $invoice_status = '';
                $invoice_counter = 1;
                foreach ($invoice_details as $invoice) {
                    $invoice_status = $invoice_status . $invoice_counter . ') ' . $invoice->getStateName() . "\r\n";
                    $invoice_counter++;
                }
                $item[$this->getData('name')] = $invoice_status;

			}
		}
		return $dataSource;
	}
}
?>