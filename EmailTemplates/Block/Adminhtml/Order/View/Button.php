<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sbmedical\EmailTemplates\Block\Adminhtml\Order\View;

use Magento\Sales\Block\Adminhtml\Order\View as OrderView;
    
class Button extends \Magento\Sales\Block\Adminhtml\Order\View
{    

   protected function _construct()
    {
        parent::_construct();

        if(!$this->getOrderId()) {
            return $this;
        }
        
        $this->addButton(
            'order-custom-email',
            ['label' => __('Custom Email')]
        );
        
        return $this;
    }

}
