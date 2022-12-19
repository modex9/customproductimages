<?php

require_once dirname(__FILE__) . '/classes/AbstractModule.php';

class CustomProductImages extends AbstractModule
{
    protected $tables = [
        'custom_product_image',
        'custom_product_image_shop',
    ];

    protected $hooks = [
        'displayHeader',
    ];

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->name = 'customproductimages';
        $this->tab = 'other';
        $this->version = '0.0.1';
        $this->author = 'M';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.6', 'max' => '1.7.9'];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Custom Product Images');
        $this->description = $this->l('Allows assigning custom images to a product.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function hookDisplayHeader($params)
    {
        return '';
    }
}
