<?php

namespace Ehub\VillageVoice\Block;

use \Magento\Customer\Model\Customer;
use \Magento\Store\Model\StoreManagerInterface;
use Ehub\VillageVoice\Helper\Category as CatHelper;
use Magento\Framework\Filesystem;

class Voice extends \Magento\Framework\View\Element\Template {

    protected $_categoryFactory;
    
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Ehub\VillageVoice\Helper\Data $helperData,
    \Ehub\VillageVoice\Model\Category $category, 
	\Ehub\VillageVoice\Helper\Category $categoryHelper, 
	\Ehub\VillageVoice\Model\Videos $videos,
    Customer $customer, 
	StoreManagerInterface $storeManager,
	CatHelper $vv_cathelper,
	\Magento\Customer\Model\CustomerFactory $customerFactory,
	Filesystem $_fileSystem
    ) {
        parent::__construct($context);
        $this->helperData = $helperData;
        $this->_categoryFactory = $category;
        $this->_categoryHelper = $categoryHelper;
        $this->_videosFactory = $videos;
        $this->_customers = $customer;
        $this->_storeManager =  $storeManager;
		$this->vv_cat_helper = $vv_cathelper;
        $this->_customerFactory = $customerFactory;	
        $this->_fileSystem = $_fileSystem;		
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        //if(!$this->getStatus()){
        //    $this->setTemplate('Ehub_VillageVoice::module_disabled.phtml');
        //}
        return $this;
    }

    public function getStatus() {
        return $this->helperData->getGeneralConfig('enable');
    }

    public function getAllCategories(){
        $collection = $this->_categoryFactory->getCollection();
        $collection->addFieldToFilter('c_status', 1);
        return $collection->getData();
    }
    
    public function getCategoryImage($banner){
        return $this->_categoryHelper->getCategoryBanner($banner);
    }

    public function getFeaturedVidoes() {

       $featuredvidoes =  $this->_videosFactory->getFeaturedVideos();
       return $featuredvidoes;
    }

// vidoes banner
    public function getBannerUrl($banner) { 
        $currentStore = $this->_storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);  
    
        $ext = pathinfo($banner, PATHINFO_EXTENSION);
 
        $file_name = pathinfo($banner, PATHINFO_FILENAME); 

        $thumbnail = "-". User::VV_IMG_WIDTH."x". User::VV_IMG_HEIGHT. ".".$ext;
		
        $imageResized = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('vv_banners/videos/').$file_name.$thumbnail;

        if (file_exists($imageResized)) { // Only resize image if not already exists.
		
		  return $mediaUrl.'vv_banners/videos/'.$file_name.$thumbnail;
		  
		}
		
        return $mediaUrl.'vv_banners/videos/'.$banner;
    }
	
	public function getCatUrl($urlKey) {
	    $currentStore = $this->_storeManager->getStore(); 
        $baseUrl = $currentStore->getBaseUrl(); 
		$catUrl = $baseUrl.'voice/category/'.$urlKey;
        return $catUrl;	
	}
	
	public function getVideoUrl($urlKey) {
        $currentStore = $this->_storeManager->getStore(); 
        $baseUrl = $currentStore->getBaseUrl(); 
        $vUrl = $baseUrl.'voice/video/'.$urlKey;
        return $vUrl; 
    }
	
    public function getVideoUserUrl($user_id) {

     return $this->vv_cat_helper->getVideoUserUrl($user_id); 

    }	
	
    public function getUserProfile($u_id)  {
	   
	    $user_collection =  $this->_customerFactory->create();

        $user = $user_collection->load($u_id);
		
		return $user->getData('profile_picture');
    }	
   
    public function getLiveUsers() {
	   
	   $connection = $this->helperData->_getWriteAdapter();

       $tablename = $connection->getTableName('vv_user_live');

       $sql = "SELECT * FROM " . $tablename . " LIMIT 4";
		    	  
	   $result = $connection->fetchAll($sql);
	   		  
	   return $result;			 
	   
    }

}
