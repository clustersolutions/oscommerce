<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_history.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  if (tep_count_customer_orders() > 0) {
    $Qhistory = $osC_Database->query('select o.orders_id, o.date_purchased, o.delivery_name, o.billing_name, ot.text as order_total, s.orders_status_name from :table_orders o, :table_orders_total ot, :table_orders_status s where o.customers_id = :customers_id and o.orders_id = ot.orders_id and ot.class = "ot_total" and o.orders_status = s.orders_status_id and s.language_id = :language_id order by orders_id desc');
    $Qhistory->bindTable(':table_orders', TABLE_ORDERS);
    $Qhistory->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
    $Qhistory->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
    $Qhistory->bindInt(':customers_id', $osC_Customer->id);
    $Qhistory->bindInt(':language_id', $osC_Session->value('languages_id'));
    $Qhistory->setBatchLimit((isset($_GET['page']) ? $_GET['page'] : 1), MAX_DISPLAY_ORDER_HISTORY);
    $Qhistory->execute();

    while ($Qhistory->next()) {
      $Qproducts = $osC_Database->query('select count(*) as count from :table_orders_products where orders_id = :orders_id');
      $Qproducts->bindTable(':table_orders_products', TABLE_ORDERS_PRODUCTS);
      $Qproducts->bindInt(':orders_id', $Qhistory->valueInt('orders_id'));
      $Qproducts->execute();

      if (tep_not_null($Qhistory->value('delivery_name'))) {
        $order_type = TEXT_ORDER_SHIPPED_TO;
        $order_name = $Qhistory->value('delivery_name');
      } else {
        $order_type = TEXT_ORDER_BILLED_TO;
        $order_name = $Qhistory->value('billing_name');
      }
?>

<div class="moduleBox">
  <div class="outsideHeading">
    <span style="float: right; text-align: right;"><?php echo TEXT_ORDER_STATUS . ' ' . $Qhistory->value('orders_status_name'); ?></span>

    <?php echo TEXT_ORDER_NUMBER . ' ' . $Qhistory->valueInt('orders_id'); ?>
  </div>

  <div class="content">
    <table border="0" width="100%" cellspacing="2" cellpadding="4">
      <tr>
        <td width="50%" valign="top"><?php echo '<b>' . TEXT_ORDER_DATE . '</b> ' . tep_date_long($Qhistory->value('date_purchased')) . '<br><b>' . $order_type . '</b> ' . tep_output_string_protected($order_name); ?></td>
        <td width="30%" valign="top"><?php echo '<b>' . TEXT_ORDER_PRODUCTS . '</b> ' . $Qproducts->valueInt('count') . '<br><b>' . TEXT_ORDER_COST . '</b> ' . strip_tags($Qhistory->value('order_total')); ?></td>
        <td width="20%"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'orders=' . $Qhistory->valueInt('orders_id') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'SSL') . '">' . tep_image_button('small_view.gif', SMALL_IMAGE_BUTTON_VIEW) . '</a>'; ?></td>
      </tr>
    </table>
  </div>
</div>

<?php
    }
?>

<div class="listingPageLinks">
  <span style="float: right;"><?php echo $Qhistory->displayBatchLinksPullDown(); ?></span>

  <?php echo $Qhistory->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_ORDERS); ?>
</div>

<?php
  } else {
?>

<div class="moduleBox">
  <div class="content">
    <?php echo TEXT_NO_PURCHASES; ?>
  </div>
</div>

<?php
  }
?>

<div class="submitFormButtons">
  <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
</div>
