<?php

namespace Ehub\VillageVoice\Block\Adminhtml\Videos\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('video_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Video Information'));
    }
}
