<?php
namespace Ehub\VillageVoice\Model\ResourceModel\Users;

class ApprovalRequest extends \Magento\Customer\Model\ResourceModel\Customer\Collection
{

    /**
     * Name of collection model
     */
    const CUSTOMER_MODEL_NAME = \Magento\Customer\Model\Customer::class;

    /**
     * @var \Magento\Framework\DataObject\Copy\Config
     */
    protected $_fieldsetConfig;


    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Eav\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot
     * @param \Magento\Framework\DataObject\Copy\Config $fieldsetConfig
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param string $modelName
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\DataObject\Copy\Config $fieldsetConfig,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $this->_fieldsetConfig = $fieldsetConfig;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $entitySnapshot,
            $fieldsetConfig,
            $connection
        );
    }
    
    protected function _construct()
    {
        $this->_init(
            'Magento\Customer\Model\Customer',
            'Magento\Customer\Model\ResourceModel\Customer'
        );
    }
    
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addAttributeToSelect('*');
        $this->addAttributeToFilter('request_for_vv_user', 1);
        
        return $this;
    }
}
