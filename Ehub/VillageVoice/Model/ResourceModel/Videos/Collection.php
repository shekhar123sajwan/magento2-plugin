<?php
namespace Ehub\VillageVoice\Model\ResourceModel\Videos;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'v_id';
    protected $_eventPrefix = 'voice_video_collection';
    protected $_eventObject = 'video_collection';
    
    protected function _construct()
    {
        $this->_init(
            'Ehub\VillageVoice\Model\Videos',
            'Ehub\VillageVoice\Model\ResourceModel\Videos'
        );
    }
    
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
                ['customer' => $this->getTable('customer_entity')],
                'main_table.v_user_id = customer.entity_id',
                ['email','firstname','middlename','lastname']
            );
        
        return $this;
    }
}
