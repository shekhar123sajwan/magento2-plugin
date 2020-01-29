<?php
namespace Ehub\VillageVoice\Controller\Video;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Ehub\VillageVoice\Model\Videos;
use \Magento\Framework\App\ResourceConnection;
use Ehub\VillageVoice\Model\Tags as TagsModel;

class Tags extends \Magento\Framework\App\Action\Action
{
     protected $resultPageFactory;

     public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        UploaderFactory $uploaderFactory ,
        \Magento\Framework\Filesystem $filesystem ,
        Videos $videos,
        ResourceConnection $resource,
        TagsModel $tags
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;

         $this->uploaderFactory = $uploaderFactory;
         $this->_filesystem = $filesystem;
         $this->_videoFactory = $videos;
         $this->_resource = $resource;
         $this->tags = $tags;

    }
    public function execute()
    {

   
        if($this->getRequest()->getParam('tag_suggestion') && !empty($this->getRequest()->getParam('tag_suggestion') )) {

           $results = [];
 

           if( $this->tags->getTags() ){
             
             foreach ($this->tags->getTags() as  $tag) {
                
               $results[] = trim($tag['tag']);

             }        

           }
           
           echo json_encode( $results );
           die;        
        } 

         $resultPage = $this->resultPageFactory->create(); 
         return $resultPage;
    }


}
