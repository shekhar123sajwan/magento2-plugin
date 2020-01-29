<?php

namespace Ehub\VillageVoice\Block; 
use \Magento\Store\Model\StoreManagerInterface;
use \Ehub\VillageVoice\Model\Videos ;
use \Ehub\VillageVoice\Helper\Data as vv_helper; 
use \Ehub\VillageVoice\Controller\User\Follow ;
use  \Magento\Customer\Model\Session ;
use Ehub\VillageVoice\Helper\Category as CatHelper;
use Ehub\VillageVoice\Model\Tags;
use Magento\Framework\Filesystem;

class SearchResults extends \Magento\Framework\View\Element\Template {

    protected $_categoryFactory;
	
	protected $_query ;
	
	protected $_search_for;
    
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Ehub\VillageVoice\Helper\Data $helperData,
     \Ehub\VillageVoice\Model\Category $category,
     \Ehub\VillageVoice\Helper\Category $categoryHelper,
    \Ehub\VillageVoice\Model\Videos $videos, 
     StoreManagerInterface $storeManager,
    \Magento\Framework\Registry $registry,
	\Magento\Customer\Model\Customer $vuser,
	\Magento\Customer\Model\CustomerFactory $customerFactory,
    vv_helper $vvhelper,
    Follow $follow,
    Session $customer,
	CatHelper $vv_cathelper,
	Tags $tags,
	Filesystem $_fileSystem
    ) {
        parent::__construct($context);
        $this->helperData = $helperData;
        $this->_categoryFactory = $category;
        $this->_categoryHelper = $categoryHelper;
        $this->_videosFactory = $videos;
        $this->_storeManager =  $storeManager;
        $this->_registry = $registry;
        $this->_videos = $videos;
        $this->_vvhelper = $vvhelper;
        $this->_follow = $follow; 
        $this->_customers = $customer;
		$this->_customer = $vuser;
		$this->vv_cat_helper = $vv_cathelper;
		$this->_customerFactory = $customerFactory;
		$this->_tags = $tags;
		$this->_fileSystem = $_fileSystem;

    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
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

    public function getAccountLoginUrl() {

      return $this->_storeManager->getStore()->getBaseUrl().'customer/account/login';

     } 
 

    public function isUserLogin() {

      if ($this->_customers->isLoggedIn()) {
       // Customer is logged in 
       return  $this->_customers->getCustomerId();  // get Customer Id

      }  

       return null;
    }

    public function getVideoUrl($urlKey) {
        $currentStore = $this->_storeManager->getStore(); 
        $baseUrl = $currentStore->getBaseUrl(); 
        $videoUrl = $baseUrl.'voice/video/'.$urlKey;
        return $videoUrl; 
    }

    public function getVideoUserUrl($user_id) {

     return $this->vv_cat_helper->getVideoUserUrl($user_id); 

    }	

	public function getUserProfile($u_id) { 
		$collection = $this->_customerFactory->create(); 
		$avator = $collection->load($u_id)->getData('profile_picture');
		
		if(isset($avator) && !empty($avator) ) {
			return $avator;
		}
		return null;
	}
	
    public function getSearchResults() {
		
		$data = [];
		
 		$this->_query = $this->getRequest()->getParam('q');
		$this->_search_for = $this->getRequest()->getParam('search_for');
		
         if( !empty($this->_search_for) && trim($this->_search_for) == 'videos' ) {	 
           $data = $this->fetchVideoData(); 
		   return $data;
		 }
		 
		 if( !empty($this->_search_for) && trim($this->_search_for) == 'pro' ) {	 
		   $data = $this->fetchProfileData(); 
		   return $data; 
		 } 
		 
		 if( !empty($this->_search_for) && trim($this->_search_for) == 'v_tag' ) {	 
		   $data = $this->fetchVideoDataByTags();   
		   return $data; 
		 }		 

        return $data;
    }
	
	public function getCustomerdatabyId($id) {
		 $collection = $this->_customerFactory->create();
		 
		 $customer = $collection->load($id)->getData('firstname');
		 
         return $customer; //Customer data by customer ID
		 
	}
	
    public function fetchVideoData() {
		
		$total_videos = 0;
 
        $total_vi_show_page = 15; 
		
        $current_page = ($this->getRequest()->getParam('page'))? $this->getRequest()->getParam('page') : 1;
		
        $collection = $this->_videos->getVideos();

        $collection->addFieldToFilter('v_status', 1);
		
		$collection->addFieldToFilter('v_user_status', 1);
    
        $collection->addFieldToFilter('v_title', array('like' => '%' . trim($this->_query) . '%')); 
		
        $total_videos = $collection->getSize();
			
		$total_pages = ceil($total_videos / $total_vi_show_page); 
		
        $limit = ( $current_page - 1 ) * $total_vi_show_page;	
		
		$collection = $this->_videos->getVideos();
		
		$collection->addFieldToFilter('v_status', 1);
		
		$collection->addFieldToFilter('v_user_status', 1); 

        $collection->addFieldToFilter('v_title', array('like' => '%' . trim($this->_query) . '%'));
		
		$collection->setPageSize($total_vi_show_page);
		
		$collection->setCurPage($limit);
		
		$data = $collection->getData();
		
		return ['s_result' => $data, 's_for' => 'videos', 'total_videos'=> $total_videos , 'current_page' => $current_page,   'total_pages' => $total_pages, 'records_per_page' => $total_vi_show_page ] ;
    }
	
	public function  fetchProfileData() {
	
		$total_pro = 0;
 
        $total_pro_show_page = 15; 
		
        $current_page = ($this->getRequest()->getParam('page'))? $this->getRequest()->getParam('page') : 1;
		
		$collection = $this->_customerFactory->create()->getCollection();
		

        $collection->addAttributeToSelect("*") 
                   ->addAttributeToFilter( array(
                                                 array('attribute' => 'firstname','like' => "%". trim($this->_query) . "%"), 
 												 array('attribute' => 'lastname','like' => "%". trim($this->_query) . "%")
												 )
										 )
				   ->setOrder('entity_id', 'DESC')
				   ->addFieldToFilter('is_vv_user', 1) 
                   ->load();
				   
	    $total_pro = $collection->getSize();
				   
		$total_pages = ceil($total_pro / $total_pro_show_page); 
		
        $limit = ( $current_page - 1 ) * $total_pro_show_page;	

		$collection = $this->_customerFactory->create()->getCollection();

        $collection->addAttributeToSelect("*")
                   ->addAttributeToFilter( array(
                                                 array('attribute' => 'firstname','like' => "%". trim($this->_query) . "%"), 
 												 array('attribute' => 'lastname','like' => "%". trim($this->_query) . "%"), 
												)
										 )
				   ->addFieldToFilter('is_vv_user', 1) ;
					
		$collection->setPageSize($total_pro_show_page);
		
		$collection->setCurPage($limit)
                    ->setOrder('entity_id', 'DESC')		
                	->load();
		
		$data = $collection->getData();
		
		return ['s_result' => $data, 's_for' => 'pro', 'total_videos'=> $total_pro , 'current_page' => $current_page,   'total_pages' => $total_pages, 'records_per_page' => $total_pro_show_page ] ; 
	}
	
	public function  fetchVideoDataByTags() {
	
		$total_pro = 0;
 
        $total_pro_show_page = 15; 
		
        $current_page = ($this->getRequest()->getParam('page'))? $this->getRequest()->getParam('page') : 1;		
		
		$collection_size = $this->_tags->getTotalVByTagName($this->_query);
				   
	    $total_pro = $collection_size; 
				   
		$total_pages = ceil($total_pro / $total_pro_show_page); 
		
        $limit = ( $current_page - 1 ) * $total_pro_show_page;	

		$data = $this->_tags->getVByTagName($this->_query,$total_pro_show_page, $limit);
		
		return ['s_result' => $data, 's_for' => 'v_tag', 'total_videos'=> $total_pro , 'current_page' => $current_page,   'total_pages' => $total_pages, 'records_per_page' => $total_pro_show_page ] ; 
	}	
	
    public function renderPagination($total_pages,$current_page,$records_per_page,$current_url) {

         $currentPage = $current_page; 
         $totalPages = $total_pages; 
         $pageLinks = 7;

        if ($totalPages <= 1)
        {
            return NULL;
        }

        $html = '<ul class="pagination">';

        $leeway = floor($pageLinks / 2);

        $firstPage = $currentPage - $leeway;  
        $lastPage = $currentPage + $leeway;   


        if ($firstPage < 1)
        {
            $lastPage += 1 - $firstPage;  
            $firstPage = 1;   
        }

        if ($lastPage > $totalPages)
        {
            $firstPage -= $lastPage - $totalPages;
            $lastPage = $totalPages;
        }
        if ($firstPage < 1)
        {
            $firstPage = 1;
        }

        if ($firstPage != 1)
        {
            $html .= '<li class="first"><a href="'.$current_url.'&page=1" title="Page 1">1</a></li>';
            $html .= '<li class="page dots"><span>...</span></li>';
        }

        for ($i = $firstPage; $i <= $lastPage; $i++)
        {
            if ($i == $currentPage)
            {
                $html .= '<li class="page current"><span>' . $i . '</span></li>';
            }
            else
            {
                $html .= '<li class="page"><a href="'.$current_url.'&page=' . $i . '" title="Page ' . $i . '">' . $i . '</a></li>';
            }
        }

        if ($lastPage != $totalPages)
        {
            $html .= '<li class="page dots"><span>...</span></li>';
            $html .= '<li class="last"><a href="'.$current_url.'&page=' . $totalPages . '" title="Page ' . $totalPages . '">' . $totalPages . '</a></li>';
        }

        $html .= '</ul>';

        return $html ;


    }
	
}
