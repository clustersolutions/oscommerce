<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1></td>
    <td class="smallText" align="right">

<?php
  echo '<form name="orders" action="' . osc_href_link_admin(FILENAME_DEFAULT) . '" method="get">' . osc_draw_hidden_field($osC_Template->getModule()) .
       SEARCH_ORDER_ID . ' ' . osc_draw_input_field('oID') .
       SEARCH_CUSTOMER_ID . ' ' . osc_draw_input_field('cID') .
       SEARCH_STATUS . ' ' . osc_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)), $orders_statuses)) .
       '<input type="submit" value="GO" class="operationButton"></form>';
?>

    </td>
  </tr>
</table>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div id="infoBox_oDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_CUSTOMERS; ?></th>
        <th><?php echo TABLE_HEADING_ORDER_TOTAL; ?></th>
        <th><?php echo TABLE_HEADING_DATE_PURCHASED; ?></th>
        <th><?php echo TABLE_HEADING_STATUS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>

<?php
  $Qorders = $osC_Database->query('select o.orders_id, o.customers_ip_address, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from :table_orders o, :table_orders_total ot, :table_orders_status s where o.orders_id = ot.orders_id and ot.class = "total" and o.orders_status = s.orders_status_id and s.language_id = :language_id');

  if (isset($_GET['oID']) && is_numeric($_GET['oID'])) {
    $Qorders->appendQuery('and o.orders_id = :orders_id');
    $Qorders->bindInt(':orders_id', $_GET['oID']);
  }

  if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
    $Qorders->appendQuery('and o.customers_id = :customers_id');
    $Qorders->bindInt(':customers_id', $_GET['cID']);
  }

  if (isset($_GET['status']) && is_numeric($_GET['status'])) {
    $Qorders->appendQuery('and s.orders_status_id = :orders_status_id');
    $Qorders->bindInt(':orders_status_id', $_GET['status']);
  }

  $Qorders->appendQuery('order by o.last_modified desc, o.date_purchased desc, o.orders_id desc');
  $Qorders->bindTable(':table_orders', TABLE_ORDERS);
  $Qorders->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
  $Qorders->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
  $Qorders->bindInt(':language_id', $osC_Language->getID());
  $Qorders->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qorders->execute();

  while ($Qorders->next()) {
    if (!isset($oInfo) && (!isset($_GET['oID']) || (isset($_GET['oID']) && empty($_GET['oID'])) || (isset($_GET['oID']) && ($_GET['oID'] == $Qorders->valueInt('orders_id'))))) {
      $oInfo = new objectInfo($Qorders->toArray());
    }
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $Qorders->valueInt('orders_id') . '&action=oEdit'), osc_image('images/icons/preview.gif', ICON_PREVIEW) . '&nbsp;' . $Qorders->valueProtected('customers_name')); ?></td>
        <td><?php echo strip_tags($Qorders->value('order_total')); ?></td>
        <td><?php echo osC_DateTime::getShort($Qorders->value('date_purchased'), true); ?></td>
        <td><?php echo $Qorders->value('orders_status_name'); ?></td>
        <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&'. (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $Qorders->valueInt('orders_id') . '&action=oEdit'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;';

    if (isset($oInfo) && ($Qorders->valueInt('orders_id') == $oInfo->orders_id)) {
      echo osc_link_object('#', osc_icon('trash.png', IMAGE_DELETE), 'onclick="toggleInfoBox(\'oDelete\');"');
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $Qorders->valueInt('orders_id') . '&action=oDelete'), osc_icon('trash.png', IMAGE_DELETE));
    }
?>

        </td>
      </tr>

<?php
  }
?>

    </tbody>
  </table>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td class="smallText"><?php echo $Qorders->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td>
      <td class="smallText" align="right"><?php echo $Qorders->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
    </tr>
  </table>

<?php
  if ( (isset($_GET['oID']) && !empty($_GET['oID'])) || (isset($_GET['cID']) && !empty($_GET['cID'])) || (isset($_GET['status']) && !empty($_GET['status'])) ) {
?>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_RESET . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';"> '; ?></p>

<?php
  }
?>

</div>

<?php
  if (isset($oInfo)) {
?>

<div id="infoBox_oDelete" <?php if ($_GET['action'] != 'oDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' [#' . $oInfo->orders_id . '] ' . $oInfo->customers_name; ?></div>
  <div class="infoBoxContent">
    <form name="oDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&'. (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_id . '&action=deleteconfirm'); ?>" method="post">

    <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $oInfo->customers_name . '</b>'; ?></p>
    <p><?php echo osc_draw_checkbox_field('restock', array(array('id' => '', 'text' => TEXT_INFO_RESTOCK_PRODUCT_QUANTITY))); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'oDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>
