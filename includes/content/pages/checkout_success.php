<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  $Qglobal = $osC_Database->query('select global_product_notifications from :table_customers_info where customers_info_id =:customers_info_id');
  $Qglobal->bindTable(':table_customers_info', TABLE_CUSTOMERS_INFO);
  $Qglobal->bindInt(':customers_info_id', $osC_Customer->id);
  $Qglobal->execute();

  if ($Qglobal->valueInt('global_product_notifications') !== 1) {
    $Qorder = $osC_Database->query('select orders_id from :table_orders where customers_id = :customers_id order by date_purchased desc limit 1');
    $Qorder->bindTable(':table_orders', TABLE_ORDERS);
    $Qorder->bindInt(':customers_id', $osC_Customer->id);
    $Qorder->execute();

    $Qproducts = $osC_Database->query('select products_id, products_name from :table_orders_products where orders_id = :orders_id order by products_name');
    $Qproducts->bindTable(':table_orders_products', TABLE_ORDERS_PRODUCTS);
    $Qproducts->bindInt(':orders_id', $Qorder->valueInt('orders_id'));
    $Qproducts->execute();

    $products_array = array();
    while ($Qproducts->next()) {
      $products_array[] = array('id' => $Qproducts->valueInt('products_id'),
                                'text' => $Qproducts->value('products_name'));
    }
  }
?>

<div class="pageHeading">
  <h1><?php echo HEADING_TITLE_CHECKOUT_SUCCESS; ?></h1>
</div>

<form name="order" action="<?php echo tep_href_link(FILENAME_CHECKOUT, 'success=update', 'SSL'); ?>" method="post">

<div>
  <div style="float: left;"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_man_on_board.gif', HEADING_TITLE_CHECKOUT_SUCCESS); ?></div>

  <div style="padding-top: 30px;">
    <p><?php echo TEXT_SUCCESS; ?></p>

    <p>
<?php
  if ($global['global_product_notifications'] != '1') {
    echo TEXT_NOTIFY_PRODUCTS . '<br><p class="productsNotifications">';

    $products_displayed = array();
    for ($i=0, $n=sizeof($products_array); $i<$n; $i++) {
      if (!in_array($products_array[$i]['id'], $products_displayed)) {
        echo osc_draw_checkbox_field('notify[]', $products_array[$i]['id']) . ' ' . $products_array[$i]['text'] . '<br>';
        $products_displayed[] = $products_array[$i]['id'];
      }
    }

    echo '</p>';
  } else {
    echo TEXT_SEE_ORDERS . '<br><br>' . TEXT_CONTACT_STORE_OWNER;
  }
?>
    </p>

    <h1 style="text-align: center;"><?php echo TEXT_THANKS_FOR_SHOPPING; ?></h1>
  </div>
</div>

<div class="submitFormButtons" style="text-align: right;">
  <?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?>
</div>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <?php if (DOWNLOAD_ENABLED == 'true') include('includes/modules/downloads.php'); ?>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
      </tr>
    </table></td>
    <td width="25%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
    <td width="25%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
    <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
        <td width="50%"><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="center" width="25%" class="checkoutBarFrom"><?php echo CHECKOUT_BAR_DELIVERY; ?></td>
    <td align="center" width="25%" class="checkoutBarFrom"><?php echo CHECKOUT_BAR_PAYMENT; ?></td>
    <td align="center" width="25%" class="checkoutBarFrom"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td>
    <td align="center" width="25%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_FINISHED; ?></td>
  </tr>
</table>

</form>
