<?php
namespace Ehub\VillageVoice\Controller\Video;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Ehub\VillageVoice\Model\VideosFactory;
use Ehub\VillageVoice\Model\VideoCategory;
use Ehub\VillageVoice\Helper\Data as Vvhelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Customer\Model\Session ;
use Ehub\VillageVoice\Model\TagsFactory;

class Save extends \Magento\Framework\App\Action\Action {

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
    protected $_fileId = 'v_banner';


    private $_url_key_pre = 9870790;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param VideosFactory $videosFactory
     * @param VideoCategory $videoCategory
     * @param Vvhelper $vv_helper
     */
    public function __construct(
    Context $context, Registry $coreRegistry, PageFactory $resultPageFactory, VideosFactory $videosFactory, VideoCategory $videoCategory, Vvhelper $vv_helper, Filesystem $_fileSystem, UploaderFactory $_uploaderFactory, DateTime $date,
	Session $customerSession,
	TagsFactory $tags,
	\Magento\Framework\Image\AdapterFactory $imageFactory  
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_videosFactory = $videosFactory;
        $this->_videoCategory = $videoCategory;
        $this->vv_helper = $vv_helper;
        $this->_fileSystem = $_fileSystem;
        $this->_uploaderFactory = $_uploaderFactory;
        $this->date = $date;
		$this->customerSession = $customerSession;
		$this->tags = $tags;
		$this->_imageFactory = $imageFactory; 
    }

    /**
     * video access rights checking
     *
     * @return bool
     */
    // protected function _isAllowed() {
    //     return $this->_authorization->isAllowed('Ehub_VillageVoice::videos');
    // }

    /**
     * @return void
     */
    public function execute() {


        $isPost = $this->getRequest()->getPost();
		
		$vv_b_width = \Ehub\VillageVoice\Block\User::VV_IMG_WIDTH;
		$vv_b_height = \Ehub\VillageVoice\Block\User::VV_IMG_HEIGHT;
        
        if ($isPost) {
			
           	$videoModel = $this->_videosFactory->create();
           
            $formData = $this->getRequest()->getParams();
			
			$videoTags = $this->getRequest()->getParam('tag');
			
            // set default video status 
            $v_status = 0;				
			
            $videoId = '';

            if ( isset($formData['v_id']) && !empty($formData['v_id'])) {
				$videoId = $formData['v_id'];
                $videoModel->load($videoId);
            }	 
			
            $destinationPath = $this->getDestinationPath(); // Return upload folder path 
			

            try {
                // Save banner
                if (isset($_FILES['v_banner']['name']) && !empty($_FILES['v_banner']['name'])) {
                    $uploader = $this->_uploaderFactory->create(['fileId' => $this->_fileId])
                            ->setAllowedExtensions($this->_allowedExtensions);
                    $uploaded = $uploader->save($destinationPath);
													

                    if (!$uploaded) {
                        throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                        );
                    } 
					
					$save_resized_img = $this->resize($_FILES['v_banner']['name'], $vv_b_width, $vv_b_height);	

                    // file name
                    if (isset($uploaded['file']) && !empty($uploaded['file'])) {
                        $formData['v_banner'] = $uploaded['file'];
                    } 
                }
				
				//check if user is set allowed to user
			    if($this->customerSession->getCustomer()->getData('vv_always_allowed') > 0 )  {
				    $v_status = 1;				
			    }else if($videoId && isset($formData['v_status']) && !empty($formData['v_status']) ) {
					$v_status = $formData['v_status'];	
				} else {
					$v_status = 0;
				}
				
				$formData['v_status'] = $v_status ;
				
				//get cutomer ID
				$formData['v_user_id'] = $this->customerSession->getCustomer()->getId();

                if ($videoId) {				
                  $formData['updated_at'] = $this->date->date(); 
				} else {
				  $formData['created_at'] = $this->date->date();
				}

                $connection = $this->vv_helper->_getWriteAdapter();

                $tablename = $connection->getTableName('vv_videos');
				
				if (!$videoId) {

					$sql = "SHOW TABLE STATUS WHERE `Name` = '".$tablename."'" ;

					$result = $connection->fetchAll($sql);  

					$current_row_id = (int) $result[0]['Auto_increment']; 
				   
					//video url key
					$formData['v_url_key'] = (int) $this->_url_key_pre.$current_row_id;
				}
		
                $videoModel->setData($formData);
				 
                $id = $videoModel->save();
				
                $video_tags = [];
				
			    $tagsModel = $this->tags->create();
				                  
                try { 
				  
				     foreach ($videoTags as $videoTag) {

                      $v_tag['tag'] = $videoTag;

                      $tagsModel->setData($v_tag);
                      $tagsModel->save();
					 }

                   }catch (\Exception $e) {

                        //$this->messageManager->addError($e->getMessage());

                }
								
				
                $connection = $this->vv_helper->_getWriteAdapter();
				
                $tablename = $connection->getTableName('vv_v_to_tags'); 
               
               
               try { 			   
                $quoted = $connection->quoteInto('IN (?)', $id->getID());

                $connection->delete($tablename, "v_id {$quoted}");

                foreach ($videoTags as $videoTag) {
                    $tags[] = ['v_id' => $id->getID(), 'tag' => $videoTag];
                }
                
                $connection->insertMultiple($tablename, $tags); 
				
		       }catch (\Exception $e) { 
			   
			    //$this->messageManager->addError($e->getMessage());
			   
			   }				
				
			 
                if (isset($formData['v_cat']) && count($formData['v_cat'])) {
                    $connection = $this->vv_helper->_getWriteAdapter();
                    $tablename = $connection->getTableName('vv_video_category');
					
                    $quoted = $connection->quoteInto('IN (?)', $videoId);
                    $connection->delete($tablename, "v_id {$quoted}");
					
                    $video_categories = [];
                    foreach ($formData['v_cat'] as $category_id) {
                        $video_categories[] = ['v_id' => $id->getID(), 'c_id' => $category_id];
                    }
                    
                    $connection->insertMultiple($tablename, $video_categories);
                }

                // Display success message
                $this->messageManager->addSuccess(__('The video has been saved.'));
 
                $this->_redirect('*/*/add');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
 
            $this->_redirect('*/*/add', ['v_id' => $videoModel->getId()]);
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
	
	
    public function resize($image, $width = null, $height = null) {
        $absolutePath = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('vv_banners/videos/').$image;
        if (!file_exists($absolutePath)) return false;

        $ext = pathinfo($image, PATHINFO_EXTENSION);

        $file_name = pathinfo($image, PATHINFO_FILENAME); 

        $thumbnail = "-".$width ."x". $height.".".$ext;

        $imageResized = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('vv_banners/videos/').$file_name.$thumbnail;
        if (!file_exists($imageResized)) { // Only resize image if not already exists.
            //create image factory...
            $imageResize = $this->_imageFactory->create();         
            $imageResize->open($absolutePath);
            $imageResize->constrainOnly(TRUE);         
            $imageResize->keepTransparency(TRUE);         
            $imageResize->keepFrame(FALSE);         
            $imageResize->keepAspectRatio(TRUE);         
            $imageResize->resize($width,$height);  
            //destination folder                
            $destination = $imageResized ;    
            //save image      
            $imageResize->save($destination);  

            return true;       
        } 
        return false;
  } 


}
