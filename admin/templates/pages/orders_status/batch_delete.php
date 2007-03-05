<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' Batch Delete'; ?></div>
<div class="infoBoxContent">
  <form name="osDeleteBatch" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete'); ?>" method="post">

  <p><?php echo TEXT_DELETE_BATCH_INTRO; ?></p>

<?php
  $check_default_flag = false;
  $check_orders_flag = false;
  $check_history_flag = false;

  $Qstatuses = $osC_Database->query('select orders_status_id, orders_status_name from :table_orders_status where orders_status_id in (":orders_status_id") and language_id = :language_id order by orders_status_name');
  $Qstatuses->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
  $Qstatuses->bindRaw(':orders_status_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qstatuses->bindInt(':language_id', $osC_Language->getID());
  $Qstatuses->execute();

  $names_string = '';

  while ( $Qstatuses->next() ) {
    if ( $Qstatuses->value('orders_status_id') == DEFAULT_ORDERS_STATUS_ID ) {
      $check_default_flag = true;
    }

    $Qorders = $osC_Database->query('select count(*) as total from :table_orders where orders_status = :orders_status');
    $Qorders->bindTable(':table_orders', TABLE_ORDERS);
    $Qorders->bindInt(':orders_status', $Qstatuses->valueInt('orders_status_id'));
    $Qorders->execute();

    if ( $Qorders->valueInt('total') > 0 ) {
      $check_orders_flag = true;
    }

    $Qhistory = $osC_Database->query('select count(*) as total from :table_orders_status_history where orders_status_id = :orders_status_id group by orders_id');
    $Qhistory->bindTable(':table_orders_status_history', TABLE_ORDERS_STATUS_HISTORY);
    $Qhistory->bindInt(':orders_status_id', $Qstatuses->valueInt('orders_status_id'));
    $Qhistory->execute();

    if ( $Qhistory->valueInt('total') > 0 ) {
      $check_history_flag = true;
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qstatuses->valueInt('orders_status_id')) . '<b>' . $Qstatuses->value('orders_status_name') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';

  if ( ( $check_default_flag === true ) || ( $check_orders_flag === true ) || ( $check_history_flag === true ) ) {
    if ( $check_default_flag === true ) {
      echo '  <p><b>' . TEXT_INFO_DELETE_PROHIBITED . '</b></p>';
    }

    if ( $check_orders_flag === true ) {
      echo '  <p><b>' . sprintf(TEXT_INFO_DELETE_PROHIBITED_ORDERS, $Qorders->valueInt('total')) . '</b></p>';
    }

    if ( $check_history_flag === true ) {
      echo '  <p><b>' . sprintf(TEXT_INFO_DELETE_PROHIBITED_HISTORY, $Qhistory->valueInt('total')) . '</b></p>';
    }

    echo '  <p align="center"><input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
  } else {
    echo '  <p align="center"><input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
  }
?>

  </form>
</div>
