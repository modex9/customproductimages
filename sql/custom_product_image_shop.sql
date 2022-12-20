CREATE TABLE IF NOT EXISTS `_DB_PREFIX_custom_product_image_shop` (
    `id_image` int(10) NOT NULL,
    `id_shop` int(10) NOT NULL,
    PRIMARY KEY (`id_image`, `id_shop`),
    KEY `shop` (`id_shop`)
    ) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;