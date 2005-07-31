<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/order.php');
  $order = new order($_GET['orders']);
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_history.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<div class="moduleBox">
  <div class="outsideHeading">
    <span style="float: right;">&nbsp;<br><?php echo HEADING_ORDER_TOTAL . ' ' . $order->info['total']; ?></span>

    <?php echo sprintf(HEADING_ORDER_NUMBER, $_GET['orders']) . ' <small>(' . $order->info['orders_status'] . ')</small><br>' . HEADING_ORDER_DATE . ' ' . tep_date_long($order->info['date_purchased']); ?>
  </div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="30%" valign="top">

<?php
  if ($order->delivery != false) {
?>
          <p><b><?php echo HEADING_DELIVERY_ADDRESS; ?></b></p>
          <p><?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br>'); ?></p>

<?php
    if (tep_not_null($order->info['shipping_method'])) {
?>

          <p><b><?php echo HEADING_SHIPPING_METHOD; ?></b></p>
          <p><?php echo $order->info['shipping_method']; ?></p>

<?php
    }
  }
?>

          <p><b><?php echo HEADING_BILLING_ADDRESS; ?></b></p>
          <p><?php echo tep_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br>'); ?></p>

          <p><b><?php echo HEADING_PAYMENT_METHOD; ?></b></p>
          <p><?php echo $order->info['payment_method']; ?></p>
        </td>
        <td width="70%" valign="top">
          <div style="border: 1px; border-style: solid; border-color: #CCCCCC; background-color: #FBFBFB; padding: 5px;">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if (sizeof($order->info['tax_groups']) > 1) {
?>
              <tr>
                <td colspan="2"><b><?php echo HEADING_PRODUCTS; ?></b></td>
                <td align="right"><b><?php echo HEADING_TAX; ?></b></td>
                <td align="right"><b><?php echo HEADING_TOTAL; ?></b></td>
              </tr>
<?php
  } else {
?>
              <tr>
                <td colspan="3"><b><?php echo HEADING_PRODUCTS; ?></b></td>
              </tr>
<?php
  }

  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    echo '              <tr>' . "\n" .
         '                <td align="right" valign="top" width="30">' . $order->products[$i]['qty'] . '&nbsp;x</td>' . "\n" .
         '                <td valign="top">' . $order->products[$i]['name'];

    if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
        echo '<br><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small></nobr>';
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
  $Qstatus = $osC_Database->query('select os.orders_status_name, osh.date_added, osh.comments from :table_orders_status os, :table_orders_status_history osh where osh.orders_id = :orders_id and osh.orders_status_id = os.orders_status_id and os.language_id = :language_id order by osh.date_added');
  $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
  $Qstatus->bindTable(':table_orders_status_history', TABLE_ORDERS_STATUS_HISTORY);
  $Qstatus->bindInt(':orders_id', $_GET['orders']);
  $Qstatus->bindInt(':language_id', $osC_Session->value('languages_id'));
  $Qstatus->execute();

  if ($Qstatus->numberOfRows() > 0) {
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo HEADING_ORDER_HISTORY; ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  while ($Qstatus->next()) {
    echo '    <tr>' . "\n" .
         '      <td valign="top" width="70">' . tep_date_short($Qstatus->value('date_added')) . '</td>' . "\n" .
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

  if (DOWNLOAD_ENABLED == 'true') {
    include('includes/modules/downloads.php');
  }
?>


<div class="submitFormButtons">
  <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'orders' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
</div>
