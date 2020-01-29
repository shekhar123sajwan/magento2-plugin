<?php

namespace Ehub\VillageVoice\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper {

    const XML_PATH_VILLAGEVOICE = 'voice/';
    
    protected $_resource;
    
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
    }

    public function getConfigValue($field, $userId = null) {
        return $this->scopeConfig->getValue(
                        $field, ScopeInterface::SCOPE_STORE, $userId
        );
    }

    public function getGeneralConfig($code, $userId = null) {
        return $this->getConfigValue(self::XML_PATH_VILLAGEVOICE . 'general/' . $code, $userId);
    }

    public function getBanner($banner_name = '', $folder = 'category') {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $media_url = $objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        if (!empty($banner_name)) {
            if ($this->CheckBannerExist($banner_name, $folder)) {
                return $media_url . 'vv_banners/'.$folder.'/' . $banner_name;
            } else {
                return false;
            }
        }

        return $media_url . 'vv_banners/' . $banner_name;
    }
    
    public function CheckBannerExist($banner_file, $folder = 'category') {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        
        $file_path = $directory->getPath('media').'/vv_banners/'.$folder.'/'.$banner_file;
        
        if(file_exists($file_path)){
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @return type
     */
    public function _getWriteAdapter()
    {
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        
        return $connection;
    }
}
    