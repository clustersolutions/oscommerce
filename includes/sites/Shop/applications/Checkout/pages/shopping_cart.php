<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\OSCOM;
?>

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists('Checkout') ) {
    echo $OSCOM_MessageStack->get('Checkout');
  }
?>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('shopping_cart_heading'); ?></h6>

  <form name="shopping_cart" action="<?php echo OSCOM::getLink(null, null, 'action=cart_update', 'SSL'); ?>" method="post">

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
    $_cart_date_added = null;

    foreach ( $OSCOM_ShoppingCart->getProducts() as $products ) {
      if ( $products['date_added'] != $_cart_date_added ) {
        $_cart_date_added = $products['date_added'];
?>

      <tr>
        <td colspan="4"><?php echo sprintf(OSCOM::getDef('date_added_to_shopping_cart'), $products['date_added']); ?></td>
      </tr>

<?php
      }
?>

      <tr>
        <td valign="top" width="60"><?php echo osc_link_object(OSCOM::getLink(null, null, 'action=cart_remove&item=' . $products['item_id'], 'SSL'), osc_draw_image_button('small_delete.gif', OSCOM::getDef('button_delete'))); ?></td>
        <td valign="top">

<?php
      echo osc_link_object(OSCOM::getLink(null, 'Products', $products['keyword']), '<b>' . $products['name'] . '</b>');

      if ( (STOCK_CHECK == '1') && ($OSCOM_ShoppingCart->isInStock($products['item_id']) === false) ) {
        echo '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
      }

// HPDL      echo '&nbsp;(Top Category)';

      if ( $OSCOM_ShoppingCart->isVariant($products['item_id']) ) {
        foreach ( $OSCOM_ShoppingCart->getVariant($products['item_id']) as $variant) {
          echo '<br />- ' . $variant['group_title'] . ': ' . $variant['value_title'];
        }
      }
?>

        </td>
        <td valign="top"><?php echo osc_draw_input_field('products[' . $products['item_id'] . ']', $products['quantity'], 'size="4"'); ?> <a href="#" onclick="document.shopping_cart.submit(); return false;">update</a></td>
        <td valign="top" align="right"><?php echo '<b>' . $OSCOM_Currencies->displayPrice($products['price'], $products['tax_class_id'], $products['quantity']) . '</b>'; ?></td>
      </tr>

<?php
    }
?>

    </table>
  </div>

  </form>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
// HPDL
//    if ($osC_OrderTotal->hasActive()) {
//      foreach ($osC_OrderTotal->getResult() as $module) {
      foreach ( $OSCOM_ShoppingCart->getOrderTotals() as $module ) {
        echo '    <tr>' . "\n" .
             '      <td align="right">' . $module['title'] . '</td>' . "\n" .
             '      <td align="right">' . $module['text'] . '</td>' . "\n" .
             '    </tr>';
      }
//    }
?>

  </table>

<?php
    if ( (STOCK_CHECK == '1') && ($OSCOM_ShoppingCart->hasStock() === false) ) {
      if ( STOCK_ALLOW_CHECKOUT == '1' ) {
        echo '<p class="stockWarning" align="center">' . sprintf(OSCOM::getDef('products_out_of_stock_checkout_possible'), STOCK_MARK_PRODUCT_OUT_OF_STOCK) . '</p>';
      } else {
        echo '<p class="stockWarning" align="center">' . sprintf(OSCOM::getDef('products_out_of_stock_checkout_not_possible'), STOCK_MARK_PRODUCT_OUT_OF_STOCK) . '</p>';
      }
    }
?>

</div>

<?php
  if ( $OSCOM_Application->requireCustomerAccount() ) {
?>

<div class="submitFormButtons">
  <span style="float: right;">
    <?php echo osc_link_object(OSCOM::getLink(null, null, 'Confirmation', 'SSL'), osc_draw_image_button('button_checkout.gif', OSCOM::getDef('button_checkout'))); ?>
  </span>
</div>

<?php
  } else {
?>

<div class="moduleBox">
  <form name="checkout" action="<?php echo OSCOM::getLink(null, null, 'action=email', 'SSL'); ?>" method="post">

  <div class="content">
    <div style="float: right;">
      <?php echo osc_draw_image_submit_button('button_checkout.gif', OSCOM::getDef('button_checkout')); ?>
    </div>

    <?php echo 'E-Mail Address: ' . osc_draw_input_field('email', $OSCOM_Customer->getEMailAddress()) . ' or ' . osc_link_object(OSCOM::getLink(null, 'Account', 'LogIn', 'SSL'), 'Sign-In') . ' to process this order'; ?>
  </div>

  </form>
</div>

<?php
  }
?>
