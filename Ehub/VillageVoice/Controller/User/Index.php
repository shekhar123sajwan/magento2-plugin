<?php
namespace Ehub\VillageVoice\Controller\User;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;   
class Index extends \Magento\Framework\App\Action\Action
{
     protected $resultPageFactory;

     public function __construct(
        Context $context,
        PageFactory $resultPageFactory 
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory; 
    }


    public function execute()
    { 
	    //user youtube live streaming request
	    if($this->getRequest()->getParam('user_live_streaming')) {	 
		     $response = ['status' => false];
			 $c_id = '';
		     $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			 $u_id = $this->getRequest()->getParam('u_id'); 
             $is_live_request = $this->getRequest()->getParam('is_live_request'); 
			 $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			 $customerSession = $objectManager->get('Magento\Customer\Model\Session');
			 $dateTime = $objectManager->get('\Magento\Framework\Stdlib\DateTime\DateTime') ;

			 
			 if ($customerSession->isLoggedIn()) {
               $c_id = $customerSession->getId();
             }
			 
			 $date = $dateTime->date(); 
			 $connection = $resource->getConnection();	
			 $tableName = $resource->getTableName('vv_user_live');		 
             if($is_live_request && $c_id){		 
				 
                 $sql = "SELECT  count(*) as is_user_live FROM " .$tableName. " WHERE customer_id = ".$c_id."";
		    	  
		         $is_exist = $connection->fetchOne($sql);
				 
				 if($is_exist) {
					 
					$sql = "DELETE  FROM " .$tableName. " WHERE `customer_id` = " .$c_id. " ";
				 
				    $connection->query($sql);
				 }

				 $sql = "INSERT INTO " . $tableName . " (`customer_id`, `date_time`, `status`) VALUES ('".$c_id."', '".$date."', '1' )";
				 					 
				 $connection->query($sql);
				 $response = ['status' => true , 'entry_added' =>  true];

				 echo json_encode($response); 
				 die;
			 }

             if(!$is_live_request && $c_id){
				 
				 $sql = "DELETE  FROM " .$tableName. " WHERE `customer_id` = " .$c_id. " ";
				 
				 $connection->query($sql);
				 
				 $response = ['status' => true , 'entry_deleted' => true];
				 
				 echo json_encode($response);
				 
				 die;
			 }	
			 
			  echo json_encode($response);
				 
			  die;			 
             
		}


        $resultPage = $this->resultPageFactory->create();

        $resultPage->getConfig()->getTitle()->set(__('User'));

        return $resultPage;
    }


}
