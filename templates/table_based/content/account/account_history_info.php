<?php
/*
  $Id:account_history_info.php 187 2005-09-14 14:22:13 +0200 (Mi, 14 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $order = new osC_Order($_GET['orders']);
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_history.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<div class="moduleBox">
  <div class="outsideHeading">
    <span style="float: right;">&nbsp;<br /><?php echo $osC_Language->get('order_total_heading') . ' ' . $order->info['total']; ?></span>

    <?php echo sprintf($osC_Language->get('order_number_heading'), $_GET['orders']) . ' <small>(' . $order->info['orders_status'] . ')</small><br />' . $osC_Language->get('order_date_heading') . ' ' . osC_DateTime::getLong($order->info['date_purchased']); ?>
  </div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="30%" valign="top">

<?php
  if ($order->delivery != false) {
?>
          <p><b><?php echo $osC_Language->get('order_delivery_address_title'); ?></b></p>
          <p><?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />'); ?></p>

<?php
    if (tep_not_null($order->info['shipping_method'])) {
?>

          <p><b><?php echo $osC_Language->get('order_shipping_method_title'); ?></b></p>
          <p><?php echo $order->info['shipping_method']; ?></p>

<?php
    }
  }
?>

          <p><b><?php echo $osC_Language->get('order_billing_address_title'); ?></b></p>
          <p><?php echo tep_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />'); ?></p>

          <p><b><?php echo $osC_Language->get('order_payment_method_title'); ?></b></p>
          <p><?php echo $order->info['payment_method']; ?></p>
        </td>
        <td width="70%" valign="top">
          <div style="border: 1px; border-style: solid; border-color: #CCCCCC; background-color: #FBFBFB; padding: 5px;">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if (sizeof($order->info['tax_groups']) > 1) {
?>
              <tr>
                <td colspan="2"><b><?php echo $osC_Language->get('order_products_title'); ?></b></td>
                <td align="right"><b><?php echo $osC_Language->get('order_tax_title'); ?></b></td>
                <td align="right"><b><?php echo $osC_Language->get('order_total_title'); ?></b></td>
              </tr>
<?php
  } else {
?>
              <tr>
                <td colspan="3"><b><?php echo $osC_Language->get('order_products_title'); ?></b></td>
              </tr>
<?php
  }

  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    echo '              <tr>' . "\n" .
         '                <td align="right" valign="top" width="30">' . $order->products[$i]['qty'] . '&nbsp;x</td>' . "\n" .
         '                <td valign="top">' . $order->products[$i]['name'];

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
  for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
    echo '              <tr>' . "\n" .
         '                <td align="right" width="100%">' . $order->totals[$i]['title'] . '</td>' . "\n" .
         '                <td>&nbsp;&nbsp;</td>' . "\n" .
         '                <td align="right">' . $order->totals[$i]['text'] . '</td>' . "\n" .
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
  <div class="outsideHeading"><?php echo $osC_Language->get('order_history_heading'); ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  while ($Qstatus->next()) {
    echo '    <tr>' . "\n" .
         '      <td valign="top" width="70">' . osC_DateTime::getShort($Qstatus->value('date_added')) . '</td>' . "\n" .
         '      <td valign="top" width="70">' . $Qstatus->value('orders_status_name') . '</td>' . "\n" .
         '      <td valign="top">' . (tep_not_null($Qstatus->valueProtected('comments')) ? nl2br($Qstatus->valueProtected('comments')) : '&nbsp;') . '</td>' . "\n" .
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
  <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'orders' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'SSL') . '">' . tep_image_button('button_back.gif', $osC_Language->get('button_back')) . '</a>'; ?>
</div>
