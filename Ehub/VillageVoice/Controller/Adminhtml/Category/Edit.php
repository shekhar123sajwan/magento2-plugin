<?php
namespace Ehub\VillageVoice\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Ehub\VillageVoice\Model\CategoryFactory;

class Edit extends \Magento\Backend\App\Action
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
     * VillageVoice model factory
     *
     * @var \Ehub\VillageVoice\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        CategoryFactory $categoryFactory
    ) {
       parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_categoryFactory = $categoryFactory;
    }


/**
     * category access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ehub_VillageVoice::category');
    }

   /**
     * @return void
     */
   public function execute()
   {
      $categoryId = $this->getRequest()->getParam('c_id');
        /** @var \Ehub\VillageVoice\Model\Category $model */
        $model = $this->_categoryFactory->create();

        if ($categoryId) {
            $model->load($categoryId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This category no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // Restore previously entered form data from session
        $data = $this->_session->getCategoryData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        
        $this->_coreRegistry->register('vv_category', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Ehub_VillageVoice::main_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('VillageVoice'));

        return $resultPage;
   }
}
