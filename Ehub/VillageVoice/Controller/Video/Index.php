<?php
namespace Ehub\VillageVoice\Controller\Video;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Ehub\VillageVoice\Model\Videos;
use \Magento\Framework\App\ResourceConnection;
use Ehub\VillageVoice\Helper\Data as Vvhelper;
use Ehub\VillageVoice\Block\IndexVideo;
use Ehub\VillageVoice\Helper\Category as CatHelper;

class Index extends \Magento\Framework\App\Action\Action
{
     protected $resultPageFactory;

     public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        UploaderFactory $uploaderFactory ,
        \Magento\Framework\Filesystem $filesystem ,
        Videos $videos,
        ResourceConnection $resource,
        \Magento\Framework\Registry $registry,
		Vvhelper $vv_helper,
        IndexVideo $video,
		CatHelper $vv_cathelper
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;

         $this->uploaderFactory = $uploaderFactory;
         $this->_filesystem = $filesystem;
         $this->_videoFactory = $videos;
         $this->_resource = $resource;
         $this->_registry = $registry;
         $this->vv_helper =  $vv_helper;
         $this->_video = $video;	
         $this->vv_cat_helper = $vv_cathelper;		 

    }
    public function execute()
    { 
	         if($this->getRequest()->isPost()) { 

            if($this->getRequest()->getParam('request') == 'v_like') {

                $data = ['status' => 0, 'result' => '', 'is_v_liked' => 0 , 'total_likes' => ''] ;

                $vdata['user_id'] = $this->getRequest()->getParam('v_user_id');

                $vdata['v_id'] = $this->getRequest()->getParam('v_id');
				
				$vdata['v_user_id'] = $this->getRequest()->getParam('user_id');

                $is_v_liked =  $this->getRequest()->getParam('v_is_like');

                if(!empty( $vdata['user_id'] ) && !empty( $vdata['v_id'] ) && !empty( $vdata['v_user_id'] )) {

                    $data = $this->setLikes($is_v_liked, $vdata['v_id'], $vdata['user_id'], $vdata);

                    echo json_encode($data);

                    die;

                }
            } 
         
            $data = ['status' => 0, 'result' => '' ,'is_v_liked' => 0 , 'total_likes' => '' ];

            echo json_encode($data);

         }
         $resultPage = $this->resultPageFactory->create();

         $current_video = $this->_registry->registry('vv_current_video');

         $v_title = $current_video->getData('v_title');

         $resultPage->getConfig()->getTitle()->set(__($v_title));

         return $resultPage;
    }
	
  public function setLikes($is_v_liked, $v_id, $user_id, $data) {

        $total_likes = 0;

        $connection = $this->vv_helper->_getWriteAdapter();

        $tablename = $connection->getTableName('vv_video_like'); 

        $vtablename = $connection->getTableName('vv_videos'); 


        if($is_v_liked > 0) {

           //  $query = 'Delete FROM ' . $tablename . ' WHERE user_id = '. (int)$data['user_id'] . ' and v_id = '. $v_id .' ';

           //   $connection->query($query);

              $total_likes = $this->_video->countVideoLikes($v_id);

           //   $query = 'Update '.$vtablename.' set v_like_count = '. $total_likes.' WHERE v_id = '. $v_id .' ';

           //   $connection->query($query);


              $response = ['status' => 1, 'result' => 'success', 'is_v_liked' => 1 , 'total_likes' => $total_likes ] ;

              return $response;

               
        }                 

          $connection->insertMultiple($tablename, $data); 

          $total_likes = $this->_video->countVideoLikes($v_id);

          $query = 'Update '.$vtablename.' set v_like_count = '. $total_likes.' WHERE v_id = '. $v_id .' ';

          $connection->query($query);          

          $response = ['status' => 1, 'result' => 'success', 'is_v_liked' => 1 , 'total_likes' => $total_likes ] ;

           return $response;
      }	
	 
    public function getVideoUserUrl($user_id) {

     return $this->vv_cat_helper->getVideoUserUrl($user_id); 

    }  	 
	  


}
