<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' Batch Delete'; ?></div>
<div class="infoBoxContent">
  <form name="oDeleteBatch" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&action=batchDelete'); ?>" method="post">

  <p><?php echo TEXT_DELETE_BATCH_INTRO; ?></p>

<?php
  $Qorders = $osC_Database->query('select orders_id from :table_orders where orders_id in (":orders_id") order by orders_id');
  $Qorders->bindTable(':table_orders', TABLE_ORDERS);
  $Qorders->bindRaw(':orders_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qorders->execute();

  $names_string = '';

  while ( $Qorders->next() ) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qorders->valueInt('orders_id')) . '<b>#' . $Qorders->valueInt('orders_id') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2);
  }

  echo '<p>' . $names_string . '</p>';
?>

  <p><?php echo osc_draw_checkbox_field('restock', array(array('id' => '', 'text' => TEXT_INFO_RESTOCK_PRODUCT_QUANTITY))); ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
