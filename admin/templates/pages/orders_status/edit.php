<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_OrdersStatus_Admin::getData($_GET['osID']));
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $osC_ObjectInfo->get('orders_status_name'); ?></div>
<div class="infoBoxContent">
  <form name="osEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&osID=' . $osC_ObjectInfo->get('orders_status_id') . '&action=save'); ?>" method="post">

  <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_INFO_ORDERS_STATUS_NAME . '</b>'; ?></td>
      <td width="60%">

<?php
  $Qsd = $osC_Database->query('select language_id, orders_status_name from :table_orders_status where orders_status_id = :orders_status_id');
  $Qsd->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
  $Qsd->bindInt(':orders_status_id', $osC_ObjectInfo->get('orders_status_id'));
  $Qsd->execute();

  $status_name = array();

  while ( $Qsd->next() ) {
    $status_name[$Qsd->valueInt('language_id')] = $Qsd->value('orders_status_name');
  }

  foreach ( $osC_Language->getAll() as $l ) {
    echo osc_image('../includes/languages/' . $l['code'] . '/images/' . $l['image'], $l['name']) . '&nbsp;' . osc_draw_input_field('name[' . $l['id'] . ']', (isset($status_name[$l['id']]) ? $status_name[$l['id']] : null)) . '<br />';
  }
?>

      </td>
    </tr>

<?php
    if ( $osC_ObjectInfo->get('orders_status_id') != DEFAULT_ORDERS_STATUS_ID ) {
?>

    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_SET_DEFAULT . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_checkbox_field('default'); ?></td>
    </tr>

<?php
    }
?>

  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
