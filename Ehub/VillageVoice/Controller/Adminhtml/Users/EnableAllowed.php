<?php

namespace Ehub\VillageVoice\Controller\Adminhtml\Users;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class EnableAllowed extends \Magento\Backend\App\Action {

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
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
    Context $context, Registry $coreRegistry, PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
    }

    /**
     * user access rights checking
     *
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Ehub_VillageVoice::manage_users');
    }

    /**
     * @return void
     */
    public function execute() {
        $isPost = $this->getRequest()->getPost();
        
        if ($isPost) {

            $selected_ids = $this->getRequest()->getParam('entity_id');

            if (!count($selected_ids)) {
                $this->messageManager->addError('Please select atleast one user!');
                $this->_redirect('*/*/');
            }

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerModel = $objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface');
            try {
                foreach ($selected_ids as $selected_id) {
                    $customer = $customerModel->getById($selected_id);

                    $customer->setCustomAttribute('vv_always_allowed', 1);
                    $customerModel->save($customer);
                }

                // Display success message
                $this->messageManager->addSuccess(__('Selected user accounts have been added in Video Allowed List.'));

                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/');
                    return;
                }

                // Go to grid page
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $this->_redirect('*/*');
        }
    }

}
