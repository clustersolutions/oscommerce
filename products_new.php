<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  require(DIR_WS_LANGUAGES . $osC_Session->value('language') . '/' . FILENAME_PRODUCTS_NEW);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_PRODUCTS_NEW));
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="3" cellpadding="3">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_products_new.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  $Qproducts = $osC_Database->query('select p.products_id, pd.products_name, p.products_image, p.products_price, p.products_tax_class_id, p.products_date_added, m.manufacturers_name from :table_products p left join :table_manufacturers m on (p.manufacturers_id = m.manufacturers_id), :table_products_description pd where p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id order by p.products_date_added desc, pd.products_name');
  $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
  $Qproducts->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
  $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qproducts->bindInt(':language_id', $osC_Session->value('languages_id'));
  $Qproducts->setBatchLimit($_GET['page'], MAX_DISPLAY_PRODUCTS_NEW);
  $Qproducts->execute();

  if ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3')) {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText"><?php echo $Qproducts->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_PRODUCTS_NEW); ?></td>
            <td align="right" class="smallText"><?php echo $Qproducts->displayBatchLinksPullDown(); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  }
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ($Qproducts->numberOfRows() > 0) {
    while ($Qproducts->next()) {
      if ($osC_Services->isStarted('specials') && ($new_price = $osC_Specials->getPrice($Qproducts->valueInt('products_id')))) {
        $products_price = '<s>' . $osC_Currencies->displayPrice($Qproducts->value('products_price'), $Qproducts->valueInt('products_tax_class_id')) . '</s> <span class="productSpecialPrice">' . $osC_Currencies->displayPrice($new_price, $Qproducts->valueInt('products_tax_class_id')) . '</span>';
      } else {
        $products_price = $osC_Currencies->displayPrice($Qproducts->value('products_price'), $Qproducts->valueInt('products_tax_class_id'));
      }
?>
          <tr>
            <td width="<?php echo SMALL_IMAGE_WIDTH + 10; ?>" valign="top" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $Qproducts->valueInt('products_id')) . '">' . tep_image(DIR_WS_IMAGES . $Qproducts->value('products_image'), $Qproducts->value('products_name'), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>'; ?></td>
            <td valign="top" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $Qproducts->valueInt('products_id')) . '"><b><u>' . $Qproducts->value('products_name') . '</u></b></a><br>' . TEXT_DATE_ADDED . ' ' . tep_date_long($Qproducts->value('products_date_added')) . '<br>' . TEXT_MANUFACTURER . ' ' . $Qproducts->value('manufacturers_name') . '<br><br>' . TEXT_PRICE . ' ' . $products_price; ?></td>
            <td align="right" valign="middle" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_NEW, tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $Qproducts->value('products_id')) . '">' . tep_image_button('button_in_cart.gif', IMAGE_BUTTON_IN_CART) . '</a>'; ?></td>
          </tr>
          <tr>
            <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
<?php
    }
  } else {
?>
          <tr>
            <td class="main"><?php echo TEXT_NO_NEW_PRODUCTS; ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
<?php
  }
?>
        </table></td>
      </tr>
<?php
  if ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText"><?php echo $Qproducts->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_PRODUCTS_NEW); ?></td>
            <td align="right" class="smallText"><?php echo $Qproducts->displayBatchLinksPullDown(); ?></td>
          </tr>
        </table></td>
      </tr>
<?php
  }
?>
    </table></td>
<!-- body_text_eof //-->
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- right_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
    </table></td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
