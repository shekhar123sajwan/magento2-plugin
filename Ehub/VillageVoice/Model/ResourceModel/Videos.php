<?php

namespace Ehub\VillageVoice\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Videos extends AbstractDb {

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context) {
        parent::__construct($context);
    }

    protected function _construct() {
        $this->_init('vv_videos', 'v_id');
    }

    protected function _getLoadSelect($field, $value, $object){
        
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->joinLeft(
                ['customer' => $this->getTable('customer_entity')],
                'vv_videos.v_user_id = customer.entity_id',
                ['email','firstname','middlename','lastname']
            );
        
        return $select;
    }
}
