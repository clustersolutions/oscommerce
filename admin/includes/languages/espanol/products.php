<?php
/*
  $Id: products.php,v 1.5 2004/11/07 20:38:51 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  define('HEADING_TITLE', 'Productos');
  define('HEADING_TITLE_SEARCH', 'Buscar:');
  define('HEADING_TITLE_GOTO', 'Ir A:');

  define('TAB_GENERAL', 'General');
  define('TAB_DATA', 'Data');
  define('TAB_IMAGES', 'Images');
  define('TAB_ATTRIBUTES', 'Attributes');
  define('TAB_CATEGORIES', 'Categories');

  define('FIELDSET_ASSIGNED_ATTRIBUTES', 'Assigned Attributes');

  define('TABLE_HEADING_PRODUCTS', 'Productos');
  define('TABLE_HEADING_PRICE', 'Price');
  define('TABLE_HEADING_QUANTITY', 'Quantity');
  define('TABLE_HEADING_STATUS', 'Estado');
  define('TABLE_HEADING_ACTION', 'Acci&oacute;n');

  define('TEXT_NEW_PRODUCT', 'Nuevo Producto en &quot;%s&quot;');
  define('TEXT_CATEGORIES', 'Categorias:');

  define('TEXT_EDIT_INTRO', 'Haga los cambios necesarios');

  define('TEXT_INFO_COPY_TO_INTRO', 'Elija la categoria hacia donde quiera copiar este producto');
  define('TEXT_INFO_CURRENT_CATEGORIES', 'Categorias:');

  define('TEXT_DELETE_PRODUCT_INTRO', 'Es usted seguro usted desea suprimir permanentemente este producto?');

  define('TEXT_MOVE_PRODUCTS_INTRO', 'Elija la categoria hacia donde quiera mover <b>%s</b>');
  define('TEXT_MOVE', 'Mover <b>%s</b> a:');

  define('TEXT_PRODUCTS_STATUS', 'Estado de los Productos:');
  define('TEXT_PRODUCTS_DATE_AVAILABLE', 'Fecha Disponibilidad:');
  define('TEXT_PRODUCT_AVAILABLE', 'Disponible');
  define('TEXT_PRODUCT_NOT_AVAILABLE', 'Agotado');
  define('TEXT_PRODUCTS_MANUFACTURER', 'Fabricante del producto:');
  define('TEXT_PRODUCTS_NAME', 'Nombre del Producto:');
  define('TEXT_PRODUCTS_DESCRIPTION', 'Descripci&oacute;n del producto:');
  define('TEXT_PRODUCTS_QUANTITY', 'Cantidad:');
  define('TEXT_PRODUCTS_MODEL', 'M&oacute;delo:');
  define('TEXT_PRODUCTS_IMAGE', 'Imagen:');
  define('TEXT_PRODUCTS_URL', 'URL del Producto:');
  define('TEXT_PRODUCTS_URL_WITHOUT_HTTP', '<small>(sin http://)</small>');
  define('TEXT_PRODUCTS_TAX_CLASS', 'Tipo Impuesto:');
  define('TEXT_PRODUCTS_PRICE_NET', 'Precio de los Productos (Net):');
  define('TEXT_PRODUCTS_PRICE_GROSS', 'Precio de los Productos (Gross):');
  define('TEXT_PRODUCTS_WEIGHT', 'Peso:');

  define('TEXT_PRODUCT_DATE_ADDED', 'Este producto fue a&ntilde;adido el %s.');
  define('TEXT_PRODUCT_DATE_AVAILABLE', 'Este producto estar&aacute; disponible el %s.');
  define('TEXT_PRODUCT_MORE_INFORMATION', 'Si quiere mas informaci&oacute;n, visite la <a href="http://%s" target="blank"><u>p&aacute;gina</u></a> de este producto.');

  define('TEXT_HOW_TO_COPY', 'Metodo de Copia:');
  define('TEXT_COPY_AS_LINK', 'Enlazar el producto');
  define('TEXT_COPY_AS_DUPLICATE', 'Duplicar el producto');

  define('ERROR_CANNOT_LINK_TO_SAME_CATEGORY', 'Error: No se pueden enlazar productos en la misma categoria.');
  define('ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE', 'Error: No se puede escribir en el directorio de imagenes del cat&aacute;logo: ' . realpath('../images'));
  define('ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST', 'Error: No existe el directorio de imagenes del cat&aacute;logo: ' . realpath('../images'));
?>
