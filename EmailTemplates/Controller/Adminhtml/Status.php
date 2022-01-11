<?php
namespace Sbmedical\EmailTemplates\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Magento\Theme\Block\Html\Header\Logo as Logo;
use Psr\Log\LoggerInterface;


class Status extends \Magento\Sales\Controller\Adminhtml\Order\View
{
    protected $scopeConfig;
    protected $emailLogo;
    protected $messageManager;
    protected $emulation;

    public function __construct(
        Logo $emailLogo,
        \Magento\Store\Model\App\Emulation $emulation, 
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order $order,
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->order = $order;
        $this->emailLogo = $emailLogo;
        $this->messageManager = $messageManager;
        $this->urlBuilder = $urlBuilder;
        $this->emulation = $emulation;
        /* 
        $this->request->setParam('form_key', $this->formKey->getFormKey()); */
        parent::__construct($context, $coreRegistry, $fileFactory, $translateInline, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultRawFactory, $orderManagement, $orderRepository, $logger);
    }
    public function execute()
    {
        // Recibe el cambio de status
        $newStatus = $this->getRequest()->getParam('custom_status');

        // Recibe el contenido del email
        $body = $this->getRequest()->getParam('email_body');
        $subject = $this->getRequest()->getParam('email_subject');
        $customerEmail = $this->getRequest()->getParam('customer_email');
        $customerName = $this->getRequest()->getParam('customer_name');
        $copymail = 'sales@sbmedical.com';
         
        $id = $this->getRequest()->getParam('order_id');
        try {
            $order = $this->orderRepository->get($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        //Cookies para mostrar notificaciones despues de recargar
        if (!strpos($this->urlBuilder->getCurrentUrl(), '?') && !isset($_COOKIE["sb"])){
            setcookie("sb", 0);
        }
        else if ($_COOKIE["sb"]==1){
            $this->messageManager->addSuccess(__("Email sent!"));
            setcookie("sb", 0);
        }
        else if ($_COOKIE["sb"]==2){
            $this->messageManager->addSuccess(__("Order Status has been changed!"));
            setcookie("sb", 0);
        }
        //Cambio de status
        if (isset($newStatus) && $_COOKIE["sb"]==0){
            switch ($newStatus) {
                case "default":
                    $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true);
                    $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                    $order->addStatusToHistory($order->getStatus(), 'Order processed successfully with reference');
                    break;
                case "backorder":
                    $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true);
                    $order->setStatus("backorder");
                    $order->addStatusToHistory($order->getStatus(), 'Order processed successfully with reference');
                    break;
                case "special":
                    $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true);
                    $order->setStatus("special");
                    $order->addStatusToHistory($order->getStatus(), 'Order processed successfully with reference');
                    break;
            }
            
            try {
                $order->save();
                setcookie("sb", 2);
                $this->_redirect(strtok($this->urlBuilder->getCurrentUrl(), '?'));
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, $e->getMessage());
            }
        }
        
        
        if (isset($body))
            $bodyHtml = $this->createEmail($body);
        //Envío del email
        if (isset($bodyHtml) && isset($customerEmail) && isset($customerName) && $_COOKIE["sb"]==0){
            try{
                $email = new \Zend_Mail('utf-8');
                $email->setSubject($subject); 
                $email->setBodyHtml($bodyHtml);
                $email->setFrom($this->getStoreEmail(), $this->getStorename());
                $email->addTo($customerEmail, $customerName);
                $email->addTo($copymail, $customerName);
                
                unset($bodyHtml);
                try{
                    $email->send();
                    setcookie("sb", 1);
                    $this->_redirect(strtok($this->urlBuilder->getCurrentUrl(), '?'));   
                }catch (\Exception $e){
                    $this->messageManager->addError(__("There was an error sending the email"));
                }
                
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, $e->getMessage());
            }
        }
        
        
        return parent::execute();
    }
    public function getStoreEmail()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getStorename()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    //Función para estilizar el email
    protected function createEmail($body){
        //Emulation sirve para tomar el logo de frontend
        $this->emulation->startEnvironmentEmulation(1, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $css = "<style>.email-container{font-family: Helvetica,Arial,sans-serif; vertical-align: top; background-color: #fff; padding: 25px; font-size: 14px; white-space: pre-wrap;} .email-image{font-family: Helvetica,Arial,sans-serif; vertical-align: top; background-color: #fff; padding: 25px; font-size: 14px} </style>";
        $logo = "<img class = 'email-image' src=\"".$this->emailLogo->getLogoSrc()."\" alt=\"Logo\" width=\"auto\" height=\"auto\" />";
        $topPart = "<div class = 'email-container'>";
        $bottomPart = "</div>";
        $this->emulation->stopEnvironmentEmulation();

        return $css.$logo.$topPart.$body.$bottomPart;
    }
} 