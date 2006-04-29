<?php
/*
  $Id:account.php 187 2005-09-14 14:22:13 +0200 (Mi, 14 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

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
  <div class="outsideHeading"><?php echo $osC_Language->get('my_account_title'); ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="80" valign="top"><?php echo tep_image(DIR_WS_IMAGES . 'account_personal.gif'); ?></td>
        <td>
          <ul style="list-style-image: url(<?php echo tep_href_link(DIR_WS_IMAGES . 'arrow_green.gif', '', 'SSL'); ?>);">
            <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'edit', 'SSL') . '">' . $osC_Language->get('my_account_information') . '</a>'; ?></li>
            <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'address_book', 'SSL') . '">' . $osC_Language->get('my_account_address_book') . '</a>'; ?></li>
            <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'password', 'SSL') . '">' . $osC_Language->get('my_account_password') . '</a>'; ?></li>
          </ul>
        </td>
      </tr>
    </table>
  </div>
</div>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo $osC_Language->get('my_orders_title'); ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="80" valign="top"><?php echo tep_image(DIR_WS_IMAGES . 'account_orders.gif'); ?></td>
        <td>
          <ul style="list-style-image: url(<?php echo tep_href_link(DIR_WS_IMAGES . 'arrow_green.gif', '', 'SSL'); ?>);">
            <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'orders', 'SSL') . '">' . $osC_Language->get('my_orders_view') . '</a>'; ?></li>
          </ul>

<?php
  if (osC_Order::numberOfEntries() > 0) {
?>

          <table border="0" width="100%" cellspacing="0" cellpadding="5">

<?php
    $Qorders = osC_Order::getListing(3);

    while ($Qorders->next()) {
      if (tep_not_null($Qorders->valueProtected('delivery_name'))) {
        $order_name = $Qorders->valueProtected('delivery_name');
        $order_country = $Qorders->valueProtected('delivery_country');
      } else {
        $order_name = $Qorders->valueProtected('billing_name');
        $order_country = $Qorders->valueProtected('billing_country');
      }
?>

            <tr class="moduleRow" onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
              <td width="50" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'orders=' . $Qorders->valueInt('orders_id'), 'SSL') . '">' . tep_image('templates/' . $osC_Template->getCode() . '/images/icons/16x16/package.png') . '</a>'; ?></td>
              <td><?php echo '#' . $Qorders->valueInt('orders_id'); ?></td>
              <td><?php echo osC_DateTime::getShort($Qorders->value('date_purchased')); ?></td>
              <td><?php echo $order_name . ', ' . $order_country; ?></td>
              <td><?php echo $Qorders->value('orders_status_name'); ?></td>
              <td align="right"><?php echo $Qorders->value('order_total'); ?></td>
              <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'orders=' . $Qorders->valueInt('orders_id'), 'SSL') . '">' . tep_image_button('small_view.gif', $osC_Language->get('button_view')) . '</a>'; ?></td>
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
  <div class="outsideHeading"><?php echo $osC_Language->get('my_notifications_title'); ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="80" valign="top"><?php echo tep_image(DIR_WS_IMAGES . 'account_notifications.gif'); ?></td>
        <td>
          <ul style="list-style-image: url(<?php echo tep_href_link(DIR_WS_IMAGES . 'arrow_green.gif', '', 'SSL'); ?>);">
            <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'newsletters', 'SSL') . '">' . $osC_Language->get('my_notifications_newsletters') . '</a>'; ?></li>
            <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'notifications', 'SSL') . '">' . $osC_Language->get('my_notifications_products') . '</a>'; ?></li>
          </ul>
        </td>
      </tr>
    </table>
  </div>
</div>
