<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  if (!strstr($_SERVER['PHP_SELF'], FILENAME_ACCOUNT_HISTORY_INFO)) {
// Get last order id for checkout_success
    $Qorder = $osC_Database->query('select orders_id from :table_orders where customers_id = :customers_id order by orders_id desc limit 1');
    $Qorder->bindTable(':table_orders', TABLE_ORDERS);
    $Qorder->bindInt(':customers_id', $osC_Customer->id);
    $Qorder->execute();

    $last_order = $Qorders->valueInt('orders_id');
  } else {
    $last_order = (int)$_GET['order_id'];
  }

// Now get all downloadable products in that order
  $Qdownloads = $osC_Database->query('select date_format(o.date_purchased, "%Y-%m-%d") as date_purchased_day, opd.download_maxdays, op.products_name, opd.orders_products_download_id, opd.orders_products_filename, opd.download_count, opd.download_maxdays from :table_orders o, :table_orders_products op, :table_orders_products_downloads opd where o.customers_id = :customers_id and o.orders_id = :orders_id and o.orders_id = op.orders_id and op.orders_products_id = opd.orders_products_id and opd.orders_products_filename != ""');
  $Qdownloads->bindTable(':table_orders', TABLE_ORDERS);
  $Qdownloads->bindTable(':table_orders_products', TABLE_ORDERS_PRODUCTS);
  $Qdownloads->bindTable(':table_orders_products_downloads', TABLE_ORDERS_PRODUCTS_DOWNLOADS);
  $Qdownloads->bindInt(':customers_id', $osC_Customer->id);
  $Qdownloads->bindInt(':orders_id', $last_order);
  $Qdownloads->execute();

  if ($Qdownloads->numberOfRows() > 0) {
?>
<!-- downloads //-->
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><b><?php echo HEADING_DOWNLOAD; ?></b></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
<!-- list of products -->
<?php
    while ($Qdownloads->next()) {
// MySQL 3.22 does not have INTERVAL
      list($dt_year, $dt_month, $dt_day) = explode('-', $Qdownloads->value('date_purchased_day'));
      $download_timestamp = mktime(23, 59, 59, $dt_month, $dt_day + $Qdownloads->value('download_maxdays'), $dt_year);
      $download_expiry = date('Y-m-d H:i:s', $download_timestamp);
?>
          <tr class="infoBoxContents">
<!-- left box -->
<?php
// The link will appear only if:
// - Download remaining count is > 0, AND
// - The file is present in the DOWNLOAD directory, AND EITHER
// - No expiry date is enforced (maxdays == 0), OR
// - The expiry date is not reached
      if ( ($Qdownloads->valueInt('download_count') > 0) && (file_exists(DIR_FS_DOWNLOAD . $Qdownloads->value('orders_products_filename'))) && ( ($Qdownloads->value('download_maxdays') == 0) || ($download_timestamp > time())) ) {
        echo '            <td class="main"><a href="' . tep_href_link(FILENAME_DOWNLOAD, 'order=' . $last_order . '&id=' . $Qdownloads->valueInt('orders_products_download_id')) . '">' . $Qdownloads->value('products_name') . '</a></td>' . "\n";
      } else {
        echo '            <td class="main">' . $Qdownloads->value('products_name') . '</td>' . "\n";
      }
?>
<!-- right box -->
<?php
      echo '            <td class="main">' . TABLE_HEADING_DOWNLOAD_DATE . tep_date_long($download_expiry) . '</td>' . "\n" .
           '            <td class="main" align="right">' . $Qdownloads->valueInt('download_count') . TABLE_HEADING_DOWNLOAD_COUNT . '</td>' . "\n" .
           '          </tr>' . "\n";
    }
?>
          </tr>
        </table></td>
      </tr>
<?php
    if (!strstr($_SERVER['PHP_SELF'], FILENAME_ACCOUNT_HISTORY_INFO)) {
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="smalltext" colspan="4"><p><?php printf(FOOTER_DOWNLOAD, '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . HEADER_TITLE_MY_ACCOUNT . '</a>'); ?></p></td>
      </tr>
<?php
    }
?>
<!-- downloads_eof //-->
<?php
  }
?>
