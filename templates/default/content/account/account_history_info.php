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

  $order = new osC_Order($_GET['orders']);
?>

<?php echo osc_image(DIR_WS_IMAGES . $osC_Template->getPageImage(), $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<div class="moduleBox">
  <span style="float: right;"><h6><?php echo $osC_Language->get('order_total_heading') . ' ' . $order->info['total']; ?></h6></span>

  <h6><?php echo  $osC_Language->get('order_date_heading') . ' ' . osC_DateTime::getLong($order->info['date_purchased']) . ' <small>(' . $order->info['orders_status'] . ')</small>'; ?></h6>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="30%" valign="top">

<?php
  if ($order->delivery != false) {
?>

          <h6><?php echo $osC_Language->get('order_delivery_address_title'); ?></h6>

          <p><?php echo osC_Address::format($order->delivery, '<br />'); ?></p>

<?php
    if (!empty($order->info['shipping_method'])) {
?>

          <h6><?php echo $osC_Language->get('order_shipping_method_title'); ?></h6>

          <p><?php echo $order->info['shipping_method']; ?></p>

<?php
    }
  }
?>

          <h6><?php echo $osC_Language->get('order_billing_address_title'); ?></h6>

          <p><?php echo osC_Address::format($order->billing, '<br />'); ?></p>

          <h6><?php echo $osC_Language->get('order_payment_method_title'); ?></h6>

          <p><?php echo $order->info['payment_method']; ?></p>
        </td>
        <td width="70%" valign="top">
          <div style="border: 1px; border-style: solid; border-color: #CCCCCC; background-color: #FBFBFB; padding: 5px;">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  if (sizeof($order->info['tax_groups']) > 1) {
?>

              <tr>
                <td colspan="2"><h6><?php echo $osC_Language->get('order_products_title'); ?></h6></td>
                <td align="right"><h6><?php echo $osC_Language->get('order_tax_title'); ?></h6></td>
                <td align="right"><h6><?php echo $osC_Language->get('order_total_title'); ?></h6></td>
              </tr>

<?php
  } else {
?>

              <tr>
                <td colspan="3"><h6><?php echo $osC_Language->get('order_products_title'); ?></h6></td>
              </tr>

<?php
  }

  foreach ($order->products as $product) {
    echo '              <tr>' . "\n" .
         '                <td align="right" valign="top" width="30">' . $product['qty'] . '&nbsp;x</td>' . "\n" .
         '                <td valign="top">' . $product['name'];

    if (isset($product['attributes']) && (sizeof($product['attributes']) > 0)) {
      foreach ($product['attributes'] as $attribute) {
        echo '<br /><nobr><small>&nbsp;<i> - ' . $attribute['option'] . ': ' . $attribute['value'] . '</i></small></nobr>';
      }
    }

    echo '</td>' . "\n";

    if (sizeof($order->info['tax_groups']) > 1) {
      echo '                <td valign="top" align="right">' . osC_Tax::displayTaxRateValue($product['tax']) . '</td>' . "\n";
    }

    echo '                <td align="right" valign="top">' . $osC_Currencies->displayPriceWithTaxRate($product['price'], $product['tax'], $product['qty'], false, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" .
         '              </tr>' . "\n";
  }
?>

            </table>

            <p>&nbsp;</p>

            <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  foreach ($order->totals as $total) {
    echo '              <tr>' . "\n" .
         '                <td align="right">' . $total['title'] . '</td>' . "\n" .
         '                <td align="right">' . $total['text'] . '</td>' . "\n" .
         '              </tr>' . "\n";
  }
?>

            </table>
          </div>
        </td>
      </tr>
    </table>
  </div>
</div>

<?php
  $Qstatus = $order->getStatusListing();

  if ($Qstatus->numberOfRows() > 0) {
?>

<div class="moduleBox">
  <h6><?php echo $osC_Language->get('order_history_heading'); ?></h6>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
    while ($Qstatus->next()) {
      echo '    <tr>' . "\n" .
           '      <td valign="top" width="70">' . osC_DateTime::getShort($Qstatus->value('date_added')) . '</td>' . "\n" .
           '      <td valign="top" width="70">' . $Qstatus->value('orders_status_name') . '</td>' . "\n" .
           '      <td valign="top">' . (!osc_empty($Qstatus->valueProtected('comments')) ? nl2br($Qstatus->valueProtected('comments')) : '&nbsp;') . '</td>' . "\n" .
           '    </tr>' . "\n";
    }
?>

    </table>
  </div>
</div>

<?php
  }

  if (DOWNLOAD_ENABLED == '1') {
    include('includes/modules/downloads.php');
  }
?>


<div class="submitFormButtons">
  <?php echo osc_link_object(osc_href_link(FILENAME_ACCOUNT, 'orders' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'SSL'), osc_draw_image_button('button_back.gif', $osC_Language->get('button_back'))); ?>
</div>
