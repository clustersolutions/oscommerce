<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_account.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo$osC_Template->getPageTitle(); ?></h1>

<?php
  if ($messageStack->size('account') > 0) {
    echo $messageStack->output('account');
  }
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo MY_ACCOUNT_TITLE; ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="80" valign="top"><?php echo tep_image(DIR_WS_IMAGES . 'account_personal.gif'); ?></td>
        <td>
          <ul style="list-style-image: url(<?php echo tep_href_link(DIR_WS_IMAGES . 'arrow_green.gif', '', 'SSL'); ?>);">
            <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'edit', 'SSL') . '">' . MY_ACCOUNT_INFORMATION . '</a>'; ?></li>
            <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'address_book', 'SSL') . '">' . MY_ACCOUNT_ADDRESS_BOOK . '</a>'; ?></li>
            <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'password', 'SSL') . '">' . MY_ACCOUNT_PASSWORD . '</a>'; ?></li>
          </ul>
        </td>
      </tr>
    </table>
  </div>
</div>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo MY_ORDERS_TITLE; ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="80" valign="top"><?php echo tep_image(DIR_WS_IMAGES . 'account_orders.gif'); ?></td>
        <td>
          <ul style="list-style-image: url(<?php echo tep_href_link(DIR_WS_IMAGES . 'arrow_green.gif', '', 'SSL'); ?>);">
            <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'orders', 'SSL') . '">' . MY_ORDERS_VIEW . '</a>'; ?></li>
          </ul>

<?php
  if (tep_count_customer_orders() > 0) {
    $Qorders = $osC_Database->query('select o.orders_id, o.date_purchased, o.delivery_name, o.delivery_country, o.billing_name, o.billing_country, ot.text as order_total, s.orders_status_name from :table_orders o, :table_orders_total ot, :table_orders_status s where o.customers_id = :customers_id and o.orders_id = ot.orders_id and ot.class = "ot_total" and o.orders_status = s.orders_status_id and s.language_id = :language_id order by orders_id desc limit 3');
    $Qorders->bindTable(':table_orders', TABLE_ORDERS);
    $Qorders->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
    $Qorders->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
    $Qorders->bindInt(':customers_id', $osC_Customer->id);
    $Qorders->bindInt(':language_id', $osC_Session->value('languages_id'));
    $Qorders->execute();
?>

          <table border="0" width="100%" cellspacing="0" cellpadding="5">

<?php
    while ($Qorders->next()) {
      if (tep_not_null($Qorders->value('delivery_name'))) {
        $order_name = $Qorders->valueProtected('delivery_name');
        $order_country = $Qorders->value('delivery_country');
      } else {
        $order_name = $Qorders->valueProtected('billing_name');
        $order_country = $Qorders->value('billing_country');
      }
?>

            <tr class="moduleRow" onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);">
              <td width="50" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'orders=' . $Qorders->valueInt('orders_id'), 'SSL') . '">' . tep_image('templates/' . $osC_Template->getTemplate() . '/images/icons/16x16/package.png') . '</a>'; ?></td>
              <td><?php echo '#' . $Qorders->valueInt('orders_id'); ?></td>
              <td><?php echo tep_date_short($Qorders->value('date_purchased')); ?></td>
              <td><?php echo $order_name . ', ' . $order_country; ?></td>
              <td><?php echo $Qorders->value('orders_status_name'); ?></td>
              <td align="right"><?php echo $Qorders->value('order_total'); ?></td>
              <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'orders=' . $Qorders->valueInt('orders_id'), 'SSL') . '">' . tep_image_button('small_view.gif', SMALL_IMAGE_BUTTON_VIEW) . '</a>'; ?></td>
            </tr>

<?php
    }
?>

          </table>

<?php
  }
?>

        </td>
      </tr>
    </table>
  </div>
</div>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo MY_EMAIL_NOTIFICATIONS_TITLE; ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="80" valign="top"><?php echo tep_image(DIR_WS_IMAGES . 'account_notifications.gif'); ?></td>
        <td>
          <ul style="list-style-image: url(<?php echo tep_href_link(DIR_WS_IMAGES . 'arrow_green.gif', '', 'SSL'); ?>);">
            <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'newsletters', 'SSL') . '">' . MY_EMAIL_NOTIFICATIONS_NEWSLETTERS . '</a>'; ?></li>
            <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'notifications', 'SSL') . '">' . MY_EMAIL_NOTIFICATIONS_PRODUCTS . '</a>'; ?></li>
          </ul>
        </td>
      </tr>
    </table>
  </div>
</div>
