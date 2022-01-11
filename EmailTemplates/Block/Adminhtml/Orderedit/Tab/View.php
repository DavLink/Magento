<?php
namespace Sbmedical\EmailTemplates\Block\Adminhtml\Orderedit\Tab;
 
class View extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_template = 'tab/view/customemail.phtml';
    protected $formkey;
 
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\Form\FormKey $formkey,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->formkey = $formkey;
        parent::__construct($context, $data);
    }
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }
    public function getOrderId()
    {
        return $this->getOrder()->getEntityId();
    }
    public function getOrderIncrementId()
    {
        return $this->getOrder()->getIncrementId();
    }
    public function getTabLabel()
    {
        return __('Custom Email');
    }
    public function getTabTitle()
    {
        return __('Custom Email');
    }
    public function canShowTab()
    {
        return true;
    }
    public function isHidden()
    {
        return false;
    }
    public function formkey()
    {
        return $this->formkey->getFormKey();
    }
    protected function _prepareLayout()
	{
		
		$onclick = "submitAndReloadArea($('sales_order_view_tabs_custom_email_content').parentNode, '" . $this->getSubmitUrl() . "')";
		$button = $this->getLayout()->createBlock(
			\Magento\Backend\Block\Widget\Button::class
			)->setData(
				['label' => __('Send Email'), 'class'=>'action-save action-primary', 'onclick' => $onclick]
			);
			$this->setChild('submit_button', $button);
		return parent::_prepareLayout();
	}
    public function getSubmitUrl()
	{
		return $this->getUrl('sbmedical_emailtemplates/index/index/');
	}
}