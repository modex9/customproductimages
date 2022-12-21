<?php

namespace CustomProductImages\Controller\Admin;

use CustomProductImages\Entity\ObjectModel\CustomProductImage;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Tools, ImageManager, Shop;

/**
 * Admin controller for product attachments (in /product/form page).
 */
class ProductCustomImageController extends FrameworkBundleAdminController
{
    /**
     * Manage form add product attachment.
     *
     * @AdminSecurity("is_granted(['create', 'update'], 'ADMINPRODUCTS_')")
     *
     * @param int $idProduct
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addAction($idProduct, Request $request)
    {
        if (!$request->files->get('custom_product_image')) {
            return new JsonResponse(['error' => 'File was not submited to the server. Check "upload_max_filesize" in php.ini']);
        }

        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['custom_product_image']['name'], '.'), 1));
        if(!in_array($type, ['jpg', 'gif', 'jpeg', 'png'])) {
            return new JsonResponse( ['error' => $this->trans('Bad file extension: %s' , 'Admin.Notifications.Error', [$type])] );
        }

        if (isset($_FILES['custom_product_image']) &&
            !empty($_FILES['custom_product_image']['tmp_name'])
        ) {

            $imagesize = @getimagesize($_FILES['custom_product_image']['tmp_name']);
            if(empty($imagesize)) {
                return new JsonResponse( ['error' => $this->trans('Failed to get image size.' , 'Admin.Notifications.Error')] );
            }

            if(!in_array(
                Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)), [
                    'jpg',
                    'gif',
                    'jpeg',
                    'png'
                ]
            )) {
                return new JsonResponse( ['error' => $this->trans('Bad file mime type: %s' , 'Admin.Notifications.Error', [$imagesize['mime']])] );
            }

            $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
            $salt = sha1(microtime());

            if ($error = ImageManager::validateUpload($_FILES['custom_product_image'])) {
                return ['error' => $error];
            } elseif (!$temp_name || !move_uploaded_file($_FILES['custom_product_image']['tmp_name'], $temp_name)) {
                return new JsonResponse( ['error' => $this->trans('Failed to move file to temporary location %s. Check your permissions.' , 'Admin.Notifications.Error', [$temp_name])] );

            } elseif (!ImageManager::resize($temp_name, _PS_ROOT_DIR_ . '/' . _MODULE_DIR_. 'customproductimages/images/'.$salt.'_'.$_FILES['custom_product_image']['name'], null, null, $type)) {
                return new JsonResponse( ['error' => $this->trans('Error occurred during the image upload process.' , 'Admin.Notifications.Error')] );
            }

            if (isset($temp_name)) {
                @unlink($temp_name);
            }

            Shop::addTableAssociation(CustomProductImage::$definition['table'], ['type' => 'shop']);
            $customProductImage = new CustomProductImage();
            $customProductImage->name = $salt.'_'.$_FILES['custom_product_image']['name'];
            $customProductImage->id_product = $idProduct;
            $customProductImage->save();

            return new JsonResponse([
                'success' => $this->trans('Image successfully assigned to the product.' , 'Admin.Notifications.Success'),
                'imageLink' => $this->getContext()->link->getMediaLink(_MODULE_DIR_. 'customproductimages/images/'.$customProductImage->name),
                'id' => $customProductImage->id,
            ]);
        }
        else {
            return new JsonResponse(['error' => $this->trans('An error occured while uploading a file.', 'Admin.Notifications.Error')]);
        }
    }

    /**
     * Manage form add product attachment.
     *
     * @AdminSecurity("is_granted(['delete'], 'ADMINPRODUCTS_')")
     *
     * @param int $idCustomImage
     *
     * @return JsonResponse
     */
    public function deleteAction($idCustomImage)
    {
        $customProductImage = new CustomProductImage($idCustomImage);
        $responseData = !$customProductImage->delete() ? ['error' => $this->trans('Failed to delete an image.', 'Admin.Notifications.Error')] :
            ['success' => $this->trans('Image deleted successfully.', 'Admin.Notifications.Success')];
        return new JsonResponse($responseData);
    }
}
