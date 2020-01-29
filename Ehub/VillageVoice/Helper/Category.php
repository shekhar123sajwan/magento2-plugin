<?php

namespace Ehub\VillageVoice\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Category extends AbstractHelper {

    public function getCategoryBanner($banner_name = '') {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $media_url = $objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        if (!empty($banner_name)) { 
            if ($this->CheckBannerExist($banner_name)) {
                return $media_url . 'vv_banners/category/' . $banner_name;
            } else {
                return false;
            }
        }

        return $media_url . 'banners/' . $banner_name;
    }
    
    public function CheckBannerExist($banner_file) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');		
		
        $file_path = $directory->getPath('media').'/vv_banners/category/'.$banner_file;
        
        if(file_exists($file_path)){
            return true;
        }
        return false;
    }
	
    public function getVideoUserUrl ($user_id) {

        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        //instance of\Magento\Framework\App\ObjectManager
        $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 

        $currentStore = $storeManager->getStore();

        $baseUrl = $currentStore->getBaseUrl();

        return $baseUrl.'voice/user/'.$user_id;
    }	
}
    