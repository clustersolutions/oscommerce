<?php
/*
  $Id: orders.php,v 1.3 2004/11/07 20:38:51 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><h1><?php echo HEADING_TITLE; ?></h1></td>
    <td class="smallText" align="right">
<?php
  echo tep_draw_form('orders', FILENAME_ORDERS, '', 'get') .
       SEARCH_ORDER_ID . ' ' . osc_draw_input_field('oID') .
       SEARCH_CUSTOMER_ID . ' ' . osc_draw_input_field('cID') .
       SEARCH_STATUS . ' ' . osc_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)), $orders_statuses)) .
       '<input type="submit" value="GO" class="operationButton"></form>';
?>
    </td>
  </tr>
</table>

<div id="infoBox_oDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
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
  $Qorders = $osC_Database->query('select o.orders_id, o.customers_ip_address, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from :table_orders o, :table_orders_total ot, :table_orders_status s where o.orders_id = ot.orders_id and ot.class = "ot_total" and o.orders_status = s.orders_status_id and s.language_id = :language_id');
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
  $Qorders->bindInt(':language_id', $osC_Session->value('languages_id'));
  $Qorders->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qorders->execute();

  while ($Qorders->next()) {
    if (!isset($oInfo) && (!isset($_GET['oID']) || (isset($_GET['oID']) && empty($_GET['oID'])) || (isset($_GET['oID']) && ($_GET['oID'] == $Qorders->valueInt('orders_id'))))) {
      $oInfo = new objectInfo($Qorders->toArray());
    }

    if (isset($oInfo) && ($Qorders->valueInt('orders_id') == $oInfo->orders_id)) {
      echo '      <tr class="selected">' . "\n";
    } else {
      echo '      <tr onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);" onClick="document.location.href=\'' . tep_href_link(FILENAME_ORDERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $Qorders->valueInt('orders_id')) . '\';">' . "\n";
    }
?>
        <td><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $Qorders->valueInt('orders_id') . '&action=oEdit') . '">' . tep_image('images/icons/preview.gif', ICON_PREVIEW) . '&nbsp;' . $Qorders->valueProtected('customers_name') . '</a>'; ?></td>
        <td><?php echo strip_tags($Qorders->value('order_total')); ?></td>
        <td><?php echo tep_datetime_short($Qorders->value('date_purchased')); ?></td>
        <td><?php echo $Qorders->value('orders_status_name'); ?></td>
        <td align="right">
<?php
    echo '<a href="#" onClick="document.location.href=\'' . tep_href_link(FILENAME_ORDERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $Qorders->valueInt('orders_id') . '&action=oEdit') . '\';">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;';

    if (isset($oInfo) && ($Qorders->valueInt('orders_id') == $oInfo->orders_id)) {
      echo '<a href="#" onClick="toggleInfoBox(\'oDelete\');">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_ORDERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $Qorders->valueInt('orders_id') . '&action=oDelete') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
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
      <td class="smallText" align="right"><?php echo $Qorders->displayBatchLinksPullDown(); ?></td>
    </tr>
  </table>

<?php
  if ( (isset($_GET['oID']) && !empty($_GET['oID'])) || (isset($_GET['cID']) && !empty($_GET['cID'])) || (isset($_GET['status']) && !empty($_GET['status'])) ) {
?>
  <p align="right"><?php echo '<input type="button" value="' . IMAGE_RESET . '" class="operationButton" onClick="document.location.href=\'' . tep_href_link(FILENAME_ORDERS) . '\';"> '; ?></p>
<?php
  }
?>

</div>

<?php
  if (isset($oInfo)) {
?>
<div id="infoBox_oDelete" <?php if ($action != 'oDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' [#' . $oInfo->orders_id . '] ' . $oInfo->customers_name; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('oDelete', FILENAME_ORDERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_id . '&action=deleteconfirm'); ?>

    <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $oInfo->customers_name . '</b>'; ?></p>
    <p><?php echo osc_draw_checkbox_field('restock', array(array('id' => '', 'text' => TEXT_INFO_RESTOCK_PRODUCT_QUANTITY))); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'oDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>
