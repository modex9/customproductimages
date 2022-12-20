<?php

require_once dirname(__FILE__) . '/classes/AbstractModule.php';

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

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

        Shop::addTableAssociation('custom_product_image', ['type' => 'shop']);

        $this->displayName = $this->l('Custom Product Images');
        $this->description = $this->l('Allows assigning custom images to a product.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function hookDisplayHeader($params)
    {
        return '';
    }

    public function hookDisplayFooterProduct($params)
    {
        return 'Product footer';
    }

    public function hookDisplayAdminProductsExtra($params) {
        $id_product = (int) $params['id_product'];

        $productCustomImages = CustomProductImage::queryObjects(['id_product' => $id_product], $this->context->shop->id);

        $productCustomImageLinks = [];
        foreach($productCustomImages as $productCustomImage)
        {
            $productCustomImageLinks[] = $this->context->link->getMediaLink(_MODULE_DIR_. $this->name . '/images/'.$productCustomImage->name);
        }
        
        $this->context->smarty->assign('productCustomImageLinks', $productCustomImageLinks);

        return $this->display(__FILE__, 'views/templates/hook/displayAdminProductsExtra.tpl');
    }


    public function saveCustomImage($id_product)
    {
        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['custom_product_image']['name'], '.'), 1));

        if(!in_array($type, ['jpg', 'gif', 'jpeg', 'png'])) {
            return ['error' => 'Bad file extension: ' . $type];
        }

        if (isset($_FILES['custom_product_image']) &&
            !empty($_FILES['custom_product_image']['tmp_name'])
        ) {

            $imagesize = @getimagesize($_FILES['custom_product_image']['tmp_name']);
            if(empty($imagesize)) {
                return ['error' => $this->l('Failed to get image size.')];
            }

            if(!in_array(
                Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)), [
                    'jpg',
                    'gif',
                    'jpeg',
                    'png'
                ]
            )) {
                return ['error' => $this->l('Bad file mime type: ') . $imagesize['mime']];
            }

            $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
            $salt = sha1(microtime());

            if ($error = ImageManager::validateUpload($_FILES['custom_product_image'])) {
                return ['error' => $error];
            } elseif (!$temp_name || !move_uploaded_file($_FILES['custom_product_image']['tmp_name'], $temp_name)) {
                return ['error' => $this->l('Failed to move file to temporary location ') . $temp_name . $this->l('. Check your permissions.')];
            } elseif (!ImageManager::resize($temp_name, __DIR__.'/images/'.$salt.'_'.$_FILES['custom_product_image']['name'], null, null, $type)) {
                return ['error' => $this->l('An error occurred during the image upload process.')];
            }

            if (isset($temp_name)) {
                @unlink($temp_name);
            }

            $customProductImage = new CustomProductImage();

            $customProductImage->name = $salt.'_'.$_FILES['custom_product_image']['name'];
            $customProductImage->id_product = $id_product;
            $customProductImage->save();

            return ['success' => $this->l('Image successfully assigned to the product.')];
        }
        else {
            return ['error' => $this->l('An error occured while uploading a file.')];
        }
    }


    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('controller') == 'AdminProducts') {
            {
                $sfContainer = SymfonyContainer::getInstance();
                $router = $sfContainer->get('router');

                Media::addJsDef([
                    'add_product_custom_image_url' => $router->generate('add_product_custom_image', ['idProduct' => 1]),
                ]
                );
                $this->context->controller->addJS($this->_path . 'views/js/cpi-admin.js');
            }
        }
    }
}
