<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div id="infoBox_osDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_ORDERS_STATUS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>

<?php
  $Qstatuses = $osC_Database->query('select orders_status_id, orders_status_name from :table_orders_status where language_id = :language_id order by orders_status_name');
  $Qstatuses->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
  $Qstatuses->bindInt(':language_id', $osC_Language->getID());
  $Qstatuses->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qstatuses->execute();

  while ($Qstatuses->next()) {
    if (!isset($osInfo) && (!isset($_GET['osID']) || (isset($_GET['osID']) && ($_GET['osID'] == $Qstatuses->valueInt('orders_status_id'))))) {
      $osInfo = new objectInfo($Qstatuses->toArray());
    }
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td>

<?php
    if (DEFAULT_ORDERS_STATUS_ID == $Qstatuses->valueInt('orders_status_id')) {
      echo '<b>' . $Qstatuses->value('orders_status_name') . ' (' . TEXT_DEFAULT . ')</b>';
    } else {
      echo $Qstatuses->value('orders_status_name');
    }
?>

        </td>
        <td align="right">

<?php
    if (isset($osInfo) && ($Qstatuses->valueInt('orders_status_id') == $osInfo->orders_status_id)) {
      echo osc_link_object('#', osc_icon('configure.png', IMAGE_EDIT), 'onclick="toggleInfoBox(\'osEdit\');"') . '&nbsp;' .
           osc_link_object('#', osc_icon('trash.png', IMAGE_DELETE), 'onclick="toggleInfoBox(\'osDelete\');"');
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&osID=' . $Qstatuses->valueInt('orders_status_id') . '&action=osEdit'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&osID=' . $Qstatuses->valueInt('orders_status_id') . '&action=osDelete'), osc_icon('trash.png', IMAGE_DELETE));
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
      <td class="smallText"><?php echo $Qstatuses->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS); ?></td>
      <td class="smallText" align="right"><?php echo $Qstatuses->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'osNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_osNew" <?php if ($_GET['action'] != 'osNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_ORDERS_STATUS; ?></div>
  <div class="infoBoxContent">
    <form name="osNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=save'); ?>" method="post">

    <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_ORDERS_STATUS_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%">

<?php
  foreach ($osC_Language->getAll() as $l) {
    echo osc_image('../includes/languages/' . $l['code'] . '/images/' . $l['image'], $l['name']) . '&nbsp;' . osc_draw_input_field('orders_status_name[' . $l['id'] . ']') . '<br />';
  }
?>

        </td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_SET_DEFAULT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('default'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'osDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($osInfo)) {
?>

<div id="infoBox_osEdit" <?php if ($_GET['action'] != 'osEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $osInfo->orders_status_name; ?></div>
  <div class="infoBoxContent">
    <form name="osEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&osID=' . $osInfo->orders_status_id . '&action=save'); ?>" method="post">

    <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_ORDERS_STATUS_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%">

<?php
    $Qsd = $osC_Database->query('select language_id, orders_status_name from :table_orders_status where orders_status_id = :orders_status_id');
    $Qsd->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
    $Qsd->bindInt(':orders_status_id', $osInfo->orders_status_id);
    $Qsd->execute();

    $status_name = array();
    while ($Qsd->next()) {
      $status_name[$Qsd->valueInt('language_id')] = $Qsd->value('orders_status_name');
    }

    foreach ($osC_Language->getAll() as $l) {
      echo osc_image('../includes/languages/' . $l['code'] . '/images/' . $l['image'], $l['name']) . '&nbsp;' . osc_draw_input_field('orders_status_name[' . $l['id'] . ']', (isset($status_name[$l['id']]) ? $status_name[$l['id']] : null)) . '<br />';
    }
?>

        </td>
      </tr>

<?php
    if (DEFAULT_ORDERS_STATUS_ID != $osInfo->orders_status_id) {
?>

      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_SET_DEFAULT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('default'); ?></td>
      </tr>

<?php
    }
?>

    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'osDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_osDelete" <?php if ($_GET['action'] != 'osDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $osInfo->orders_status_name; ?></div>
  <div class="infoBoxContent">
<?php
    $Qorders = $osC_Database->query('select count(*) as total from :table_orders where orders_status = :orders_status');
    $Qorders->bindTable(':table_orders', TABLE_ORDERS);
    $Qorders->bindInt(':orders_status', $osInfo->orders_status_id);
    $Qorders->execute();

    $Qhistory = $osC_Database->query('select count(*) as total from :table_orders_status_history where orders_status_id = :orders_status_id group by orders_id');
    $Qhistory->bindTable(':table_orders_status_history', TABLE_ORDERS_STATUS_HISTORY);
    $Qhistory->bindInt(':orders_status_id', $osInfo->orders_status_id);
    $Qhistory->execute();

    if ( (DEFAULT_ORDERS_STATUS_ID == $osInfo->orders_status_id) || ($Qorders->valueInt('total') > 0) || ($Qhistory->valueInt('total') > 0) ) {
      if (DEFAULT_ORDERS_STATUS_ID == $osInfo->orders_status_id) {
        echo '    <p><b>' . TEXT_INFO_DELETE_PROHIBITED . '</b></p>' . "\n";
      }

      if ($Qorders->valueInt('total') > 0) {
        echo '    <p><b>' . sprintf(TEXT_INFO_DELETE_PROHIBITED_ORDERS, $Qorders->valueInt('total')) . '</b></p>' . "\n";
      }

      if ($Qhistory->valueInt('total') > 0) {
        echo '    <p><b>' . sprintf(TEXT_INFO_DELETE_PROHIBITED_HISTORY, $Qhistory->valueInt('total')) . '</b></p>' . "\n";
      }

      echo '    <p align="center"><input type="button" value="' . IMAGE_BACK . '" onclick="toggleInfoBox(\'osDefault\');" class="operationButton"></p>' . "\n";
    } else {
?>

    <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $osInfo->orders_status_name . '</b>'; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&osID=' . $osInfo->orders_status_id . '&action=deleteconfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'osDefault\');" class="operationButton">'; ?></p>

<?php
    }
?>

  </div>
</div>

<?php
  }
?>
