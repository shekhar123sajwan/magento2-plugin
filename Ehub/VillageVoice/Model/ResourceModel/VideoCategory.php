<?php

namespace Ehub\VillageVoice\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class VideoCategory extends AbstractDb {

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context) {
        parent::__construct($context);
    }

    protected function _construct() {
        $this->_init('vv_video_category', 'v_id');
        $this->_isPkAutoIncrement = false;
    }
}
