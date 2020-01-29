<?php

namespace Ehub\VillageVoice\Controller;


class Router implements \Magento\Framework\App\RouterInterface {

    protected $_actionFactory;
    protected $_storeManager;
    protected $_scopeConfig;
    protected $_attributeCode;
    protected $_category;
	

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    public function __construct(
    \Magento\Framework\App\ActionFactory $actionFactory, 
            \Magento\Store\Model\StoreManagerInterface $storeManager, 
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 
            \Magento\Framework\Registry $registry,
            \Ehub\VillageVoice\Model\Category $category,
			\Ehub\VillageVoice\Model\Videos $videos,
			\Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->_actionFactory = $actionFactory;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_registry = $registry;
        $this->_category = $category;
		$this->_videos = $videos;
		$this->_customerFactory = $customerFactory;
    }

    /**
     * Validate village Voice Category Page and render category page
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request) {
        $pathInfo = trim($request->getPathInfo(), '/');
        $urlKey = explode('/', $pathInfo);
        //var_dump($urlKey);
		
        if (isset($urlKey[1]) && ($urlKey[0] == 'voice') ) {

          //$this->_registry->register('is_voice_module', 1);
		  
        }
		
		
        if (isset($urlKey[1]) && ($urlKey[0] == 'voice') && $urlKey[1] == 'category') {
            $urlKey = strtolower(urldecode($urlKey[2]));

            $collection = $this->_category->getCollection()->addFieldToFilter('c_url_key', $urlKey);

            $category = $collection->getFirstItem();

            if ($category->getCId()) {
                $request->setModuleName('voice')
                        ->setControllerName('category')
                        ->setActionName('Index')
                        ->setParam('c_id', $category->getCId());

                $this->_registry->register('vv_current_category', $category);
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, 'voice/' . $urlKey);

                return $this->_actionFactory->create('Magento\Framework\App\Action\Forward');
            }
        }
			
  
		// routes for video page
		if (isset($urlKey[1]) && ($urlKey[0] == 'voice') && $urlKey[1] == 'video') { 
     
      $urlKey = intval($urlKey[2]);

      $collection = $this->_videos->getCollection()->addFieldToFilter('v_url_key', $urlKey);

      $video = $collection->getFirstItem();

      if ($video->getData('v_id')) {
            $request->setModuleName('voice')
            ->setControllerName('video')
            ->setActionName('Index')
            ->setParam('v_id', $video->getData('v_id')); 
			
            $this->_registry->register('vv_current_video', $video);
 
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, 'voice/' . $urlKey);

            return $this->_actionFactory->create('Magento\Framework\App\Action\Forward');			
      }

    }

		// routes for User page
		if (isset($urlKey[1]) && ($urlKey[0] == 'voice') && $urlKey[1] == 'user') { 
     
         $u_id = intval($urlKey[2]);

         if($u_id) {

            $user_collection =  $this->_customerFactory->create();

            $user = $user_collection->load($u_id); 

           if($user->getData()) {
              $request->setModuleName('voice')
              ->setControllerName('user')
              ->setActionName('Index')
              ->setParam('u_id', $user->getData('entity_id')); 

              $this->_registry->register('user_data', $user->getData());
           } 
     
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, 'voice/' . $u_id);

            return $this->_actionFactory->create('Magento\Framework\App\Action\Forward');
        }

    } 
 
       return null;
    }

}
