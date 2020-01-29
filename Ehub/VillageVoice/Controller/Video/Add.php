<?php
namespace Ehub\VillageVoice\Controller\Video;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Ehub\VillageVoice\Model\Videos;
use \Magento\Framework\App\ResourceConnection;
use \Magento\Customer\Model\Session;

class Add extends \Magento\Framework\App\Action\Action
{
     protected $resultPageFactory;

     public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        UploaderFactory $uploaderFactory ,
        \Magento\Framework\Filesystem $filesystem ,
        Videos $videos,
        ResourceConnection $resource,
		Session $customerSession,
		\Magento\Customer\Model\CustomerFactory $customerFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;

         $this->uploaderFactory = $uploaderFactory;
         $this->_filesystem = $filesystem;
         $this->_videoFactory = $videos;
         $this->_resource = $resource;
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
         $resultPage->getConfig()->getTitle()->set(__('Add Video'));
         return $resultPage;
    }


}
