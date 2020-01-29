<?php

namespace Ehub\VillageVoice\Block;
use Ehub\VillageVoice\Model\Videos;
use \Magento\Framework\App\ResourceConnection; 
use \Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use \Magento\User\Model\User;
use Ehub\VillageVoice\Helper\Category as CatHelper;
use Magento\Framework\Filesystem;

class Video extends \Magento\Framework\View\Element\Template {

    protected $_categoryFactory;
 
    
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Ehub\VillageVoice\Helper\Data $helperData, \Ehub\VillageVoice\Model\Category $category, \Ehub\VillageVoice\Helper\Category $categoryHelper,
        Videos $videos,
        ResourceConnection $resource,
        \Magento\Framework\Registry $registry,
        ObjectManagerInterface $objectmanager,
        StoreManagerInterface $StoreManagerInterface,
		\Magento\Customer\Model\Customer $customer,
        User $user,
		CatHelper $vv_cathelper,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		Filesystem $_fileSystem
		
    ) {
        parent::__construct($context);
        $this->helperData = $helperData;
        $this->_categoryFactory = $category;
        $this->_categoryHelper = $categoryHelper; 
        $this->_videoFactory = $videos;
        $this->_resource = $resource;
        $this->_registry = $registry;
        $this->_objectManager = $objectmanager;
        $this->_storeManagerInterface =  $StoreManagerInterface;
        $this->_userFactory = $user;
		$this->_customer = $customer;
		$this->vv_cat_helper = $vv_cathelper;
		$this->_customerFactory = $customerFactory;	
		$this->_fileSystem = $_fileSystem;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout(); 
          $this->setTemplate('Ehub_VillageVoice::category/video.phtml');
        
        return $this;
    }


    public function getVideos() {
 
        $videos = $this->fetchPageData();

        return $videos;
    }

    public function renderPagination($total_pages,$current_page,$records_per_page) {

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
            $html .= '<li class="first"><a href="?page=1" title="Page 1">1</a></li>';
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
                $html .= '<li class="page"><a href="?page=' . $i . '" title="Page ' . $i . '">' . $i . '</a></li>';
            }
        }

        if ($lastPage != $totalPages)
        {
            $html .= '<li class="page dots"><span>...</span></li>';
            $html .= '<li class="last"><a href="?page=' . $totalPages . '" title="Page ' . $totalPages . '">' . $totalPages . '</a></li>';
        }

        $html .= '</ul>';

        return $html ;

 
    }

    public function fetchPageData() {

        $videos = [];

        //get pagination page
        $current_page =($this->getRequest()->getParam('page'))? $this->getRequest()->getParam('page') : 1;


        $current_category = $this->_registry->registry('vv_current_category');

        $collection = $this->_videoFactory->getVideos(); 


        $c_id = $current_category->getData('c_id');

        $c_url_key = $current_category->getData('c_url_key');


        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

        $vi2cat = $connection->getTableName('vv_video_category'); 


        $currentStore = $this->_storeManagerInterface->getStore();

        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'/vv_banners/videos/';


        $collection->getSelect()->joinInner( array('vi2cat' => $vi2cat),
                                            'main_table.v_id = vi2cat.v_id'
                                          ) 
                               ->where('vi2cat.c_id=?',  $c_id)
                               ->where('main_table.v_status=?','1')
							   ->where('main_table.v_user_status=?','1');

          $total_videos = $collection->count();

          $total_vi_show_page = 15;

          $total_pages = ceil($total_videos / $total_vi_show_page); 

          $limit = ( $current_page - 1 ) * $total_vi_show_page;


          $collection = $this->_videoFactory->getVideos(); 

          $collection->getSelect()->joinInner( array('vi2cat' => $vi2cat),
                                              'main_table.v_id = vi2cat.v_id'
                                            ) 
                                 ->where('vi2cat.c_id=?',  $c_id)
                                 ->where('main_table.v_status=?','1')
								 ->where('main_table.v_user_status=?','1')
                                 ->limit($total_vi_show_page, $limit);
	  

         if($collection->getData()) {
            foreach ($collection->getData() as $key => $v_data) { 
                $userId = $v_data['v_user_id'];
                $videos[] = [ 'v_id' => $v_data['v_id'], 
				              'v_url_key' => $v_data['v_url_key'],
                              'v_url' => $v_data['v_url'],
                              'v_banner_url' => $this->getBannerUrl($v_data['v_banner']),
                              'v_title' => $v_data['v_title'],
                              'v_user_name' => ucwords($v_data['firstname']),
							  'v_user_id'   => $userId
                            ] ; 
            }

           } 



        return ['videos' => $videos, 'total_videos'=> $total_videos , 'current_page' => $current_page,   'total_pages' => $total_pages, 'c_url_key' => $c_url_key, 'records_per_page' => $total_vi_show_page ];

    }
	
	 public function getVideoUrl($urlKey) {
        $currentStore = $this->_storeManagerInterface->getStore(); 
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
   
    // vidoes banner
    public function getBannerUrl($banner) { 
	  
	   
        $currentStore = $this->_storeManagerInterface->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA); 

        $ext = pathinfo($banner, PATHINFO_EXTENSION);
 
        $file_name = pathinfo($banner, PATHINFO_FILENAME); 

        $thumbnail = "-". \Ehub\VillageVoice\Block\User::VV_IMG_WIDTH."x". \Ehub\VillageVoice\Block\User::VV_IMG_HEIGHT. ".".$ext;
		
        $imageResized = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('vv_banners/videos/').$file_name.$thumbnail;

        if (file_exists($imageResized)) { // Only resize image if not already exists.
		
		  return $mediaUrl.'vv_banners/videos/'.$file_name.$thumbnail;
		  
		}
		
        return $mediaUrl.'vv_banners/videos/'.$banner;
    }   

   
}
