<?php

namespace Ehub\VillageVoice\Block;

use Magento\Store\Model\StoreManagerInterface;
use Ehub\VillageVoice\Model\Videos;
use Ehub\VillageVoice\Helper\Data as Vvhelper;
use  \Magento\Customer\Model\Session ;
use Ehub\VillageVoice\Model\Tags;

class AddVideo extends \Magento\Framework\View\Element\Template {

    protected $_categoryFactory;
	
	protected $v_id; 

    protected $_v_title;

    protected $_v_cat = [];

    protected $_v_banner;

    protected $_v_upload_link;

    protected $_v_about;
	
	protected $_v_youtube_channel;
	
	protected $_is_vv_user;
	
	protected $_v_status;
    
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Ehub\VillageVoice\Helper\Data $helperData,
   \Ehub\VillageVoice\Model\Category $category, \Ehub\VillageVoice\Helper\Category $categoryHelper,
   StoreManagerInterface $storeManager,
   Videos $video,
   Vvhelper $vv_helper,
   Session $customer,
   Tags $tags,
   \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        parent::__construct($context);
        $this->helperData = $helperData;
        $this->_categoryFactory = $category;
        $this->_categoryHelper = $categoryHelper;
        $this->_storeManager = $storeManager;
        $this->_videos = $video; 
        $this->vv_helper = $vv_helper;
		$this->_customers = $customer;
		$this->tags = $tags;
        $this->_customerFactory = $customerFactory;		
		
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout(); 
        return $this;
    }

    public function getAllCategories(){
        $collection = $this->_categoryFactory->getCollection();
        $collection->addFieldToFilter('c_status', 1);
        return $collection;
    }

    public function getAction() {  
        $currentStore = $this->_storeManager->getStore();
        $baseUrl = $currentStore->getBaseUrl().'voice/video/save';
         return $baseUrl;
    }
	
	public function getHtmlEditor()
{
    //Refactor this line later
    $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
    $wysiwygConfig = $object_manager->get('\Magento\Cms\Model\Wysiwyg\Config');
    $configwysiwyg =  $wysiwygConfig->getConfig();
    $configwysiwygData = $configwysiwyg->getData();
    $configwysiwygData["settings"]["theme_advanced_buttons1"] = "bold,italic,|,justifyleft,justifycenter,justifyright,|,fontselect,fontsizeselect,|,forecolor,backcolor,|,link,unlink,image,|,bullist,numlist,|,code";
    $configwysiwygData["settings"]["theme_advanced_buttons2"] = false;
    $configwysiwygData["settings"]["theme_advanced_buttons3"] = false;
    $configwysiwygData["settings"]["theme_advanced_buttons4"] = false;
    $configwysiwygData["settings"]["theme_advanced_statusbar_location"] = false;
    $configwysiwygData["height"] = "250px";
    $configwysiwygData["add_variables"] = false;
    $configwysiwygData["plugins"] = false;
    $configwysiwygData["add_widgets"] = false;
    $configwysiwygData["add_images"] = false;
    $configwysiwygData["files_browser_window_url"] =false;
    $configwysiwygData["no_display"] =true;
    $configwysiwygData["toggle_button"] = false;
    $configwysiwyg->setData($configwysiwygData);
    $elementId = "custom_wysiwyg_content";
    $config = [
        'label'     => __('Content'),
        'name'      => 'wysiwyg_content',
        'config' => $configwysiwyg,
        'wysiwyg' =>  true,
        'style' => 'width:100%; height:250px;',
        'required'=> true,
        'class' => " required-entry",
        'value' => '',
        "validation" => [
            "required-entry" => true
        ]
    ];
    $form = $object_manager->get('\Magento\Framework\Data\Form');
    $editor = $object_manager->get('\Magento\Framework\Data\Form\Element\Editor')->setData($config);
    $editor->setForm($form);
    $editor->setId($elementId);
    return $editor->getElementHtml();
}

  public function editVideoRequest() {

        if(!empty($this->getRequest()->getParams('edit_v_id')) && $this->getRequest()->getParams('edit_v_id')) {

           return true;
        }

        return false;
    } 

    public function getEditVideoData() {
 
        $video= $this->getRequest()->getParams('edit_v_id');

        $v_id = array_keys($video);
		
        $collection = $this->_videos->getCollection()->addFieldToFilter('v_id', $v_id[0])->addFieldToFilter('v_user_id', $this->getVUserID())->getFirstItem();

        $this->v_id =  $collection->getData('v_id');

        $this->_v_title = $collection->getData('v_title');

        $this->_v_cat = $this->getCategory( $this->v_id  );

        $this->_v_banner = $collection->getData('v_banner');

        $this->_v_upload_link = $collection->getData('v_url');

        $this->_v_about = $collection->getData('v_about');
		
		$this->_v_youtube_channel = $collection->getData('vv_youtube_channel');
		
		$this->_is_vv_user = $this->_customers->getCustomer()->getData();
		
		$this->_v_status = $collection->getData('v_status');

    }

    public function getCategory($v_id) {

        $cat_id = [];    

        $connection = $this->vv_helper->_getWriteAdapter();

        $tablename = $connection->getTableName('vv_video_category');   

        $sql = "SELECT * FROM  ".$tablename." WHERE  v_id = '" .$v_id. "' ";      

        $cats = $connection->fetchAll($sql);

        foreach ( $cats as $key => $cat) {
            $cat_id[] = $cat['c_id'];
        }

        return $cat_id;  
    }

    public function getVId() {
        return $this->v_id;
    }
	
    public function getTitle() {
        return $this->_v_title;
    }

    public function getUploadLink() {
        return $this->_v_upload_link;
    }  

    public function getAboutDesc() {
        return $this->_v_about;
    }
	
	public function getYouTubeChannel() {
	  return $this->_v_youtube_channel;
	}
	
	public function getvvUser() {
	  return $this->_is_vv_user;
	}

    public function getBanner() {
        return $this->_v_banner;
    }   

    public function getvCategory() {
        return $this->_v_cat;
    } 	
	
    public function isUserLogin() {

      if ($this->_customers->isLoggedIn()) {
       // Customer is logged in 
       return  $this->_customers->getCustomerId();  // get Customer Id

      }  

       return null;
    }

    public function getBannerUrl() {
		return '/pub/media/vv_banners/videos/'.$this->_v_banner;
	}

    public function checkExistingUserVideo() {
		if( $this->isUserLogin() == $this->getVUserID()) {
			return true;
		}
		return false;
	}
	
	public function getVUserID() {
		
	    $video= $this->getRequest()->getParams('edit_v_id');

        $v_id = array_keys($video);
 

        $collection = $this->_videos->getCollection()->addFieldToFilter('v_id', $v_id[0])->getFirstItem(); 

        return $collection->getData('v_user_id');	
	}

    public function getVTags($v_id) {
        return $this->tags->getVTags($v_id);

    }
  
    public function strip_char($str) {

     return  preg_replace("/[^A-Z0-9]/i", "_", $str);  

    }	
	
   public function getVStatus() {
	   return $this->_v_status;
   }
   public function isVVUser() {
	   
	    $user_collection =  $this->_customerFactory->create();

        $user = $user_collection->load($this->isUserLogin());
		
		return $user->getData('is_vv_user');
   }
	
}
