<?php

namespace Ehub\VillageVoice\Block\Adminhtml\Users\Grid\Renderer;

class Name extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_storeManager;


    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,      
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;        
    }


    public function render(\Magento\Framework\DataObject $row)
    {
//        var_dump($row->getData());
        $name = $row->getFirstname();
        
        if($row->getFirstname()){
            $name .= " ".$row->getMiddlename();
        }
        
        $name .= " ".$row->getLastname();
        return $name;
    }
}