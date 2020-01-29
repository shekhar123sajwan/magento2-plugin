<?php

namespace Ehub\VillageVoice\Block; 
use \Magento\Store\Model\StoreManagerInterface;
use \Ehub\VillageVoice\Model\Videos ;
use \Ehub\VillageVoice\Helper\Data as vv_helper; 
use \Ehub\VillageVoice\Controller\User\Follow ;
use  \Magento\Customer\Model\Session ;
use Ehub\VillageVoice\Helper\Category as CatHelper;
use Magento\Framework\Filesystem; 

class User extends \Magento\Framework\View\Element\Template {

    protected $_categoryFactory;

    protected static $_user_id ;
	
	const VV_IMG_WIDTH  = 263;
	
	const VV_IMG_HEIGHT  = 175;
    
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Ehub\VillageVoice\Helper\Data $helperData,
     \Ehub\VillageVoice\Model\Category $category,
     \Ehub\VillageVoice\Helper\Category $categoryHelper,
    \Ehub\VillageVoice\Model\Videos $videos, 
     StoreManagerInterface $storeManager,
    \Magento\Framework\Registry $registry,
	\Magento\Customer\Model\Customer $vuser,
    vv_helper $vvhelper,
    Follow $follow,
    Session $customer,
	CatHelper $vv_cathelper,
	\Magento\Customer\Model\CustomerFactory $customerFactory,
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
		$this->_fileSystem = $_fileSystem; 

    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }

    public function getUserData() {
      
        $current_user = $this->_registry->registry('user_data');

        return $current_user;
    }
	
	public function getUserByID($u_id) {
		return $this->_customerFactory->create()->load($u_id)->getData();
	} 

    public function countUserVideos($u_id) {

        $total_v = 0;

        self::$_user_id = $u_id;

        $collection = $this->_videos->getVideos();

        $collection->addFieldToFilter('entity_id', self::$_user_id);
		

        if($collection->count() && ( $this->isUserLogin() == self::$_user_id) ) {
          return $collection->count();
        }
		
		 if($collection->count()) {
			 
		    $collection = $this->_videos->getVideos();

            $collection->addFieldToFilter('entity_id', self::$_user_id);
			
			$collection->addFieldToFilter('v_status', '1');
			
			return $collection->count();
		 }

        return  $total_v;
    }


    public function getUserVideos() { 

        $total_v = 0;

        $collection = $this->_videos->getVideos();

        $collection->addFieldToFilter('entity_id', self::$_user_id);
		
		//$collection = $this->vv_users->getVideos();

        return  $collection->getData();
    }

     // vidoes banner
    public function getBannerUrl($banner) {  
	
        $currentStore = $this->_storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA); 
			
        $ext = pathinfo($banner, PATHINFO_EXTENSION);
 
        $file_name = pathinfo($banner, PATHINFO_FILENAME); 

        $thumbnail = "-". SELF::VV_IMG_WIDTH."x". SELF::VV_IMG_HEIGHT. ".".$ext;
		
        $imageResized = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('vv_banners/videos/').$file_name.$thumbnail;

        if (file_exists($imageResized)) { // Only resize image if not already exists.
		
		  return $mediaUrl.'vv_banners/videos/'.$file_name.$thumbnail;
		  
		}
		
        return $mediaUrl.'vv_banners/videos/'.$banner;
    }

    public function getAccountLoginUrl() {

      return $this->_storeManager->getStore()->getBaseUrl().'customer/account/login';

     } 

    public function getAjaxFollowUrl() {

       return $this->_storeManager->getStore()->getBaseUrl().'/voice/user/follow';

    }  

    public function isUserLogin() {

      if ($this->_customers->isLoggedIn()) {
       // Customer is logged in 
       return  $this->_customers->getCustomerId();  // get Customer Id

      }  

       return null;
    }


    public function isUserFollowing($v_user_id) {

        $follower_id = $this->isUserLogin();

        if(!empty($follower_id) && $follower_id !== '') { 


            $connection = $this->_vvhelper->_getWriteAdapter();

            $sql = 'select count(*) is_following from vv_followers where user_id = "'.$v_user_id.'" and follower_id = '.$follower_id.' ';
     

            if( $connection->fetchOne($sql)) {
            
              return true;
            }
          }

          return null;

    }


    public function countUserVideosLikes($v_user_id) {

        $total = 0;
 
        $connection = $this->_vvhelper->_getWriteAdapter();

        $sql = 'select count(*) total_likes from vv_video_like where v_user_id = "'.$v_user_id.'"';
     
        if($likes = $connection->fetchAll($sql)) {

           $total =  $likes[0]['total_likes'];

           return $total;
        } 

          return $total;

    }

     public function countUserFollowers($v_user_id) {

        $user_followers = 0;

        $connection = $this->_vvhelper->_getWriteAdapter();

        $tablename = $connection->getTableName('vv_followers');

        $sql = "SELECT  count(*) as total_followers FROM " . $tablename . " WHERE user_id = ". $v_user_id ." ";

        if($followers = $connection->fetchAll($sql)) {

           $user_followers =  $followers[0]['total_followers'];

           return $user_followers;
        } 
 
         return  $user_followers;
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
		$avator = $this->_customer->load($u_id)->getData('profile_picture');
		if(isset($avator) && !empty($avator) ) {
			return $avator;
		}
		return null;
	}

   public function isVVUser() {
	   
	    $user_collection =  $this->_customerFactory->create();

        $user = $user_collection->load($this->isUserLogin());
		
		return $user->getData('is_vv_user');
   }
   
   public function isLive(){
   	$API_KEY = 'AIzaSyCp85_qlwtcQA-w3iiUG5vNt-IW_Y4d4rk';
   	
   	$current_user = $this->getUserData();
   	//var_dump($current_user);
   	$channelID = '';
   	if(isset($current_user['vv_youtube_channel']) && !empty($current_user['vv_youtube_channel'])){
		$channelID = $current_user['vv_youtube_channel'];
	}

	$online = false;
	if(!empty($channelID)){
		$channelInfo = 'https://www.googleapis.com/youtube/v3/search?part=snippet&channelId='.$channelID.'&type=video&eventType=live&key='.$API_KEY;

		$context = stream_context_create(
		    array(
		        "http" => array(
		            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
		        )
		    )
		);

		$extractInfo = $this->curl_get_contents($channelInfo);
//echo $extractInfo = '{"kind": "youtube#searchListResponse", "etag": "\"XpPGQXPnxQJhLgs6enD_n8JR4Qk/-f6JA5_OcXz2RWuH1mpAA2_9mM8\"", "regionCode": "US", //"pageInfo": { "totalResults": 0, "resultsPerPage": 5 }, "items": [] }';

		$showInfo = json_decode($extractInfo, true);
		print_r($showInfo); die;
//var_dump($showInfo);
		if(!$showInfo){
			$online = false;	
		}
		if(isset($showInfo['pageInfo']['totalResults']) && $showInfo['pageInfo']['totalResults'] === 0){
			$online = false;
		}else{
			$online = $channelID;
		}
	}
	
	return $online;
   }
   
   protected function curl_get_contents($url)
	{
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	  curl_setopt($ch, CURLOPT_REFERER, 'http://dev.goodgreenkarma.com/');
	  $data = curl_exec($ch);
	  curl_close($ch);
	  return $data;
	}
	
	public function getLiveVideoUser() {
		
		$login_customer = $this->isUserLogin(); 
		if($login_customer) { 
			
	      $connection = $this->_vvhelper->_getWriteAdapter();

          $tablename = $connection->getTableName('vv_user_live');

          $sql = "SELECT  count(*) as is_user_live FROM " . $tablename . " WHERE customer_id = ".$login_customer."";
		    	  
		  $result = $connection->fetchOne($sql);
		  if($result > 0) {		  
		     return true;			 
		  }
		}
		return false;	
	}
	
	public function isUserLive($u_id) { 
			
	      $connection = $this->_vvhelper->_getWriteAdapter();

          $tablename = $connection->getTableName('vv_user_live');

          $sql = "SELECT  count(*) as is_user_live FROM " . $tablename . " WHERE customer_id = ".$u_id."";
		    	  
		  $result = $connection->fetchOne($sql); 
		  if($result > 0) {	

        	$current_user = $this->getUserData(); 
        	$channelID = '';
   	        if(isset($current_user['vv_youtube_channel']) && !empty($current_user['vv_youtube_channel'])){
		       $channelID = $current_user['vv_youtube_channel'];
	        }		  
		    return $channelID;			 
		  } 
		  
		return false;		
		
	}

	
	 
}
