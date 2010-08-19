<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\Address;
  use osCommerce\OM\Core\Site\Shop\Tax;

  if ( $OSCOM_ShoppingCart->hasBillingMethod() ) {
    echo $OSCOM_PaymentModule->preConfirmationCheck();
  }
?>

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<div class="moduleBox">
  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="30%" valign="top">

<?php
  if ( $OSCOM_ShoppingCart->hasShippingAddress() ) {
?>
          <p><?php echo '<b>' . OSCOM::getDef('order_delivery_address_title') . '</b> ' . osc_link_object(OSCOM::getLink(null, 'Checkout', 'Shipping&Address', 'SSL'), '<span class="orderEdit">' . OSCOM::getDef('order_text_edit_title') . '</span>'); ?></p>
          <p><?php echo Address::format($OSCOM_ShoppingCart->getShippingAddress(), '<br />'); ?></p>

<?php
    if ( $OSCOM_ShoppingCart->hasShippingMethod() ) {
?>

          <p><?php echo '<b>' . OSCOM::getDef('order_shipping_method_title') . '</b> ' . osc_link_object(OSCOM::getLink(null, 'Checkout', 'Shipping', 'SSL'), '<span class="orderEdit">' . OSCOM::getDef('order_text_edit_title') . '</span>'); ?></p>
          <p><?php echo $OSCOM_ShoppingCart->getShippingMethod('title'); ?></p>

<?php
    }
  }
?>

          <p><?php echo '<b>' . OSCOM::getDef('order_billing_address_title') . '</b> ' . osc_link_object(OSCOM::getLink(null, 'Checkout', 'Billing&Address', 'SSL'), '<span class="orderEdit">' . OSCOM::getDef('order_text_edit_title') . '</span>'); ?></p>
          <p><?php echo Address::format($OSCOM_ShoppingCart->getBillingAddress(), '<br />'); ?></p>

          <p><?php echo '<b>' . OSCOM::getDef('order_payment_method_title') . '</b> ' . osc_link_object(OSCOM::getLink(null, 'Checkout', 'Billing', 'SSL'), '<span class="orderEdit">' . OSCOM::getDef('order_text_edit_title') . '</span>'); ?></p>
          <p><?php echo $OSCOM_ShoppingCart->getBillingMethod('title'); ?></p>
        </td>
        <td width="70%" valign="top">
          <div style="border: 1px; border-style: solid; border-color: #CCCCCC; background-color: #FBFBFB; padding: 5px;">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  if ( $OSCOM_ShoppingCart->numberOfTaxGroups() > 1 ) {
?>

              <tr>
                <td colspan="2"><?php echo '<b>' . OSCOM::getDef('order_products_title') . '</b> ' . osc_link_object(OSCOM::getLink(null, 'Checkout', null, 'SSL'), '<span class="orderEdit">' . OSCOM::getDef('order_text_edit_title') . '</span>'); ?></td>
                <td align="right"><b><?php echo OSCOM::getDef('order_tax_title'); ?></b></td>
                <td align="right"><b><?php echo OSCOM::getDef('order_total_title'); ?></b></td>
              </tr>

<?php
  } else {
?>

              <tr>
                <td colspan="3"><?php echo '<b>' . OSCOM::getDef('order_products_title') . '</b> ' . osc_link_object(OSCOM::getLink(null, 'Checkout', null, 'SSL'), '<span class="orderEdit">' . OSCOM::getDef('order_text_edit_title') . '</span>'); ?></td>
              </tr>

<?php
  }

  foreach ( $OSCOM_ShoppingCart->getProducts() as $products ) {
    echo '              <tr>' . "\n" .
         '                <td align="right" valign="top" width="30">' . $products['quantity'] . '&nbsp;x&nbsp;</td>' . "\n" .
         '                <td valign="top">' . $products['name'];

    if ( (STOCK_CHECK == '1') && !$OSCOM_ShoppingCart->isInStock($products['item_id']) ) {
      echo '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
    }

    if ( $OSCOM_ShoppingCart->isVariant($products['item_id']) ) {
      foreach ( $OSCOM_ShoppingCart->getVariant($products['item_id']) as $variant) {
        echo '<br />- ' . $variant['group_title'] . ': ' . $variant['value_title'];
      }
    }

    echo '</td>' . "\n";

    if ( $OSCOM_ShoppingCart->numberOfTaxGroups() > 1 ) {
      echo '                <td valign="top" align="right">' . Tax::displayTaxRateValue($products['tax']) . '</td>' . "\n";
    }

    echo '                <td align="right" valign="top">' . $OSCOM_Currencies->displayPrice($products['price'], $products['tax_class_id'], $products['quantity']) . '</td>' . "\n" .
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
    foreach ( $OSCOM_ShoppingCart->getOrderTotals() as $module ) {
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
  if ( $OSCOM_ShoppingCart->hasBillingMethod() ) {
    if ( $confirmation = $OSCOM_PaymentModule->confirmation() ) {
?>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('order_payment_information_title'); ?></h6>

  <div class="content">
    <p><?php echo $confirmation['title']; ?></p>

<?php
      if ( isset($confirmation['fields']) ) {
?>

    <table border="0" cellspacing="0" cellpadding="2">

<?php
        for ( $i=0, $n=sizeof($confirmation['fields']); $i<$n; $i++ ) {
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

      if ( isset($confirmation['text']) ) {
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

  if ( isset($_SESSION['comments']) && !empty($_SESSION['comments']) ) {
?>

<div class="moduleBox">
  <h6><?php echo '<b>' . OSCOM::getDef('order_comments_title') . '</b> ' . osc_link_object(OSCOM::getLink(null, 'Checkout', 'Payment', 'SSL'), '<span class="orderEdit">' . OSCOM::getDef('order_text_edit_title') . '</span>'); ?></h6>

  <div class="content">
    <?php echo nl2br(osc_output_string_protected($_SESSION['comments'])) . osc_draw_hidden_field('comments', $_SESSION['comments']); ?>
  </div>
</div>

<?php
  }
?>

<div class="submitFormButtons" style="text-align: right;">

<?php
  if ( $OSCOM_ShoppingCart->hasBillingMethod() && $OSCOM_PaymentModule->hasGateway() ) {
    $form_action_url = $OSCOM_PaymentModule->getGatewayURL();
  } else {
    $form_action_url = OSCOM::getLink(null, null, 'Process', 'SSL');
  }

  echo '<form name="checkout_confirmation" action="' . $form_action_url . '" method="post">';

  if ( $OSCOM_ShoppingCart->hasBillingMethod() ) {
    echo $OSCOM_PaymentModule->getProcessButton();
  }

  echo osc_draw_image_submit_button('button_confirm_order.gif', OSCOM::getDef('button_confirm_order')) . '</form>';
?>

</div>
