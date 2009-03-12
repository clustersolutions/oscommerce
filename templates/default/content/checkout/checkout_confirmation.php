<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<?php echo osc_image(DIR_WS_IMAGES . $osC_Template->getPageImage(), $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<div class="moduleBox">
  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="30%" valign="top">

<?php
  if ($osC_ShoppingCart->hasShippingAddress()) {
?>
          <p><?php echo '<b>' . $osC_Language->get('order_delivery_address_title') . '</b> ' . osc_link_object(osc_href_link(FILENAME_CHECKOUT, 'shipping_address', 'SSL'), '<span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span>'); ?></p>
          <p><?php echo osC_Address::format($osC_ShoppingCart->getShippingAddress(), '<br />'); ?></p>

<?php
    if ($osC_ShoppingCart->hasShippingMethod()) {
?>

          <p><?php echo '<b>' . $osC_Language->get('order_shipping_method_title') . '</b> ' . osc_link_object(osc_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'), '<span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span>'); ?></p>
          <p><?php echo $osC_ShoppingCart->getShippingMethod('title'); ?></p>

<?php
    }
  }
?>

          <p><?php echo '<b>' . $osC_Language->get('order_billing_address_title') . '</b> ' . osc_link_object(osc_href_link(FILENAME_CHECKOUT, 'payment_address', 'SSL'), '<span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span>'); ?></p>
          <p><?php echo osC_Address::format($osC_ShoppingCart->getBillingAddress(), '<br />'); ?></p>

          <p><?php echo '<b>' . $osC_Language->get('order_payment_method_title') . '</b> ' . osc_link_object(osc_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'), '<span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span>'); ?></p>
          <p><?php echo $osC_ShoppingCart->getBillingMethod('title'); ?></p>
        </td>
        <td width="70%" valign="top">
          <div style="border: 1px; border-style: solid; border-color: #CCCCCC; background-color: #FBFBFB; padding: 5px;">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  if ($osC_ShoppingCart->numberOfTaxGroups() > 1) {
?>

              <tr>
                <td colspan="2"><?php echo '<b>' . $osC_Language->get('order_products_title') . '</b> ' . osc_link_object(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'), '<span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span>'); ?></td>
                <td align="right"><b><?php echo $osC_Language->get('order_tax_title'); ?></b></td>
                <td align="right"><b><?php echo $osC_Language->get('order_total_title'); ?></b></td>
              </tr>

<?php
  } else {
?>

              <tr>
                <td colspan="3"><?php echo '<b>' . $osC_Language->get('order_products_title') . '</b> ' . osc_link_object(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'), '<span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span>'); ?></td>
              </tr>

<?php
  }

  foreach ($osC_ShoppingCart->getProducts() as $products) {
    echo '              <tr>' . "\n" .
         '                <td align="right" valign="top" width="30">' . $products['quantity'] . '&nbsp;x&nbsp;</td>' . "\n" .
         '                <td valign="top">' . $products['name'];

    if ( (STOCK_CHECK == '1') && !$osC_ShoppingCart->isInStock($products['item_id']) ) {
      echo '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
    }

    if ( $osC_ShoppingCart->isVariant($products['item_id']) ) {
      foreach ( $osC_ShoppingCart->getVariant($products['item_id']) as $variant) {
        echo '<br />- ' . $variant['group_title'] . ': ' . $variant['value_title'];
      }
    }

    echo '</td>' . "\n";

    if ($osC_ShoppingCart->numberOfTaxGroups() > 1) {
      echo '                <td valign="top" align="right">' . osC_Tax::displayTaxRateValue($products['tax']) . '</td>' . "\n";
    }

    echo '                <td align="right" valign="top">' . $osC_Currencies->displayPrice($products['price'], $products['tax_class_id'], $products['quantity']) . '</td>' . "\n" .
         '              </tr>' . "\n";
  }
?>

            </table>

            <p>&nbsp;</p>

            <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
// HPDL
//  if ($osC_OrderTotal->hasActive()) {
//    foreach ($osC_OrderTotal->getResult() as $module) {
    foreach ($osC_ShoppingCart->getOrderTotals() as $module) {
      echo '              <tr>' . "\n" .
           '                <td align="right">' . $module['title'] . '</td>' . "\n" .
           '                <td align="right">' . $module['text'] . '</td>' . "\n" .
           '              </tr>';
    }
//  }
?>

            </table>
          </div>
        </td>
      </tr>
    </table>
  </div>
</div>

<?php
  if ($osC_Payment->hasActive()) {
    if ($confirmation = $osC_Payment->confirmation()) {
?>

<div class="moduleBox">
  <h6><?php echo $osC_Language->get('order_payment_information_title'); ?></h6>

  <div class="content">
    <p><?php echo $confirmation['title']; ?></p>

<?php
      if (isset($confirmation['fields'])) {
?>

    <table border="0" cellspacing="0" cellpadding="2">

<?php
        for ($i=0, $n=sizeof($confirmation['fields']); $i<$n; $i++) {
?>

      <tr>
        <td width="10">&nbsp;</td>
        <td><?php echo $confirmation['fields'][$i]['title']; ?></td>
        <td width="10">&nbsp;</td>
        <td><?php echo $confirmation['fields'][$i]['field']; ?></td>
      </tr>

<?php
        }
?>

    </table>

<?php
      }

      if (isset($confirmation['text'])) {
?>

    <p><?php echo $confirmation['text']; ?></p>

<?php
      }
?>

  </div>
</div>

<?php
    }
  }

  if (isset($_SESSION['comments']) && !empty($_SESSION['comments'])) {
?>

<div class="moduleBox">
  <h6><?php echo '<b>' . $osC_Language->get('order_comments_title') . '</b> ' . osc_link_object(osc_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'), '<span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span>'); ?></h6>

  <div class="content">
    <?php echo nl2br(osc_output_string_protected($_SESSION['comments'])) . osc_draw_hidden_field('comments', $_SESSION['comments']); ?>
  </div>
</div>

<?php
  }
?>

<div class="submitFormButtons" style="text-align: right;">

<?php
  if ($osC_Payment->hasActionURL()) {
    $form_action_url = $osC_Payment->getActionURL();
  } else {
    $form_action_url = osc_href_link(FILENAME_CHECKOUT, 'process', 'SSL');
  }

  echo '<form name="checkout_confirmation" action="' . $form_action_url . '" method="post">';

  if ($osC_Payment->hasActive()) {
    echo $osC_Payment->process_button();
  }

  echo osc_draw_image_submit_button('button_confirm_order.gif', $osC_Language->get('button_confirm_order')) . '</form>';
?>

</div>
