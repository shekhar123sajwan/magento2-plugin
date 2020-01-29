<?php

namespace Ehub\VillageVoice\Block;
use Ehub\VillageVoice\Model\Videos;
use \Magento\Framework\App\ResourceConnection; 
use \Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use \Magento\User\Model\User;
use Ehub\VillageVoice\Helper\Data as Vvhelper;
use Ehub\VillageVoice\Helper\Category as CatHelper;

class IndexVideo extends \Magento\Framework\View\Element\Template {

    protected $_categoryFactory;
	
	protected $_videoData;
 
    
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Ehub\VillageVoice\Helper\Data $helperData, \Ehub\VillageVoice\Model\Category $category, \Ehub\VillageVoice\Helper\Category $categoryHelper,
        Videos $videos,
        ResourceConnection $resource,
        \Magento\Framework\Registry $registry,
        ObjectManagerInterface $objectmanager,
        StoreManagerInterface $StoreManagerInterface,
    \Magento\Customer\Model\Customer $customer,
        User $user,
        \Magento\Customer\Model\Session $session,
        Vvhelper $vv_helper,
		CatHelper $vv_cathelper
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
        $this->_isuserLogin =  $session;
        $this->vv_helper =  $vv_helper;
		$this->vv_cat_helper = $vv_cathelper;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout(); 
        
        return $this;
    }


    public function getVideo() {

        $current_video = $this->_registry->registry('vv_current_video');
		
		$this->_videoData = $current_video->getData();

        if($this->getRequest()->getParams('v_id') !== '' && $this->getRequest()->getParams('v_id') !== Null){
            $result = $current_video->getData();
            $url_type = $current_video->getData('v_url');
			$url_type = $this->checkUrlType($current_video->getData('v_url'));
          return [ 'result' => $result,'url_type' => $url_type ];
        }

        return null;
    }

    public function checkUrlType($url) {

      $urls_type = ['facebook'];

      foreach ($urls_type as $key => $url_type) {

         if(strpos($url, $url_type) != false) {
           return $url_type;
         }

      }
      return null;

    }

   public function createDateFormat($created_d) {

    $date_form = '';  
    $date1=date_create($created_d);
    $date2=date_create(date('Y-m-d H:i:s'));
    $diff=date_diff($date1,$date2);

    //date differnece
    $d = $diff->days;
 
   
    //convert date into weeks
    $week =  floor($d/7);

    //if date diff is below 0 hour then return date in min

    if($diff->h <= 0 && $d <= 0 && $diff->m <= 0 && $diff->y <= 0) {

        $date_form = $diff->i.' Min Ago';
        return  $date_form;

    }

    //if date diff is below 0 then return date in time
    if($diff->h > 0 && $d <= 0  && $diff->m <= 0 && $diff->y <= 0) {
      $hours_in_min = $diff->h  * 60;
      $min = $diff->i;

       $hour = ($hours_in_min + $min) / 60;

       $date_form = floor($hour).' Hour Ago';

       return  $date_form;
    }
  
   //if weeks is below 0 then return date in date format
    if($week <= 0 && $d >= 1 && $diff->m <= 0 && $diff->y <= 0 ) {
     $str = ($d > 1) ? 's' : '';
     $date_form = $d.' Day'.$str.' Ago';
     return  $date_form;
    }

    // if date differences in week 
   if($week >= 1 && $d >= 7 && $diff->m >= 0 && $diff->y <= 0 ) { 
      $str = ($week > 1) ? 's' : '';
      $date_form = $week. ' Week'.$str.' Ago';

      return  $date_form;
   }

     // if date differences in months

   if($week >= 53 && $diff->m >= 0 && $diff->y >= 1 ) { 
      $str = ($diff->y > 1) ? 's' : '';
      $date_form = $diff->y. ' Year'.$str.' Ago';

      return  $date_form;
   }  

   return $date_form;

  }

  public function getAjaxUrl() {
   return $this->_storeManager->getStore()->getBaseUrl().'/voice/video/comment';
  }

  public function isUserLogin() {

    if ($this->_isuserLogin->isLoggedIn()) {
       // Customer is logged in 
       return  $this->_isuserLogin->getCustomerId();  // get Customer Id

    }  

    return null;
  }


  public function loadComments($v_id) {

        $limit =  10;

        $connection = $this->vv_helper->_getWriteAdapter();

        $sql = 'select * from vv_video_comments where v_id = "'.$v_id.'" order by created_date Desc limit '.$limit.'';

        $data = $connection->fetchAll($sql);


        if(isset($data)  && !empty( $data ) ) {
           $count = 0;
            foreach ($data  as $key => $comment) {
                $u_data = $this->_customer->load($comment['user_id'])->getData();

                $u_name = $u_data['firstname'].' '.$u_data['lastname'];


                $created_date = $this->createDateFormat($comment['created_date']);


                $result_arr[] = ['u_name' => $u_name,
                                'user_id'  => $comment['user_id'],
                                'comment' => $comment['comment'],
                                'status'  => $comment['status'],
                                'created_date' => $created_date,
                                'comment_id'   => $comment['id']
                              ];

                $count++;
            }

            $result_arr['total_comments'] = $count;


          return $result_arr ; 
        }

        return null;
  

    }

    public function getTotalcomments($v_id) {

        $total_comments = 0;

        $connection = $this->vv_helper->_getWriteAdapter();

        $sql = 'select count(*) total_comments from vv_video_comments where v_id = "'.$v_id.'" order by created_date Desc';

        if( $data = $connection->fetchAll($sql)) {

           $total_comments =  $data[0]['total_comments'];

        }

        return $total_comments;


    }
	
	    public function getAjaxFollowUrl() {

       return $this->_storeManager->getStore()->getBaseUrl().'/voice/user/follow';

    }


    public function isFollowing() {

      $follower_id = $this->isUserLogin();

      $v_user_id = $this->_videoData['v_user_id'];

    if(!empty($follower_id) && $follower_id !== '') {

        $connection = $this->vv_helper->_getWriteAdapter();

        $sql = 'select count(*) is_following from vv_followers where user_id = "'.$v_user_id.'" and follower_id = '.$follower_id.' ';

        if( $connection->fetchOne($sql)) {
        
          return true;
        }
      }

      return null;

    }

    public function isLikedVideo() {

      $user_id = $this->isUserLogin();

      $v_id = $this->_videoData['v_id'];

     if(!empty($user_id) && $user_id !== '') {

        $connection = $this->vv_helper->_getWriteAdapter();

        $sql = 'select count(*) is_liked from vv_video_like where user_id = "'.$user_id.'" and v_id = '.$v_id.' ';

        if( $connection->fetchOne($sql)) {
        
          return true;
        }
      }

      return null;

    }    
  
      public function getLikeVidoeUrl() {

       return $this->_storeManager->getStore()->getBaseUrl().'/voice/video/index';

      }

    public function getAccountLoginUrl() {

     return $this->_storeManager->getStore()->getBaseUrl().'customer/account/login';

     }    


    public function countVideoLikes( int $v_id) {

        $total_likes = 0;

        $connection = $this->vv_helper->_getWriteAdapter();

        $tablename = $connection->getTableName('vv_video_like'); 

         $sql = 'select count(*) total_likes from '.$tablename.' where v_id = "'. $v_id.'" ';

        if( $data = $connection->fetchAll($sql)) {

           $total_likes =  $data[0]['total_likes'];

        }

        return $total_likes;       
     }
  
    public function getVideoUserUrl($user_id) {

     return $this->vv_cat_helper->getVideoUserUrl($user_id); 

    }  
 
}
