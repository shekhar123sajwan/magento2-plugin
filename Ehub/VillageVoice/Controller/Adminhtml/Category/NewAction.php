<?php
namespace Ehub\VillageVoice\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Ehub\VillageVoice\Model\CategoryFactory;

class NewAction extends \Magento\Backend\App\Action
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
     * Category model factory
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
     * user access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ehub_VillageVoice::manage_category');
    }
   /**
     * Create new user action
     *
     * @return void
     */
   public function execute()
   {
      $this->_forward('edit');
   }
}
