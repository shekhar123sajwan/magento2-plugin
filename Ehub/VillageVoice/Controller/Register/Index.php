<?php

namespace Ehub\VillageVoice\Controller\Register;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    public function __construct(
    Context $context, PageFactory $resultPageFactory, \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_registry = $registry;
    }

    public function execute() {
        $resultPage = $this->resultPageFactory->create();

//        $current_category = $this->_registry->registry('vv_current_category');

        $resultPage->getConfig()->getTitle()->set(__('Register to upload in Village Voice'));
        return $resultPage;
    }

}
