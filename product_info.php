<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $osC_Session->value('language') . '/' . FILENAME_PRODUCT_INFO);

  $Qcheck = $osC_Database->query('select count(*) as total from :table_products p, :table_products_description pd where p.products_status = 1 and p.products_id = :products_id and pd.products_id = p.products_id and pd.language_id = :language_id');
  $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
  $Qcheck->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qcheck->bindInt(':products_id', $_GET['products_id']);
  $Qcheck->bindInt(':language_id', $osC_Session->value('languages_id'));
  $Qcheck->execute();
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<script language="javascript"><!--
function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
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
    <td width="100%" valign="top"><?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params(array('action')) . 'action=add_product')); ?><table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php
  if ($Qcheck->valueInt('total') < 1) {
?>
      <tr>
        <td><?php new infoBox(array(array('text' => TEXT_PRODUCT_NOT_FOUND))); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
<?php
  } else {
    $Qproduct = $osC_Database->query('select p.products_id, pd.products_name, pd.products_description, p.products_model, p.products_quantity, p.products_image, pd.products_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id from :table_products p, :table_products_description pd where p.products_status = 1 and p.products_id = :products_id and pd.products_id = p.products_id and pd.language_id = :language_id');
    $Qproduct->bindTable(':table_products', TABLE_PRODUCTS);
    $Qproduct->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qproduct->bindInt(':products_id', $_GET['products_id']);
    $Qproduct->bindInt(':language_id', $osC_Session->value('languages_id'));
    $Qproduct->execute();

    $Qupdate = $osC_Database->query('update :table_products_description set products_viewed = products_viewed+1 where products_id = :products_id and language_id = :language_id');
    $Qupdate->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qupdate->bindInt(':products_id', $_GET['products_id']);
    $Qupdate->bindInt(':language_id', $osC_Session->value('languages_id'));
    $Qupdate->execute();

    if ( ($osC_Services->isStarted('specials')) && ($new_price = $osC_Specials->getPrice($Qproduct->valueInt('products_id'))) ) {
      $products_price = '<s>' . $osC_Currencies->displayPrice($Qproduct->value('products_price'), $Qproduct->valueInt('products_tax_class_id')) . '</s> <span class="productSpecialPrice">' . $osC_Currencies->displayPrice($new_price, $Qproduct->valueInt('products_tax_class_id')) . '</span>';
    } else {
      $products_price = $osC_Currencies->displayPrice($Qproduct->value('products_price'), $Qproduct->valueInt('products_tax_class_id'));
    }

    if (tep_not_null($Qproduct->value('products_model'))) {
      $products_name = $Qproduct->value('products_name') . '<br><span class="smallText">[' . $Qproduct->value('products_model') . ']</span>';
    } else {
      $products_name = $Qproduct->value('products_name');
    }
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading" valign="top"><?php echo $products_name; ?></td>
            <td class="pageHeading" align="right" valign="top"><?php echo $products_price; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="main">
<?php
    if (tep_not_null($Qproduct->value('products_image'))) {
?>
          <table border="0" cellspacing="0" cellpadding="2" align="right">
            <tr>
              <td align="center" class="smallText">
<script language="javascript"><!--
document.write('<?php echo '<a href="javascript:popupWindow(\\\'' . tep_href_link(FILENAME_POPUP_IMAGE, 'pID=' . $Qproduct->valueInt('products_id')) . '\\\')">' . tep_image(DIR_WS_IMAGES . $Qproduct->value('products_image'), addslashes($Qproduct->value('products_name')), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '<br>' . TEXT_CLICK_TO_ENLARGE . '</a>'; ?>');
//--></script>
<noscript>
<?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES . $Qproduct->value('products_image')) . '" target="_blank">' . tep_image(DIR_WS_IMAGES . $Qproduct->value('products_image'), $Qproduct->value('products_name'), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '<br>' . TEXT_CLICK_TO_ENLARGE . '</a>'; ?>
</noscript>
              </td>
            </tr>
          </table>
<?php
    }
?>
          <p><?php echo $Qproduct->value('products_description'); ?></p>
<?php
    $Qattributes = $osC_Database->query('select count(*) as total from :table_products_options popt, :table_products_attributes patrib where patrib.products_id = :products_id and patrib.options_id = popt.products_options_id and popt.language_id = :language_id');
    $Qattributes->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
    $Qattributes->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
    $Qattributes->bindInt(':products_id', $_GET['products_id']);
    $Qattributes->bindInt(':language_id', $osC_Session->value('languages_id'));
    $Qattributes->execute();

    if ($Qattributes->valueInt('total') > 0) {
?>
          <table border="0" cellspacing="0" cellpadding="2">
            <tr>
              <td class="main" colspan="2"><?php echo TEXT_PRODUCT_OPTIONS; ?></td>
            </tr>
<?php
      $Qoptions = $osC_Database->query('select distinct popt.products_options_id, popt.products_options_name from :table_products_options popt, :table_products_attributes patrib where patrib.products_id = :products_id and patrib.options_id = popt.products_options_id and popt.language_id = :language_id order by popt.products_options_name');
      $Qoptions->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
      $Qoptions->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
      $Qoptions->bindInt(':products_id', $_GET['products_id']);
      $Qoptions->bindInt(':language_id', $osC_Session->value('languages_id'));
      $Qoptions->execute();

      while ($Qoptions->next()) {
        $products_options_array = array();

        $Qvalues = $osC_Database->query('select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from :table_products_attributes pa, :table_products_options_values pov where pa.products_id = :products_id and pa.options_id = :options_id and pa.options_values_id = pov.products_options_values_id and pov.language_id = :language_id');
        $Qvalues->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
        $Qvalues->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
        $Qvalues->bindInt(':products_id', $_GET['products_id']);
        $Qvalues->bindInt(':options_id', $Qoptions->valueInt('products_options_id'));
        $Qvalues->bindInt(':language_id', $osC_Session->value('languages_id'));
        $Qvalues->execute();

        while ($Qvalues->next()) {
          $products_options_array[] = array('id' => $Qvalues->valueInt('products_options_values_id'), 'text' => $Qvalues->value('products_options_values_name'));

          if ($Qvalues->value('options_values_price') != '0') {
            $products_options_array[sizeof($products_options_array)-1]['text'] .= ' (' . $Qvalues->value('price_prefix') . $osC_Currencies->displayPrice($Qvalues->value('options_values_price'), $Qproduct->valueInt('products_tax_class_id')) .') ';
          }
        }

        if (isset($cart->contents[$_GET['products_id']]['attributes'][$Qoptions->valueInt('products_options_id')])) {
          $selected_attribute = $cart->contents[$_GET['products_id']]['attributes'][$Qoptions->valueInt('products_options_id')];
        } else {
          $selected_attribute = false;
        }
?>
            <tr>
              <td class="main"><?php echo $Qoptions->value('products_options_name') . ':'; ?></td>
              <td class="main"><?php echo osc_draw_pull_down_menu('id[' . $Qoptions->valueInt('products_options_id') . ']', $products_options_array, $selected_attribute); ?></td>
            </tr>
<?php
      }
?>
          </table>
<?php
    }
?>
        </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
    if ($osC_Services->isStarted('reviews')) {
      $Qreviews = $osC_Database->query('select count(*) as count from :table_reviews where products_id = :products_id');
      $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
      $Qreviews->bindInt(':products_id', $_GET['products_id']);
      $Qreviews->execute();

      if ($Qreviews->valueInt('count') > 0) {
?>
      <tr>
        <td class="main"><?php echo TEXT_CURRENT_REVIEWS . ' ' . $Qreviews->valueInt('count'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
      }
    }

    if (tep_not_null($Qproduct->value('products_url'))) {
?>
      <tr>
        <td class="main"><?php echo sprintf(TEXT_MORE_INFORMATION, tep_href_link(FILENAME_REDIRECT, 'action=url&goto=' . urlencode($Qproduct->value('products_url')), 'NONSSL', true, false)); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
    }

    if ($Qproduct->value('products_date_available') > date('Y-m-d H:i:s')) {
?>
      <tr>
        <td align="center" class="smallText"><?php echo sprintf(TEXT_DATE_AVAILABLE, tep_date_long($Qproduct->value('products_date_available'))); ?></td>
      </tr>
<?php
    } else {
?>
      <tr>
        <td align="center" class="smallText"><?php echo sprintf(TEXT_DATE_ADDED, tep_date_long($Qproduct->value('products_date_added'))); ?></td>
      </tr>
<?php
    }
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main">
<?php
  if ($osC_Services->isStarted('reviews')) {
    echo '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params()) . '">' . tep_image_button('button_reviews.gif', IMAGE_BUTTON_REVIEWS) . '</a>';
  }
?>
                </td>
                <td class="main" align="right"><?php echo osc_draw_hidden_field('products_id', $Qproduct->valueInt('products_id')) . tep_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART); ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><?php include(DIR_WS_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS); ?></td>
      </tr>
<?php
  }
?>
    </table></form></td>
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
