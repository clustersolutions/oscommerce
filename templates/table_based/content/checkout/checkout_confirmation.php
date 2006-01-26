<?php
/*
  $Id:checkout_confirmation.php 187 2005-09-14 14:22:13 +0200 (Mi, 14 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

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
  if ($_SESSION['sendto'] != false) {
?>
          <p><?php echo '<b>' . $osC_Language->get('order_delivery_address_title') . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping_address', 'SSL') . '"><span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span></a>'; ?></p>
          <p><?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />'); ?></p>

<?php
    if (tep_not_null($order->info['shipping_method'])) {
?>

          <p><?php echo '<b>' . $osC_Language->get('order_shipping_method_title') . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL') . '"><span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span></a>'; ?></p>
          <p><?php echo $order->info['shipping_method']; ?></p>

<?php
    }
  }
?>

          <p><?php echo '<b>' . $osC_Language->get('order_billing_address_title') . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT, 'payment_address', 'SSL') . '"><span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span></a>'; ?></p>
          <p><?php echo tep_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />'); ?></p>

          <p><?php echo '<b>' . $osC_Language->get('order_payment_method_title') . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL') . '"><span class="orderEdit">' . $osC_Language->get('order_text_edit_title') . '</span></a>'; ?></p>
          <p><?php echo $order->info['payment_method']; ?></p>
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

  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    echo '              <tr>' . "\n" .
         '                <td align="right" valign="top" width="30">' . $order->products[$i]['qty'] . '&nbsp;x</td>' . "\n" .
         '                <td valign="top">' . $order->products[$i]['name'];

    if (STOCK_CHECK == 'true') {
      echo tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty']);
    }

    if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
        echo '<br /><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small></nobr>';
      }
    }

    echo '</td>' . "\n";

    if (sizeof($order->info['tax_groups']) > 1) {
      echo '                <td valign="top" align="right">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n";
    }

    echo '                <td align="right" valign="top">' . $osC_Currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" .
         '              </tr>' . "\n";
  }
?>

            </table>

            <p>&nbsp;</p>

            <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  if (MODULE_ORDER_TOTAL_INSTALLED) {
    $order_total_modules->process();
    echo $order_total_modules->output();
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
  if (is_array($payment_modules->modules)) {
    if ($confirmation = $payment_modules->confirmation()) {
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
  if (isset($$payment->form_action_url)) {
    $form_action_url = $$payment->form_action_url;
  } else {
    $form_action_url = tep_href_link(FILENAME_CHECKOUT, 'process', 'SSL');
  }

  echo '<form name="checkout_confirmation" action="' . $form_action_url . '" method="post">';

  if (is_array($payment_modules->modules)) {
    echo $payment_modules->process_button();
  }

  echo tep_image_submit('button_confirm_order.gif', $osC_Language->get('button_confirm_order')) . '</form>';
?>

</div>
