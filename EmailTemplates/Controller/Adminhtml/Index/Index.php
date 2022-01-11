<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Sbmedical\EmailTemplates\Controller\Adminhtml\Index;

class Index extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;
    protected $jsonHelper;
    protected $messageManager;
    protected $emulation;
    protected $scopeConfig;
    protected $transportBuilder;
    protected $resultFactory;
    protected $orderRepository;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Store\Model\App\Emulation $emulation, 
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Theme\Block\Html\Header\Logo $emailLogo,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->messageManager = $messageManager;
        $this->emulation = $emulation;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->emailLogo = $emailLogo;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->resultFactory = $resultFactory;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $post = $this->getRequest()->getParams();
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/emailtemplates.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        /* $logger->info("Controller Funcionando!"); */

        // Recibe el contenido del email
        $body = $this->getRequest()->getParam('email_body');
        $subject = $this->getRequest()->getParam('email_subject');
        $customerEmail = $this->getRequest()->getParam('customer_email');
        $customerName = $this->getRequest()->getParam('customer_name');
        $copymail = 'sales@sbmedical.com';
        $customerName = $this->getRequest()->getParam('customer_name');

        $id = $this->getRequest()->getParam('order_id');

        if(isset($id)){ 
            $order = $this->getOrder($id);
            $storeId = $order->getStoreId();
        }
        else{
            $storeId = 1;
        }
        $storeName = $this->getStorename($storeId);
        $storeEmail = $this->getStoreEmail($storeId);

        if (isset($body))
            $bodyHtml = $this->createEmail($body, $storeId);
        //Envío del email
        if (isset($bodyHtml) && isset($customerEmail) && isset($customerName)){
            try{
                $report = [
                    'subject' => $subject,
                    'body' => $bodyHtml
                ];

                /* $logger->info("Construye el mensaje"); */
                $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($report);
                $transport = $this->transportBuilder
                        ->setTemplateIdentifier('custom_email')
                        ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_ADMINHTML, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
                        ->setTemplateVars(['data' => $postObject])
                        ->setFrom(['name' => $storeName,'email' => $storeEmail])
                        ->addTo($customerEmail, $customerName)
                        ->addBcc($copymail)
                        ->getTransport();
                
                unset($bodyHtml);
                try{
                    $transport->sendMessage();
                    $logger->info("Envio del mensaje");
                    try {
                        return $this->jsonResponse('Sending Email...');
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        return $this->jsonResponse($e->getMessage());
                    } catch (\Exception $e) {
                        $this->logger->critical($e);
                        return $this->jsonResponse($e->getMessage());
                    }
                    $this->reload();
                }catch (\Exception $e){
                    $logger->info("Fallo el envio1 del mensaje");
                    $this->messageManager->addError(__("There was an error sending the email"));
                    $this->reload();
                }
                
            } catch (\Exception $e) {
                $this->messageManager->addError(__("There was an error sending the email"));
                $this->reload();
                $this->messageManager->addExceptionMessage($e, $e->getMessage());
            }
        }        
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function getOrder($id)
    {
        return $this->orderRepository->get($id);
    }

    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
    public function getStoreEmail($storeId)
    {
        $this->emulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $storeEmail = $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $this->emulation->stopEnvironmentEmulation();
        return $storeEmail;
    }
    public function getStorename($storeId)
    {
        $this->emulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $storeName = $this->scopeConfig->getValue(
            'trans_email/ident_sales/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $this->emulation->stopEnvironmentEmulation();
        return $storeName;
    }
    public function getStoreLogo($storeId)
    {
        $this->emulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $logo = $this->emailLogo->getLogoSrc();
        $this->emulation->stopEnvironmentEmulation();
        return $logo;
    }

    //Función para estilizar el email
    protected function createEmail($body, $storeId){
        //Emulation sirve para tomar el logo de frontend
        
        $logo = "<img class = 'email-image' src=\"".$this->getStoreLogo($storeId)."\" alt=\"Logo\" width=\"auto\" height=\"auto\" />";
        $topPart = "<div class = 'email-container'>";
        $bottomPart = "</div>";

        return $logo.$topPart.$this->nl2p($body).$bottomPart;
    }
    //Separa en parrafos los saltos de linea
    public function nl2p($string)
    {
        $paragraphs = '';

        foreach (explode("\n", $string) as $line) {
            if (trim($line)) {
                $paragraphs .= '<p>' . $line . '</p>';
            }
        }

        return $paragraphs;
    } 
    //Recarga despues de enviar el email
    public function reload(){
        echo "<script>window.location.reload(false);</script>";
    }
}

