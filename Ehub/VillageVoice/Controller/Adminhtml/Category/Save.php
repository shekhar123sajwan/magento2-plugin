<?php

namespace Ehub\VillageVoice\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Ehub\VillageVoice\Model\CategoryFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;

class Save extends \Magento\Backend\App\Action {

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
    protected $_categoryFactory;

    /**
     * Locale Date/Timezone
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezone;

    /**
     * File System
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * File Upload Factory
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * List of allowed files
     * @var array
     */
    protected $_allowedExtensions = ['jpeg', 'jpg', 'png']; // to allow file upload types 

    /**
     * File type input field name
     * @var string
     */
    protected $_fileId = "category[c_banner_file]";

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
    Context $context, Registry $coreRegistry, PageFactory $resultPageFactory, CategoryFactory $categoryFactory,
            \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
            Filesystem $_fileSystem, UploaderFactory $_uploaderFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_timezone = $timezone;
        $this->_fileSystem = $_fileSystem;
        $this->_uploaderFactory = $_uploaderFactory;
    }

    /**
     * category access rights checking
     *
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Ehub_VillageVoice::category');
    }

    /**
     * @return void
     */
    public function execute() {
        $isPost = $this->getRequest()->getPost();

        if ($isPost) {
            $categoryModel = $this->_categoryFactory->create();
           // $categoryId = $this->getRequest()->getParam('c_id');

            $formData = $this->getRequest()->getParam('category');
			//print_r($categoryId); die;

            // if ($categoryId) {
            //   $categoryModel->load($categoryId);
           //    $formData['updated_at'] = $this->_timezone->date()->format('Y-m-d H:i:s');
           // } else {
           //     $formData['created_at'] = $this->_timezone->date()->format('Y-m-d H:i:s');
           // }
			
			if ( isset( $formData['c_id'] ) && !empty($formData['c_id']) ) {
				$categoryId = $formData['c_id'];
                $categoryModel->load($categoryId);
                $formData['updated_at'] = $this->_timezone->date()->format('Y-m-d H:i:s');
            } else {
                $formData['created_at'] = $this->_timezone->date()->format('Y-m-d H:i:s');
            } 
			
            try {
				
                
                $destinationPath = $this->getDestinationPath(); // Return upload folder path
				
                 if (!is_dir($destinationPath)) { 
                    mkdir($destinationPath, 0775);
                 } 		   

				 
                if (isset($_FILES['category']['name']['c_banner_file']) && !empty($_FILES['category']['name']['c_banner_file'])) {
                    $uploader = $this->_uploaderFactory->create(['fileId' => $this->_fileId])
                            ->setAllowedExtensions($this->_allowedExtensions); 
                    $uploaded = $uploader->save($destinationPath);
					
					
                    
                    if (!$uploaded) {
                        throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                        );
                    }
                    
                    // Assign new banner
                    if(isset($uploaded['file']) && !empty($uploaded['file'])){
                        $formData['c_banner'] = $uploaded['file'];
                    }else{
                        $formData['c_banner'] = str_replace(" ", "_", $_FILES['category']['name']['c_banner_file']);
                    }
                }
				
				$formData['c_url_key'] = strtolower(str_replace(' ', '_', trim($formData['c_title'])) ); 
                
                $categoryModel->setData($formData);
                // Save category
                $categoryModel->save();

                // Display success message
                $this->messageManager->addSuccess(__('The category has been saved.'));

                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['c_id' => $categoryModel->getId(), '_current' => true]);
                    return;
                }

                // Go to grid page
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($formData);
            $this->_redirect('*/*/edit', ['c_id' => $categoryModel]);
        }
    }

    /**
     * Get upload folder path
     * @return string
     */
    public function getDestinationPath() {
        return $this->_fileSystem
                        ->getDirectoryWrite(DirectoryList::MEDIA)
                        ->getAbsolutePath('/vv_banners/category/');
    }

}
