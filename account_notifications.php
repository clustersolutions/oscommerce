<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if ($osC_Customer->isLoggedOn() == false) {
    $navigation->set_snapshot();

    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

// needs to be included earlier to set the success message in the messageStack
  require(DIR_WS_LANGUAGES . $osC_Session->value('language') . '/' . FILENAME_ACCOUNT_NOTIFICATIONS);

  $Qglobal = $osC_Database->query('select global_product_notifications from :table_customers_info where customers_info_id = :customers_info_id');
  $Qglobal->bindTable(':table_customers_info', TABLE_CUSTOMERS_INFO);
  $Qglobal->bindInt(':customers_info_id', $osC_Customer->id);
  $Qglobal->execute();

  if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
    $updated = false;

    if (isset($_POST['product_global']) && is_numeric($_POST['product_global'])) {
      $product_global = tep_db_prepare_input($_POST['product_global']);
    } else {
      $product_global = '0';
    }

    if (isset($_POST['products'])) {
      (array)$products = $_POST['products'];
    } else {
      $products = array();
    }

    if ($product_global != $Qglobal->valueInt('global_product_notifications')) {
      $product_global = (($Qglobal->valueInt('global_product_notifications') == '1') ? '0' : '1');

      $Qupdate = $osC_Database->query('update :table_customers_info set global_product_notifications = :global_product_notifications where customers_info_id = :customers_info_id');
      $Qupdate->bindTable(':table_customers_info', TABLE_CUSTOMERS_INFO);
      $Qupdate->bindInt(':global_product_notifications', $product_global);
      $Qupdate->bindInt(':customers_info_id', $osC_Customer->id);
      $Qupdate->execute();

      if ($Qupdate->affectedRows() == 1) {
        $updated = true;
      }
    } elseif (sizeof($products) > 0) {
      $products_parsed = tep_array_filter($products, 'is_numeric');

      if (sizeof($products_parsed) > 0) {
        $Qcheck = $osC_Database->query('select count(*) as total from :table_products_notifications where customers_id = :customers_id and products_id not in :products_id');
        $Qcheck->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
        $Qcheck->bindInt(':customers_id', $osC_Customer->id);
        $Qcheck->bindRaw(':products_id', '(' . implode(',', $products_parsed) . ')');
        $Qcheck->execute();

        if ($Qcheck->valueInt('total') > 0) {
          $Qdelete = $osC_Database->query('delete from :table_products_notifications where customers_id = :customers_id and products_id not in :products_id');
          $Qdelete->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
          $Qdelete->bindInt(':customers_id', $osC_Customer->id);
          $Qdelete->bindRaw(':products_id', '(' . implode(',', $products_parsed) . ')');
          $Qdelete->execute();

          if ($Qdelete->affectedRows() > 0) {
            $updated = true;
          }
        }
      }
    } else {
      $Qcheck = $osC_Database->query('select count(*) as total from :table_products_notifications where customers_id = :customers_id');
      $Qcheck->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
      $Qcheck->bindInt(':customers_id', $osC_Customer->id);
      $Qcheck->execute();

      if ($Qcheck->valueInt('total') > 0) {
        $Qdelete = $osC_Database->query('delete from :table_products_notifications where customers_id = :customers_id');
        $Qdelete->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
        $Qdelete->bindInt(':customers_id', $osC_Customer->id);
        $Qdelete->execute();

        if ($Qdelete->affectedRows() > 0) {
          $updated = true;
        }
      }
    }

    if ($updated === true) {
      $messageStack->add_session('account', SUCCESS_NOTIFICATIONS_UPDATED, 'success');
    }

    tep_redirect(tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  }

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ACCOUNT_NOTIFICATIONS, '', 'SSL'));
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<script language="javascript"><!--
function rowOverEffect(object) {
  if (object.className == 'moduleRow') object.className = 'moduleRowOver';
}

function rowOutEffect(object) {
  if (object.className == 'moduleRowOver') object.className = 'moduleRow';
}

function checkBox(object) {
  document.account_notifications.elements[object].checked = !document.account_notifications.elements[object].checked;
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
    <td width="100%" valign="top"><?php echo tep_draw_form('account_notifications', tep_href_link(FILENAME_ACCOUNT_NOTIFICATIONS, '', 'SSL')) . osc_draw_hidden_field('action', 'process'); ?><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_account.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><b><?php echo MY_NOTIFICATIONS_TITLE; ?></b></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main"><?php echo MY_NOTIFICATIONS_DESCRIPTION; ?></td>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><b><?php echo GLOBAL_NOTIFICATIONS_TITLE; ?></b></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="checkBox('product_global')">
                    <td class="main" width="30"><?php echo osc_draw_checkbox_field('product_global', '1', $Qglobal->valueInt('global_product_notifications'), 'onclick="checkBox(\'product_global\')"'); ?></td>
                    <td class="main"><b><?php echo GLOBAL_NOTIFICATIONS_TITLE; ?></b></td>
                  </tr>
                  <tr>
                    <td width="30">&nbsp;</td>
                    <td class="main"><?php echo GLOBAL_NOTIFICATIONS_DESCRIPTION; ?></td>
                  </tr>
                </table></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  if ($Qglobal->valueInt('global_product_notifications') != '1') {
?>
      <tr>
        <td class="main"><b><?php echo NOTIFICATIONS_TITLE; ?></b></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
    $Qcheck = $osC_Database->query('select count(*) as total from :table_products_notifications where customers_id = :customers_id');
    $Qcheck->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
    $Qcheck->bindInt(':customers_id', $osC_Customer->id);
    $Qcheck->execute();

    if ($Qcheck->valueInt('total') > 0) {
?>
                  <tr>
                    <td class="main" colspan="2"><?php echo NOTIFICATIONS_DESCRIPTION; ?></td>
                  </tr>
<?php
      $counter = 0;

      $Qproducts = $osC_Database->query('select pd.products_id, pd.products_name from :table_products_description pd, :table_products_notifications pn where pn.customers_id = :customers_id and pn.products_id = pd.products_id and pd.language_id = :language_id order by pd.products_name');
      $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qproducts->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
      $Qproducts->bindInt(':customers_id', $osC_Customer->id);
      $Qproducts->bindInt(':language_id', $osC_Session->value('languages_id'));
      $Qproducts->execute();

      while ($Qproducts->next()) {
?>
                  <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="checkBox('products[<?php echo $counter; ?>]')">
                    <td class="main" width="30"><?php echo osc_draw_checkbox_field('products[' . $counter . ']', $Qproducts->valueInt('products_id'), true, 'onclick="checkBox(\'products[' . $counter . ']\')"'); ?></td>
                    <td class="main"><b><?php echo $Qproducts->value('products_name'); ?></b></td>
                  </tr>
<?php
        $counter++;
      }
    } else {
?>
                  <tr>
                    <td class="main"><?php echo NOTIFICATIONS_NON_EXISTING; ?></td>
                  </tr>
<?php
    }
?>
                </table></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
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
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
                <td align="right"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
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
