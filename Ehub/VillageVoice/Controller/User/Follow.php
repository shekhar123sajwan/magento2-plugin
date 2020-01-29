<?php
namespace Ehub\VillageVoice\Controller\User;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory; 
use Ehub\VillageVoice\Helper\Data as Vvhelper; 
use \Magento\Framework\Exception\AlreadyExistsException;
class Follow extends \Magento\Framework\App\Action\Action
{
     protected $resultPageFactory;

     public function __construct(
        Context $context,
        PageFactory $resultPageFactory ,
        Vvhelper $vv_helper 
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->vv_helper =  $vv_helper; 
    }


    public function execute()
    { 

     $result =  ''; 

     $status = 0; 

    if ($this->getRequest()->getPost() ) {

         $formData['user_id'] = $this->getRequest()->getParam('v_user_id');

         $formData['follower_id'] = $this->getRequest()->getParam('f_id');


         if(($this->getRequest()->getParam('f_id')) && !empty($this->getRequest()->getParam('v_user_id')) ) {

            $connection = $this->vv_helper->_getWriteAdapter();

            $tablename = $connection->getTableName('vv_followers');


            $is_following =  $this->getRequest()->getParam('following'); 


            if($is_following > 0) {

                $query = 'Delete FROM ' . $tablename . ' WHERE user_id = '. (int)$formData['user_id'] . ' and follower_id = '. $formData['follower_id'] .' ';

                  $connection->query($query);

                  $response = ['status' => 1, 'result' => $result, 'follow' => 0 ] ;

                  echo json_encode($response);

                  die;
            } 

            $connection->insertMultiple($tablename, $formData);

            $response = ['status' => 1, 'result' => $result, 'follow' => 1 ] ;

            echo json_encode($response);

            die;

         }
     }

     echo json_encode(['status' => $status, 'result' => $result, 'follow' => 0 ]);

    }


}
