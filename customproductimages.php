<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

$autoloadPath = dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use CustomProductImages\Entity\ObjectModel\CustomProductImage;
use CustomProductImages\Module\AbstractModule;

class CustomProductImages extends AbstractModule
{
    protected $tables = [
        'custom_product_image',
        'custom_product_image_shop',
    ];

    protected $hooks = [
        'displayHeader',
        'displayFooterProduct',
        'displayAdminProductsExtra',
        'actionAdminControllerSetMedia'
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
        if($this->context->controller->php_self != 'product')
            return;
        $this->context->controller->addCSS($this->_path . 'views/css/cpi.css');
    }

    public function hookDisplayFooterProduct($params)
    {
        $id_product = $params['product']['id_product'] ?? 0;
        if(!$id_product)
            return;

        $customProductImageLinks = $this->getProductCustomImagesLinks($id_product);
        if(empty($customProductImageLinks))
            return;

        $this->context->smarty->assign('customProductImageLinks', $customProductImageLinks);
        return $this->display(__FILE__, 'views/templates/hook/displayFooterProduct.tpl');
    }

    public function hookDisplayAdminProductsExtra($params) {
        $id_product = (int) $params['id_product'];
        
        $customProductImageLinks = $this->getProductCustomImagesLinks($id_product);
        
        $this->context->smarty->assign('customProductImageLinks', $customProductImageLinks);
        return $this->display(__FILE__, 'views/templates/hook/displayAdminProductsExtra.tpl');
    }

    private function getProductCustomImagesLinks($id_product) {
        $customProductImages = CustomProductImage::queryObjects(['id_product' => $id_product], $this->context->shop->id);

        $customProductImageLinks = [];
        foreach($customProductImages as $customProductImage)
        {
            $customProductImageLinks[$customProductImage->id] = $this->context->link->getMediaLink(_MODULE_DIR_. $this->name . '/images/'.$customProductImage->name);
        }

        return $customProductImageLinks;
    }


    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('controller') == 'AdminProducts') {
            {
                $sfContainer = SymfonyContainer::getInstance();
                $router = $sfContainer->get('router');

                Media::addJsDef([
                    'add_product_custom_image_url' => $router->generate('add_product_custom_image', ['idProduct' => 1]),
                    'delete_product_custom_image_url' => $router->generate('delete_product_custom_image_url', ['idCustomImage' => 1]),
                ]
                );
                $this->context->controller->addJS($this->_path . 'views/js/cpi-admin.js');
                $this->context->controller->addCSS($this->_path . 'views/css/cpi-admin.css');
            }
        }
    }
}
