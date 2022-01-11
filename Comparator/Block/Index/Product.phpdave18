<?php
namespace Sbmedical\Comparator\Block;

use Magento\Catalog\Api\ProductRepositoryInterface;
class Product extends \Magento\Framework\View\Element\Template
{
    protected $productRepository; 
    protected $_storeManager; 

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository;
    }
    public function getProduct($productId)
    {
        return $this->productRepository->getById($productId);
    }
}