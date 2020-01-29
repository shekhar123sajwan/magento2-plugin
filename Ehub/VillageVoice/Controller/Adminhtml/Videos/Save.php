<?php

namespace Ehub\VillageVoice\Controller\Adminhtml\Videos;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Ehub\VillageVoice\Model\VideosFactory;
use Ehub\VillageVoice\Model\VideoCategory;
use Ehub\VillageVoice\Helper\Data as Vvhelper;
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
     * Videos model factory
     *
     */
    protected $_videosFactory;

    /**
     * Videos category model
     *
     */
    protected $_videoCategory;

    /**
     * Helper
     *
     * Ehub\VillageVoice\Helper\Data
     */
    protected $vv_helper;

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
    protected $_fileId = 'videos[v_banner_file]';

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param VideosFactory $videosFactory
     * @param VideoCategory $videoCategory
     * @param Vvhelper $vv_helper
     */
    public function __construct(
    Context $context, Registry $coreRegistry, PageFactory $resultPageFactory, VideosFactory $videosFactory, VideoCategory $videoCategory, Vvhelper $vv_helper, Filesystem $_fileSystem, UploaderFactory $_uploaderFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_videosFactory = $videosFactory;
        $this->_videoCategory = $videoCategory;
        $this->vv_helper = $vv_helper;
        $this->_fileSystem = $_fileSystem;
        $this->_uploaderFactory = $_uploaderFactory;
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
            $videoModel = $this->_videosFactory->create();
            $formData = $this->getRequest()->getParam('videos');
            $videoId = $formData['v_id'];

            if ($videoId) {
                $videoModel->load($videoId);
            }

            try {
                // Save video
                $destinationPath = $this->getDestinationPath(); // Return upload folder path
                if (isset($_FILES['videos']['name']['v_banner_file']) && !empty($_FILES['videos']['name']['v_banner_file'])) {
                    $uploader = $this->_uploaderFactory->create(['fileId' => $this->_fileId])
                            ->setAllowedExtensions($this->_allowedExtensions);
                    $uploaded = $uploader->save($destinationPath);

                    if (!$uploaded) {
                        throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                        );
                    }

                    // Assign new banner
                    if (isset($uploaded['file']) && !empty($uploaded['file'])) {
                        $formData['v_banner'] = $uploaded['file'];
                    } else {
                        $formData['v_banner'] = str_replace(" ", "_", $_FILES['category']['name']['v_banner_file']);
                    }
                }
                
                $videoModel->setData($formData);
                $id = $videoModel->save();

                if (isset($formData['v_categories']) && count($formData['v_categories'])) {
                    $connection = $this->vv_helper->_getWriteAdapter();
                    $tablename = $connection->getTableName('vv_video_category');
                    $quoted = $connection->quoteInto('IN (?)', $videoId);
                    $connection->delete($tablename, "v_id {$quoted}");

                    $video_categories = [];
                    foreach ($formData['v_categories'] as $category_id) {
                        $video_categories[] = ['v_id' => $videoId, 'c_id' => $category_id];
                    }
                    
                    $connection->insertMultiple($tablename, $video_categories);
                }

                // Display success message
                $this->messageManager->addSuccess(__('The video has been saved.'));

                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['v_id' => $videoModel->getId(), '_current' => true]);
                    return;
                }

                // Go to grid page
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($formData);
            $this->_redirect('*/*/edit', ['v_id' => $videoModel->getId()]);
        }
    }

    /**
     * Get upload folder path
     * @return string
     */
    public function getDestinationPath() {
        return $this->_fileSystem
                        ->getDirectoryWrite(DirectoryList::MEDIA)
                        ->getAbsolutePath('/vv_banners/videos/');
    }

}
