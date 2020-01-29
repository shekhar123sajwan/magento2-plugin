<?php

namespace Ehub\VillageVoice\Model;

class Videos extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {

    const CACHE_TAG = 'voice_videos';

    protected $_cacheTag = 'voice_videos';
    protected $_eventPrefix = 'voice_videos';

    protected function _construct()
    {
        $this->_init('Ehub\VillageVoice\Model\ResourceModel\Videos');
    }
    
    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues() {
        $values = [];

        return $values;
    }
	
	public function getVideos() {
		
		$collection = $this->getCollection();	
		
		return $collection;	
	}
	
    public function getFeaturedVideos() {
        $collection = $this->getCollection();
        $collection->addFieldToFilter('v_status', 1);
        $collection->addFieldToFilter('v_is_featured', 1);
		$collection->addFieldToFilter('v_user_status', 1);
        $collection->setOrder('v_id','DESC');
        $collection->setPageSize(8);
        return $collection->getData(); 
    }	

}
