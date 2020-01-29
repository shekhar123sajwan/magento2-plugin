<?php
namespace Ehub\VillageVoice\Controller\Video;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Ehub\VillageVoice\Model\Videos;
use \Magento\Framework\App\ResourceConnection;
use Ehub\VillageVoice\Helper\Data as Vvhelper;
use Magento\Framework\Stdlib\DateTime\DateTime;
use \Magento\Customer\Model\Customer;
use Ehub\VillageVoice\Block\IndexVideo;

class Comment extends \Magento\Framework\App\Action\Action
{
     protected $resultPageFactory;

     public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        UploaderFactory $uploaderFactory ,
        \Magento\Framework\Filesystem $filesystem ,
        Videos $videos,
        ResourceConnection $resource,
         Vvhelper $vv_helper,
        \Magento\Framework\Registry $registry,
        DateTime $date,
        Customer $customer,
        IndexVideo $videoBlock
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
         $this->date = $date;
         $this->_customer = $customer;
         $this->_videoindexBlock =  $videoBlock;

    }
    public function execute()
    {  
         $response = ['status' => 0, 'result' => ''] ;

         $formData['v_id'] = $this->getRequest()->getParam('v_id');

         $formData['user_id'] = $this->getRequest()->getParam('user_id');

         $formData['comment'] = $this->getRequest()->getParam('comment');

         if(($this->getRequest()->getParam('load_all_comments')) && !empty($this->getRequest()->getParam('load_all_comments')) ) {

              $limit = $this->getRequest()->getParam('limit');

              $data =  $this->loadMoreComments($limit, $formData['v_id']);

              $response = ['status' => 1, 'result' => $data] ;

               echo json_encode($response);

               die;


         }

         if(empty($formData['comment']) || empty( $formData['user_id'] )) {

            echo json_encode($response);
            die;
         }

         //date_default_timezone_set('Asia/Kolkata');

         //$formData['user_id'] = 2; die;
         
        
        $connection = $this->vv_helper->_getWriteAdapter();

        $tablename = $connection->getTableName('vv_video_comments'); 

        $formData['created_date'] = $this->date->date(); 

        $connection->insertMultiple($tablename, $formData);

        $limit =  10;

        $sql = 'select * from vv_video_comments where v_id = "'.$formData['v_id'].'" order by created_date Desc limit '.$limit.'';

        $data = $connection->fetchAll($sql);


        if(isset($data)  && !empty( $data ) ) {
            foreach ($data  as $key => $comment) {
                $u_data = $this->_customer->load($comment['user_id'])->getData();

                $u_name = $u_data['firstname'].' '.$u_data['lastname'];


                $created_date = $this->_videoindexBlock->createDateFormat($comment['created_date']);


                $result_arr[] = ['u_name' => $u_name,
                                'user_id'  => $comment['user_id'],
                                'comment' => $comment['comment'],
                                'status'  => $comment['status'],
                                'created_date' => $created_date,
                                'comment_id'   => $comment['id']     
                              ];
            }
        }
 
        // Display success message
        $response = ['status' => 1, 'result' => $result_arr] ; 

        echo json_encode($response);

        die;
 

 
    }


    public function loadMoreComments($limit_offst, $v_id) {

      $limit = 10;

      $loadbtn = false;

      $result =  [ 'comments' => '' , 'limit' => $limit ]; 

      $connection = $this->vv_helper->_getWriteAdapter();

       $sql = 'select * from vv_video_comments where v_id = "'.$v_id.'" order by created_date Desc limit '.$limit.' Offset '.$limit_offst.' ';  

        $data = $connection->fetchAll($sql);


        if(isset($data)  && !empty( $data ) ) {

            $total_comments_show = 10;
            
            foreach ($data  as $key => $comment) {

                $u_data = $this->_customer->load($comment['user_id'])->getData();

                $u_name = $u_data['firstname'].' '.$u_data['lastname'];


                $created_date = $this->_videoindexBlock->createDateFormat($comment['created_date']);


                $result_arr[] = ['u_name' => $u_name,
                                'user_id'  => $comment['user_id'],
                                'comment' => $comment['comment'],
                                'status'  => $comment['status'],
                                'created_date' => $created_date,
                                'comment_id'   => $comment['id']     
                              ];

                 $total_comments_show-- ;
            }

            if($total_comments_show <= 0) {
                $loadbtn = true;
            }

         $result =  [ 'result' => $result_arr , 'limit' => $limit_offst + 10 , 'loadbtn' => $loadbtn ]; 

         return $result;

        }

        return null;




    }


}
