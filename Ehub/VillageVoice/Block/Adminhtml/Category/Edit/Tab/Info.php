<?php

namespace Ehub\VillageVoice\Block\Adminhtml\Category\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Ehub\VillageVoice\Model\System\Config\Status;

class Info extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Ehub\VillageVoice\Model\Config\Status
     */
    protected $categoryStatus;

   /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Status $categoryStatus
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Status $categoryStatus,
        array $data = array()
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->categoryStatus = $categoryStatus;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
       /** @var $model \Ehub\VillageVoice\Model\Category */
        $model = $this->_coreRegistry->registry('vv_category');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('category_');
        $form->setFieldNameSuffix('category');

        $data = $model->getData();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('\Ehub\VillageVoice\Helper\Data');
        
        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend' => __('General'))
        );

        if ($model->getId()) {
            $fieldset->addField(
                'c_id',
                'hidden',
                array('name' => 'c_id')
            );
        }
        $fieldset->addField(
            'c_title',
            'text',
            array(
                'name'        => 'c_title',
                'label'    => __('Category Title'),
                'required'     => true
            )
        );
        
        $banner_url = '';
	if(isset($data['c_banner'])){
	        $banner_url = $helper->getBanner($data['c_banner']);
	}

        if ($model->getId()) {
            $fieldset->addField(
                    'c_banner', 'hidden', ['name' => 'c_banner']
            );
        }

	if(isset($data['c_banner']) && !empty($data['c_banner']) && $banner_url){
		$fieldset->addField(
		        'c_banner_file', 'file', [
		    'name' => 'c_banner_file',
		    'label' => __('Banner'),
		    'required' => false
		        ]
		)->setAfterElementHtml('
		    <script>
		        require([
		             "jquery",
		        ], function($){
		            $(document).ready(function () {
			            $("#category_c_banner_file").parent().after(\'<br><img src="' . $banner_url . '" width="100px" />\');
		            });
		          });
		   </script>
		');
	}else{
		$fieldset->addField(
                'c_banner_file', 'file', [
		    'name' => 'c_banner_file',
		    'label' => __('Banner'),
		    'required' => false
		        ]
		);
	}
        
        $fieldset->addField(
            'c_about',
            'editor',
            array(
                'name'   => 'c_about',
                'label'    => __('About Category'),
                'required'     => true,
                'style' => 'height:16em;',
                'config'    => $this->_wysiwygConfig->getConfig(),
                'wysiwyg'   => true
            )
        );
        
        $fieldset->addField(
            'c_meta_title',
            'text',
            array(
                'name'        => 'c_meta_title',
                'label'    => __('Category Meta Title'),
                'required'     => false
            )
        );
        
        $fieldset->addField(
            'c_meta_desc',
            'textarea',
            array(
                'name'        => 'c_meta_desc',
                'label'    => __('Category Meta Description'),
                'required'     => false
            )
        );

        if ($model->getId()) {
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
        }
        $fieldset->addField(
            'c_status',
            'select',
            array(
                'name'      => 'c_status',
                'label'     => __('Status'),
                'options'   => $this->categoryStatus->toOptionArray()
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
        return __('Category Info');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Category Info');
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
