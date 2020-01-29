<?php
namespace Ehub\VillageVoice\Model\ResourceModel\VideoCategory;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'c_id';
    protected $_eventPrefix = 'video_category_collection';
    protected $_eventObject = 'video_category_collection';
    
    protected function _construct()
    {
        $this->_init(
            'Ehub\VillageVoice\Model\VideoCategory',
            'Ehub\VillageVoice\Model\ResourceModel\VideoCategory'
        );
    }
}
