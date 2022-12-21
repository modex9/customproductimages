<?php

namespace CustomProductImages\Entity\ObjectModel;

class CustomProductImage extends ObjectModelExt
{
    public $id;

    public $id_product;

    public $id_hrx;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'custom_product_image',
        'primary' => 'id_image',
        'fields' => [
            'id_product'    => ['type' => self::TYPE_INT, 'required' => true, 'size' => 10],
            'name'          => ['type' => self::TYPE_STRING, 'size' => 255],
        ],
    ];
}
