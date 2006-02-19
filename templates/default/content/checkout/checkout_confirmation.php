<?php
/*
  $Id:checkout_confirmation.php 187 2005-09-14 14:22:13 +0200 (Mi, 14 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_confirmation.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<div class="moduleBox">
  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="30%" valign="top">

<?php
  if ($osC_ShoppingCart->hasShippingAddress()) {
?>
          <p><?php echo '<b>' . $osC_Language->get('order_delivery_address_title') . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping_address', 'SSL') . '"><span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span></a>'; ?></p>
          <p><?php echo tep_address_format($osC_ShoppingCart->getShippingAddress('format_id'), $osC_ShoppingCart->getShippingAddress(), 1, ' ', '<br />'); ?></p>

<?php
    if ($osC_ShoppingCart->hasShippingMethod()) {
?>

          <p><?php echo '<b>' . $osC_Language->get('order_shipping_method_title') . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL') . '"><span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span></a>'; ?></p>
          <p><?php echo $osC_ShoppingCart->getShippingMethod('title'); ?></p>

<?php
    }
  }
?>

          <p><?php echo '<b>' . $osC_Language->get('order_billing_address_title') . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT, 'payment_address', 'SSL') . '"><span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span></a>'; ?></p>
          <p><?php echo tep_address_format($osC_ShoppingCart->getBillingAddress('format_id'), $osC_ShoppingCart->getBillingAddress(), 1, ' ', '<br />'); ?></p>

          <p><?php echo '<b>' . $osC_Language->get('order_payment_method_title') . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL') . '"><span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span></a>'; ?></p>
          <p><?php echo $osC_ShoppingCart->getBillingMethod('title'); ?></p>
        </td>
        <td width="70%" valign="top">
          <div style="border: 1px; border-style: solid; border-color: #CCCCCC; background-color: #FBFBFB; padding: 5px;">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if (sizeof($order->info['tax_groups']) > 1) {
?>
              <tr>
                <td colspan="2"><?php echo '<b>' . $osC_Language->get('order_products_title') . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT, '', 'SSL') . '"><span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span></a>'; ?></td>
                <td align="right"><b><?php echo $osC_Language->get('order_tax_title'); ?></b></td>
                <td align="right"><b><?php echo $osC_Language->get('order_total_title'); ?></b></td>
              </tr>
<?php
  } else {
?>
              <tr>
                <td colspan="3"><?php echo '<b>' . $osC_Language->get('order_products_title') . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT, '', 'SSL') . '"><span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span></a>'; ?></td>
              </tr>
<?php
  }

  foreach ($osC_ShoppingCart->getProducts() as $products) {
    echo '              <tr>' . "\n" .
         '                <td align="right" valign="top" width="30">' . $products['quantity'] . '&nbsp;x&nbsp;</td>' . "\n" .
         '                <td valign="top">' . $products['name'];

    if (STOCK_CHECK == 'true') {
      echo tep_check_stock($products['id'], $products['quantity']);
    }

    if ( (isset($products['attributes'])) && (sizeof($products['attributes']) > 0) ) {
      foreach ($products['attributes'] as $attributes) {
        echo '<br /><nobr><small>&nbsp;<i> - ' . $attributes['products_options_name'] . ': ' . $attributes['products_options_values_name'] . '</i></small></nobr>';
      }
    }

    echo '</td>' . "\n";

    if (sizeof($osC_ShoppingCart->_tax_groups) > 1) {
      echo '                <td valign="top" align="right">' . tep_display_tax_value($products['tax']) . '%</td>' . "\n";
    }

    echo '                <td align="right" valign="top">' . $osC_Currencies->format(tep_add_tax($products['final_price'], $products['tax_class_id']) * $products['quantity'], $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" .
         '              </tr>' . "\n";
  }
?>

            </table>

            <p>&nbsp;</p>

            <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
//  if ($osC_OrderTotal->hasActive()) {
//    foreach ($osC_OrderTotal->getResult() as $module) {
    foreach ($osC_ShoppingCart->getOrderTotals() as $module) {
      echo '              <tr>' . "\n" .
           '                <td align="right" class="main">' . $module['title'] . '</td>' . "\n" .
           '                <td align="right" class="main">' . $module['text'] . '</td>' . "\n" .
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
  <div class="outsideHeading">
    <?php echo $osC_Language->get('order_payment_information_title'); ?>
  </div>

  <div class="content">
    <table border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main" colspan="4"><?php echo $confirmation['title']; ?></td>
      </tr>
<?php
      for ($i=0, $n=sizeof($confirmation['fields']); $i<$n; $i++) {
?>
      <tr>
        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
        <td class="main"><?php echo $confirmation['fields'][$i]['title']; ?></td>
        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
        <td class="main"><?php echo $confirmation['fields'][$i]['field']; ?></td>
      </tr>
<?php
      }
?>
    </table>
  </div>
</div>

<?php
    }
  }

  if (tep_not_null($order->info['comments'])) {
?>

<div class="moduleBox">
  <div class="outsideHeading">
    <?php echo '<b>' . $osC_Language->get('order_comments_title') . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL') . '"><span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span></a>'; ?>
  </div>

  <div class="content">
    <?php echo nl2br(tep_output_string_protected($order->info['comments'])) . osc_draw_hidden_field('comments', $order->info['comments']); ?>
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
    $form_action_url = tep_href_link(FILENAME_CHECKOUT, 'process', 'SSL');
  }

  echo '<form name="checkout_confirmation" action="' . $form_action_url . '" method="post">';

  if ($osC_Payment->hasActive()) {
    echo $osC_Payment->process_button();
  }

  echo tep_image_submit('button_confirm_order.gif', $osC_Language->get('button_confirm_order')) . '</form>';
?>

</div>
