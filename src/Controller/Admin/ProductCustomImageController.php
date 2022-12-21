<?php

namespace CustomProductImages\Controller\Admin;

use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

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
        if(!$request->files->get('custom_product_image')) {
            return new JsonResponse(['error' => 'File was not submited to the server. Check "upload_max_filesize" in php.ini']);
        }
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        $module = $moduleRepository->getModule('customproductimages');
        $jsonData = $module->getInstance()->saveCustomImage($idProduct);

        return new JsonResponse($jsonData);
    }

    /**
     * Manage form add product attachment.
     *
     * @AdminSecurity("is_granted(['delete'], 'ADMINPRODUCTS_')")
     *
     * @param int $idCustomImage
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteAction($idCustomImage, Request $request)
    {
        $moduleRepository = $this->get('prestashop.core.admin.module.repository');
        $module = $moduleRepository->getModule('customproductimages');
        return new JsonResponse($module->getInstance()->deleteCustomImage($idCustomImage));
    }
}
