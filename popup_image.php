<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $osC_NavigationHistory->removeCurrentPage();

  $Qproducts = $osC_Database->query('select pd.products_name, p.products_image from :table_products p left join :table_products_description pd on p.products_id = pd.products_id where p.products_status = 1 and p.products_id = :products_id and pd.language_id = :language_id');
  $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
  $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qproducts->bindInt(':products_id', $_GET['pID']);
  $Qproducts->bindInt(':language_id', $osC_Language->getID());
  $Qproducts->execute();
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo $Qproducts->value('products_name'); ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<script type="text/javascript"><!--
var i=0;
function resize() {
  if (navigator.appName == 'Netscape') i=40;
  if (document.images[0]) window.resizeTo(document.images[0].width +30, document.images[0].height+60-i);
  self.focus();
}
//--></script>
</head>
<body onload="resize();">
<?php echo tep_image(DIR_WS_IMAGES . $Qproducts->value('products_image'), $Qproducts->value('products_name')); ?>
</body>
</html>
<?php require('includes/application_bottom.php'); ?>
