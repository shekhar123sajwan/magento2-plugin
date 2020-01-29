<?php

namespace Ehub\VillageVoice\Model;

class Users extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {

    const CACHE_TAG = 'user_locator_users';

    protected $_cacheTag = 'user_locator_users';
    protected $_eventPrefix = 'user_locator_users';

    protected function _construct()
    {
        $this->_init('Ehub\VillageVoice\Model\ResourceModel\Users');
    }
    
    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues() {
        $values = [];

        return $values;
    }

}
