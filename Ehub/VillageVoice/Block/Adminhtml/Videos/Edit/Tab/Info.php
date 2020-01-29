<?php

namespace Ehub\VillageVoice\Block\Adminhtml\Videos\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Ehub\VillageVoice\Model\System\Config\Status;
use Ehub\VillageVoice\Model\System\Config\Category;

class Info extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Ehub\VillageVoice\Model\Config\Status
     */
    protected $videoStatus;
    
    /**
     * @var \Ehub\VillageVoice\Model\Config\Category
     */
    protected $videoCategories;

   /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Status $videoStatus
     * @param Category $videoCategories
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Status $videoStatus,
        Category $videoCategories,
        array $data = array()
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->videoStatus = $videoStatus;
        $this->videoCategories = $videoCategories;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
       /** @var $model \Ehub\VillageVoice\Model\Videos */
        $model = $this->_coreRegistry->registry('vv_videos');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('videos_');
        $form->setFieldNameSuffix('videos');

        $data = $model->getData();
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('\Ehub\VillageVoice\Helper\Data');
        
        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend' => __('General'))
        );

        $selectedCategories = [];
        if ($model->getId()) {
            $fieldset->addField(
                'v_id',
                'hidden',
                array('name' => 'v_id')
            );
            
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $categories = $objectManager->create('Ehub\VillageVoice\Model\VideoCategory')->getCollection();
            $categories->addFieldToFilter('v_id', $model->getId());
            foreach($categories as $row){
                $selectedCategories[] = $row->getData('c_id');
            }
        }
        $fieldset->addField(
            'v_title',
            'text',
            array(
                'name'        => 'v_title',
                'label'    => __('Video Title'),
                'required'     => true
            )
        );
        $fieldset->addField(
            'v_url',
            'text',
            array(
                'name'        => 'v_url',
                'label'    => __('Video URL'),
                'required'     => true
            )
        );
        
        $fieldset->addField(
            'v_categories',
            'multiselect',
            array(
                'name'      => 'v_categories[]',
                'label'     => __('Categories'),
                'values'   => $this->videoCategories->toOptionArray()
            )
        );
        $model->setVCategories($selectedCategories);
        
        $banner_url = '';
        
	if(isset($data['v_banner'])){
	        $banner_url = $helper->getBanner($data['v_banner'], 'videos');
	}

        if ($model->getId()) {
            $fieldset->addField(
                    'v_banner', 'hidden', ['name' => 'v_banner']
            );
        }

	if(isset($data['v_banner']) && !empty($data['v_banner']) && $banner_url){
		$fieldset->addField(
		        'v_banner_file', 'file', [
		    'name' => 'v_banner_file',
		    'label' => __('Video Banner'),
		    'required' => false
		        ]
		)->setAfterElementHtml('
		    <script>
		        require([
		             "jquery",
		        ], function($){
		            $(document).ready(function () {
			            $("#videos_v_banner_file").parent().after(\'<br><img src="' . $banner_url . '" width="100px" />\');
		            });
		          });
		   </script>
		');
	}else{
		$fieldset->addField(
                'v_banner_file', 'file', [
		    'name' => 'v_banner_file',
		    'label' => __('Video Banner'),
		    'required' => false
		        ]
		);
	}
        
        $fieldset->addField(
            'v_about',
            'editor',
            array(
                'name'        => 'v_about',
                'label'    => __('About Video'),
                'required'     => true,
                'style' => 'height:16em;',
                'config'    => $this->_wysiwygConfig->getConfig(),
                'wysiwyg'   => true
            )
        );
        
        $fieldset->addField(
            'email',
            'text',
            array(
                'name'        => 'email',
                'label'    => __('Created By'),
                'required'     => false,
                'disabled' => true
            )
        );
        
        $fieldset->addField(
            'created_at',
            'text',
            array(
                'name'        => 'created_at',
                'label'    => __('Created Date'),
                'required'     => false,
                'disabled' => true
            )
        );
        
        $fieldset->addField(
            'v_status',
            'select',
            array(
                'name'      => 'v_status',
                'label'     => __('Status'),
                'options'   => $this->videoStatus->toOptionArray()
            )
        );
		
		$fieldset->addField(
            'v_is_featured',
            'select',
            array(
                'name'      => 'v_is_featured',
                'label'     => __('Featured Video'),
				'value'  => '0',
                'values' => array('1' => 'Yes','0' => 'No')
            )
        );
        
        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Videos Info');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Videos Info');
    }
 
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
