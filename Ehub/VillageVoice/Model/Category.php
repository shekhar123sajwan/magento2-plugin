<?php

namespace Ehub\VillageVoice\Model;

class Category extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {

    const CACHE_TAG = 'voice_category';

    protected $_cacheTag = 'voice_category';
    protected $_eventPrefix = 'voice_category';

    protected function _construct()
    {
        $this->_init('Ehub\VillageVoice\Model\ResourceModel\Category');
    }
    
    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues() {
        $values = [];

        return $values;
    }

}
