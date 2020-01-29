<?php
namespace Ehub\VillageVoice\Controller\Adminhtml\Users;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Ehub\VillageVoice\Model\UsersFactory;

class Save extends \Magento\Backend\App\Action
{
    
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Result page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Users model factory
     *
     * @var \Tutorial\SimpleUsers\Model\UsersFactory
     */
    protected $_usersFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param UsersFactory $usersFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        UsersFactory $usersFactory
    ) {
       parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_usersFactory = $usersFactory;
    }

    /**
     * user access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ehub_VillageVoice::manage_users');
    }
   /**
     * @return void
     */
   public function execute()
   {
      $isPost = $this->getRequest()->getPost();

      if ($isPost) {
         $userModel = $this->_usersFactory->create();
         $userId = $this->getRequest()->getParam('id');

         if ($userId) {
            $userModel->load($userId);
         }
         $formData = $this->getRequest()->getParam('users');
         $userModel->setData($formData);
		 
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 	 
         
         try {
            // Save news
            $userModel->save();

            // Display success message
            $this->messageManager->addSuccess(__('The user has been saved.'));

            // Check if 'Save and Continue'
            if ($this->getRequest()->getParam('back')) {
               $this->_redirect('*/*/edit', ['id' => $userModel->getId(), '_current' => true]);
               return;
            }

            // Go to grid page
            $this->_redirect('*/*/');
            return;
         } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
         }

         $this->_getSession()->setFormData($formData);
         $this->_redirect('*/*/edit', ['id' => $userModel]);
      }
   }
}
