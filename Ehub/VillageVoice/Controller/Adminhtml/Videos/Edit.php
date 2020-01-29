<?php
namespace Ehub\VillageVoice\Controller\Adminhtml\Videos;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Ehub\VillageVoice\Model\VideosFactory;

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
     * @var \Ehub\VillageVoice\Model\VideosFactory
     */
    protected $_videosFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param VideosFactory $videosFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        VideosFactory $videosFactory
    ) {
       parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_videosFactory = $videosFactory;
    }


/**
     * video access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ehub_VillageVoice::videos');
    }

   /**
     * @return void
     */
   public function execute()
   {
      $videosId = $this->getRequest()->getParam('v_id');
        /** @var \Ehub\VillageVoice\Model\Videos $model */
        $model = $this->_videosFactory->create();

        if ($videosId) {
            $model->load($videosId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This videos no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // Restore previously entered form data from session
        $data = $this->_session->getVideosData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        
        $this->_coreRegistry->register('vv_videos', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Ehub_VillageVoice::main_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('VillageVoice'));

        return $resultPage;
   }
}
