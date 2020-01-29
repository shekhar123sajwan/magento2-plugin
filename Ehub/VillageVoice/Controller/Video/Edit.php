<?php
namespace Ehub\VillageVoice\Controller\Video;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;  
use \Magento\Customer\Model\Session; 
class Edit extends \Magento\Framework\App\Action\Action
{
     protected $resultPageFactory;

     public function __construct(
        Context $context,
        PageFactory $resultPageFactory ,
		Session $customerSession,
		\Magento\Customer\Model\CustomerFactory $customerFactory
		
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory; 
		$this->_customerSession = $customerSession;
		$this->_customerFactory = $customerFactory;
    }


    public function execute()
    { 
		if (!$this->_customerSession->isLoggedIn()) {
           $this->_redirect('customer/account/login');
		   return;
        }
		
        $resultPage = $this->resultPageFactory->create();
		

        $resultPage->getConfig()->getTitle()->set(__('Edit'));

        return $resultPage;
    }


}
