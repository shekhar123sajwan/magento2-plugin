<?php

namespace Ehub\VillageVoice\Block\Register;

class Form extends \Magento\Customer\Block\Form\Register {

    protected $_categoryFactory;
    
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $moduleManager,
                $customerSession,
                $customerUrl
        );
        
        $this->urlBuilder = $context->getUrlBuilder();
        
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->urlBuilder->getUrl('voice/account/createpost');
    }
}
