<?php

namespace Ehub\VillageVoice\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;
use Magento\Customer\Model\Customer;
use Magento\Customer\Api\CustomerMetadataInterface;

class InstallData implements InstallDataInterface {

    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory, Config $eavConfig) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        
//        $eavSetup->addAttribute(
//                \Magento\Customer\Model\Customer::ENTITY, 'request_for_vv_user', [
//            'type' => 'int',
//            'label' => 'Request to enable',
//            'input' => 'select',
//            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
//            'required' => false,
//            'visible' => true,
//            'user_defined' => true,
//            'position' => 104,
//            'system' => 0,
//                ]
//        );
//        
//        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'request_for_vv_user');
//        
//        $attribute->save();
//        
//        $eavSetup->addAttribute(
//                \Magento\Customer\Model\Customer::ENTITY, 'is_vv_user', [
//            'type' => 'int',
//            'label' => 'Is Village Voice User?',
//            'input' => 'select',
//            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
//            'required' => false,
//            'visible' => true,
//            'user_defined' => true,
//            'position' => 104,
//            'system' => 0,
//                ]
//        );
//        
//        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'is_vv_user');
// 
//        //  used_in_forms are of these types you can use forms key according to your need 
//        //  ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit',
//        //  'customer_address_edit','customer_register_address', 'customer_account_create']
//         
//        $attribute->setData(
//            'used_in_forms',
//            ['adminhtml_customer', 'customer_account_edit']
// 
//        );
//        
//        $attribute->save();
//        
//        $eavSetup->addAttribute(
//                \Magento\Customer\Model\Customer::ENTITY, 'facebook_profile_link', [
//            'type' => 'varchar',
//            'label' => 'Facebook Profile Link',
//            'input' => 'text',
//            'required' => false,
//            'visible' => true,
//            'user_defined' => true,
//            'position' => 100,
//            'system' => 0,
//                ]
//        );
//        
//        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'facebook_profile_link');
// 
//        //  used_in_forms are of these types you can use forms key according to your need 
//        //  ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit',
//        //  'customer_address_edit','customer_register_address', 'customer_account_create']
//         
//        $attribute->setData(
//            'used_in_forms',
//            ['adminhtml_customer', 'customer_account_edit']
// 
//        );
//        
//        $attribute->save();
//        
//        $eavSetup->addAttribute(
//                \Magento\Customer\Model\Customer::ENTITY, 'youtube_page_link', [
//            'type' => 'varchar',
//            'label' => 'YouTube Page Link',
//            'input' => 'text',
//            'required' => false,
//            'visible' => true,
//            'user_defined' => true,
//            'position' => 100,
//            'system' => 0,
//                ]
//        );
//        
//        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'youtube_page_link');
// 
//        //  used_in_forms are of these types you can use forms key according to your need 
//        //  ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit',
//        //  'customer_address_edit','customer_register_address', 'customer_account_create']
//         
//        $attribute->setData(
//            'used_in_forms',
//            ['adminhtml_customer', 'customer_account_edit']
// 
//        );
//        
//        $attribute->save();
//        
//        $eavSetup->addAttribute(
//                \Magento\Customer\Model\Customer::ENTITY, 'about_yourself', [
//            'type' => 'text',
//            'label' => 'About yourself',
//            'input' => 'text',
//            'required' => false,
//            'visible' => true,
//            'user_defined' => true,
//            'position' => 100,
//            'system' => 0,
//                ]
//        );
//        
//        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'about_yourself');
// 
//        //  used_in_forms are of these types you can use forms key according to your need 
//        //  ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit',
//        //  'customer_address_edit','customer_register_address', 'customer_account_create']
//         
//        $attribute->setData(
//            'used_in_forms',
//            ['adminhtml_customer', 'customer_account_edit']
// 
//        );
//        
//        $attribute->save();
        
//        $eavSetup->addAttributeToSet(
//            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
//            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
//            null,
//            'request_for_vv_user');
//        
//        $eavSetup->addAttributeToSet(
//            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
//            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
//            null,
//            'is_vv_user');
//        
//        $eavSetup->addAttributeToSet(
//            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
//            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
//            null,
//            'facebook_profile_link');
//        
//        $eavSetup->addAttributeToSet(
//            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
//            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
//            null,
//            'youtube_page_link');
//        
//        $eavSetup->addAttributeToSet(
//            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
//            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
//            null,
//            'about_yourself');
        
        
//        $eavSetup->removeAttribute(
//            \Magento\Customer\Model\Customer::ENTITY,
//            'profile_pic'
//        );
        
//        $eavSetup->addAttribute(
//            \Magento\Customer\Model\Customer::ENTITY, 'profile_pic', [
//            'type' => 'varchar',
//            'label' => 'Profile Pic',
//            'input' => 'image',
//            'required' => false,
//            'visible' => true,
//            'user_defined' => true,
//            'position' => 110,
//            'system' => 0,
//            'backend' => 'Ehub\VillageVoice\Model\Attribute\Backend\ProfilePic'
//                ]
//        );
//        
//        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'profile_pic');
// 
//        //  used_in_forms are of these types you can use forms key according to your need 
//        //  ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit',
//        //  'customer_address_edit','customer_register_address', 'customer_account_create']
//         
//        $attribute->setData(
//            'used_in_forms',
//            ['adminhtml_customer', 'customer_account_edit', 'customer_account_create']
// 
//        );
//        
//        $attribute->save();
//        
//        $eavSetup->addAttributeToSet(
//            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
//            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
//            null,
//            'profile_pic');
        
        //$eavSetup->addAttribute(
         //   \Magento\Customer\Model\Customer::ENTITY, 'vv_always_allowed', [
         //       'type' => 'int',
         //       'label' => 'VV Always Allowed',
         //       'input' => 'select',
         //       'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
         //       'required' => false,
         //       'visible' => true,
         //       'user_defined' => true,
         //       'position' => 111,
         //       'system' => 0,
         //   ]
        //);
        
        //$attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'vv_always_allowed');
 
        //$attribute->setData(
        //    'used_in_forms',
        //    ['adminhtml_customer', 'customer_account_edit']
 
        //);
        
        //$attribute->save();
        
        //$eavSetup->addAttributeToSet(
        //    CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
        //    CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
        //    null,
        //    'vv_always_allowed');
            
	$eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY, 'vv_youtube_channel', [
                'type' => 'text',
                'label' => 'VV YouTube Channel ID',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'position' => 115,
                'system' => 0,
            ]
        );
        
        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'vv_youtube_channel');
 
        $attribute->setData(
            'used_in_forms',
            ['adminhtml_customer', 'customer_account_edit']
 
        );
        
        $attribute->save();
        
        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            'vv_youtube_channel');
        
    }

}
