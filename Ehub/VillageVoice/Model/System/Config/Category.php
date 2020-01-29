<?php

namespace Ehub\VillageVoice\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class Category implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $category = $objectManager->create('\Ehub\VillageVoice\Model\Category');
        
        $collection = $category->getCollection();
        
        foreach($collection as $model){            
            $options[] = ['value' => $model->getData('c_id'), 'label' => $model->getData('c_title')];
        }

        return $options;
    }
}
