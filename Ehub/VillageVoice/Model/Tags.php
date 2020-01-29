<?php
namespace Ehub\VillageVoice\Model;

class Tags extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {

    const CACHE_TAG = 'voice_tags';

    protected $_cacheTag = 'voice_tags';
    protected $_eventPrefix = 'voice_tags';

    protected function _construct()
    {
        $this->_init('Ehub\VillageVoice\Model\ResourceModel\Tags');
    }
    
    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues() {
        $values = [];

        return $values;
    }

    public function getTags() {
        $collection = $this->getCollection();

        return $collection->getData();

    }

    public function getVTags($v_id) {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('vv_v_to_tags'); //gives table name with prefix

        $sql = "select * from " . $tableName. " where v_id = '".$v_id."' ";

        $data = $connection->fetchAll($sql);
        
        return $data;


    }

	 public function getTotalVByTagName($tag_name) {
		
        $joinCollection = $this->getCollection()->join(
												 ['v_2_tag'=> 'vv_v_to_tags'],
												 "main_table.tag = v_2_tag.tag",
												   [
												     "tag" => "v_2_tag.tag"
												   ]
											    )
												->join(
												 ['v'=> 'vv_videos'],
												 "v_2_tag.v_id = v.v_id"
											    )
												->addFieldToFilter(
                                                    'main_table.tag', array('like' => '%'.$tag_name.'%') ) 
                                                 ->addFieldToFilter("v.v_status" , 1)
												 ->addFieldToFilter("v.v_user_status" , 1)
												;

       return $joinCollection->getSize();

    }
	
	 public function getVByTagName($tag_name,$total_pro_show_page,$limit) {
		
        $joinCollection = $this->getCollection()->join(
												 ['v_2_tag'=> 'vv_v_to_tags'],
												 "main_table.tag = v_2_tag.tag",
												   [
												     "tag" => "v_2_tag.tag"
												   ]
											    )
												->join(
												 ['v'=> 'vv_videos'],
												 "v_2_tag.v_id = v.v_id"
											    )
												->addFieldToFilter(
                                                    'main_table.tag', array('like' => '%'.$tag_name.'%') ) 
                                                 ->addFieldToFilter("v.v_status" , 1)
												 ->addFieldToFilter("v.v_user_status" , 1)
												 ->setPageSize($total_pro_show_page)
												 ->setCurPage($limit)
												;

       return $joinCollection->getData();

    }	


}
