<?php

    namespace Sbmedical\Comparator\Controller\Index;

    class Index extends \Magento\Framework\App\Action\Action
    {
        protected $_resultPageFactory;

        public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory
        )
        {
            $this->_resultPageFactory = $resultPageFactory;
            parent::__construct($context);
        }

        public function execute()
        {
            $this->_resultPageFactory->create();
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        }

        
    }
