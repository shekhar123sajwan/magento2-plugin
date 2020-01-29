<?php

namespace Ehub\VillageVoice\Model;

class VideoCategory extends \Magento\Framework\Model\AbstractModel {

    protected function _construct()
    {
        $this->_init('Ehub\VillageVoice\Model\ResourceModel\VideoCategory');
    }

    public function getDefaultValues() {
        $values = [];
        return $values;
    }

}
