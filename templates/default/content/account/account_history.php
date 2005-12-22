<?php
/*
  $Id:account_history.php 187 2005-09-14 14:22:13 +0200 (Mi, 14 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_history.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  if (order::numberOfEntries() > 0) {
    $Qhistory = order::getListing(MAX_DISPLAY_ORDER_HISTORY);

    while ($Qhistory->next()) {
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
        <td width="50%" valign="top"><?php echo '<b>' . TEXT_ORDER_DATE . '</b> ' . tep_date_long($Qhistory->value('date_purchased')) . '<br /><b>' . $order_type . '</b> ' . tep_output_string_protected($order_name); ?></td>
        <td width="30%" valign="top"><?php echo '<b>' . TEXT_ORDER_PRODUCTS . '</b> ' . order::numberOfProducts($Qhistory->valueInt('orders_id')) . '<br /><b>' . TEXT_ORDER_COST . '</b> ' . strip_tags($Qhistory->value('order_total')); ?></td>
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
