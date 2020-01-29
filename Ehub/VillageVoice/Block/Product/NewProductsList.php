<?php
namespace Ehub\VillageVoice\Block\Product;
use Magento\Framework\View\Element\Template;
use Zend_Db_Expr;

class NewProductsList extends \Magento\Catalog\Block\Product\ListProduct  {


    public function getLoadedProductCollection()
    {  
       $pathInfo = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true ]) ;

       $urlKey = explode('/', $pathInfo);

       $page = 'new-products';

       if(isset($urlKey) && (count($urlKey) > 1) && (end($urlKey) == $page )) {

        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $_productCollectionFactory = $objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory'); 

        $_catalogProductVisibility = $objectManager->create('Magento\Catalog\Model\Product\Visibility'); 

        $collection = $_productCollectionFactory->create();

        $collection->setVisibility($_catalogProductVisibility->getVisibleInCatalogIds());


        $collections = $this->_addProductAttributesAndPrices(
            $collection
        )->addStoreFilter()->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'news_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
        )->addAttributeToSort(
            'news_from_date',
            'desc'
        );

       }
 
        return $collection; 
 
    }
}
