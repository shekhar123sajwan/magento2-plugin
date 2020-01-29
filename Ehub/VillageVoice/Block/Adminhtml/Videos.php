<?php
namespace Ehub\VillageVoice\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Videos extends Container
{

	protected function _construct()
	{
		$this->_controller = 'adminhtml';
		$this->_blockGroup = 'Ehub_VillageVoice';
		$this->_headerText = __('Videos');
		parent::_construct();
                $this->removeButton('add');
	}
}
