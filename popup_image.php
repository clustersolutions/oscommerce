<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $osC_NavigationHistory->removeCurrentPage();

  $Qproducts = $osC_Database->query('select pd.products_name, i.image from :table_products p left join :table_products_images i on (p.products_id = i.products_id and i.products_images_groups_id = :products_images_groups_id and i.default_flag = :default_flag), :table_products_description pd where p.products_id = :products_id and p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id');
  $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
  $Qproducts->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
  $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qproducts->bindInt(':products_images_groups_id', $osC_Image->getID('large'));
  $Qproducts->bindInt(':default_flag', 1);
  $Qproducts->bindInt(':products_id', $_GET['pID']);
  $Qproducts->bindInt(':language_id', $osC_Language->getID());
  $Qproducts->execute();
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $osC_Language->getTextDirection(); ?>" xml:lang="<?php echo $osC_Language->getCode(); ?>" lang="<?php echo $osC_Language->getCode(); ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $osC_Language->getCharacterSet(); ?>">
<title><?php echo $Qproducts->value('products_name'); ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
</head>
<body onload="resize();">
<?php echo $osC_Image->show($Qproducts->value('image'), $Qproducts->value('products_name'), '', 'large'); ?>
</body>
</html>
<?php require('includes/application_bottom.php'); ?>
