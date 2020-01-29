<?php

namespace Ehub\VillageVoice\Controller\Adminhtml\Videos;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Ehub\VillageVoice\Model\VideosFactory;

class Disable extends \Magento\Backend\App\Action {

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
     * Videos model factory
     *
     */
    protected $_videosFactory;
    
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
    Context $context, Registry $coreRegistry, PageFactory $resultPageFactory, VideosFactory $videosFactory
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
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Ehub_VillageVoice::videos');
    }

    /**
     * @return void
     */
    public function execute() {
        $isPost = $this->getRequest()->getPost();
        
        if ($isPost) {

            $selected_ids = $this->getRequest()->getParam('v_id');

            if (!count($selected_ids)) {
                $this->messageManager->addError('Please select atleast one video!');
                $this->_redirect('*/*/');
            }

            $videoModel = $this->_videosFactory->create();
            try {
                foreach ($selected_ids as $selected_id) {
                    $video = $videoModel->load($selected_id);
                    $video->setData('v_status', '0');

                    $video->save();
                }

                // Display success message
                $this->messageManager->addSuccess(__('Selected videos have been disabled.'));

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
