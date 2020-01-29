<?php

namespace Ehub\Catalog\Helper;

use Magento\Framework\App\Action\Action;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Design package instance
     *
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $_design;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_design = $design;
        $this->_localeDate = $localeDate;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Return HTML
     */
    public function getSellerHtml($_product)
    {
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create('Webkul\Marketplace\Helper\Data');
	$sellerId = '';
	$marketplaceProduct = $helper->getSellerProductDataByProductId($_product['entity_id']);
	foreach ($marketplaceProduct as $value) {
	    $sellerId = $value['seller_id'];
	}
	$sellerInfo = $helper->getSellerInfo($sellerId);
        $shopTitle = $sellerInfo['shop_title'];
        $shopUrl = $sellerInfo['shop_url'];
        if (!$shopTitle) {
            $shopTitle = $shopUrl;
        }
	$collectionPageUrl = $helper->getRewriteUrl('marketplace/seller/collection/shop/'.$shopUrl);
	$style = '';
	if(empty($shopTitle)){
		$style="visibility:hidden;";
	}
        $html = '<div class="wk-seller-card-row" style="'.$style.'"><span class="wk-block-font-bold-up">'.__('Sold By').'</span>';
        $html .= '<span class="wk-block-title-css">
                <a href="'.$collectionPageUrl.'" title="'.__('Visit Shop').'" id="profileconnect" target="_blank">'.$shopTitle.'</a></span></div>';
        return $html;
    }

}
