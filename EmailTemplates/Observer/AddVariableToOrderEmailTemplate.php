<?php
namespace Sbmedical\EmailTemplates\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Newsletter\Model\Subscriber;

class AddVariableToOrderEmailTemplate implements ObserverInterface
{
    protected $_subscriber;

    public function __construct(
        Subscriber $subscriber
    ) {
        $this->_subscriber= $subscriber;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\App\Action\Action $controller */
        $transport = $observer->getTransport();
        $order = $transport->getOrder();
        $email = $order->getCustomerEmail();
        $subscriber = $this->_subscriber->loadByEmail($email);
        $code = $subscriber->getData('subscriber_confirm_code');
        $id = $subscriber->getData('subscriber_id');
        $link = "newsletter/subscriber/unsubscribe/id/$id/code/$code/";
        $transport['SubscriberLink'] = $link;
    }
}