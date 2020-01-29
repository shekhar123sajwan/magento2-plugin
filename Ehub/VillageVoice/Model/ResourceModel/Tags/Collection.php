<?php
namespace Ehub\VillageVoice\Model\ResourceModel\Tags;
  
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
  
class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */

    protected function _construct()
    {
        $this->_init(
            'Ehub\VillageVoice\Model\Tags',
            'Ehub\VillageVoice\Model\ResourceModel\Tags'
        );
    }

}