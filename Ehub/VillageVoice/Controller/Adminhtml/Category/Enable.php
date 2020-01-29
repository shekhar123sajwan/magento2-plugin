<?php

namespace Ehub\VillageVoice\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Ehub\VillageVoice\Model\CategoryFactory;

class Enable extends \Magento\Backend\App\Action {

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
     * Category model factory
     *
     */
    protected $_CategoryFactory;
    
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
    Context $context, Registry $coreRegistry, PageFactory $resultPageFactory, CategoryFactory $CategoryFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_CategoryFactory = $CategoryFactory;
    }

    /**
     * Category access rights checking
     *
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Ehub_VillageVoice::Category');
    }

    /**
     * @return void
     */
    public function execute() {
        $isPost = $this->getRequest()->getPost();
        
        if ($isPost) {

            $selected_ids = $this->getRequest()->getParam('c_id');

            if (!count($selected_ids)) {
                $this->messageManager->addError('Please select atleast one Category!');
                $this->_redirect('*/*/');
            }

            $CategoryModel = $this->_CategoryFactory->create();
            try {
                foreach ($selected_ids as $selected_id) {
                    $Category = $CategoryModel->load($selected_id);
                    $Category->setData('c_status', '1');

                    $Category->save();
                }

                // Display success message
                $this->messageManager->addSuccess(__('Selected Category have been enabled.'));

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
