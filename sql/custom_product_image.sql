CREATE TABLE IF NOT EXISTS `_DB_PREFIX_custom_product_image` (
    `id_image` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `id_product` int(10) NOT NULL,
    `name` varchar(255) NOT NULL,
    PRIMARY KEY (`id_image`),
    KEY `product` (`id_product`)
    ) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;