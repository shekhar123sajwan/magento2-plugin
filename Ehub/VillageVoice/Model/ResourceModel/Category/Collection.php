<?php
namespace Ehub\VillageVoice\Model\ResourceModel\Category;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'c_id';
    protected $_eventPrefix = 'voice_category_collection';
    protected $_eventObject = 'category_collection';
    
    protected function _construct()
    {
        $this->_init(
            'Ehub\VillageVoice\Model\Category',
            'Ehub\VillageVoice\Model\ResourceModel\Category'
        );
    }
}
