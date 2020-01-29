<?php
namespace Ehub\VillageVoice\Controller\Adminhtml\Users;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Ehub\VillageVoice\Model\Users;

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
     * Users model factory
     *
     * @var \Ehub\VillageVoice\Model\UsersFactory
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
        Users $usersFactory
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
     * Create new user action
     *
     * @return void
     */
   public function execute()
   {
      $this->_forward('edit');
   }
}
