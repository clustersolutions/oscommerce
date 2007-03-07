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
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' Batch Delete'; ?></div>
<div class="infoBoxContent">
  <form name="cDeleteBatch" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&action=batchDelete'); ?>" method="post">

  <p><?php echo TEXT_DELETE_BATCH_INTRO; ?></p>

<?php
  $Qcustomers = $osC_Database->query('select customers_id, customers_firstname, customers_lastname from :table_customers where customers_id in (":customers_id") order by customers_firstname, customers_lastname');
  $Qcustomers->bindTable(':table_customers', TABLE_CUSTOMERS);
  $Qcustomers->bindRaw(':customers_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qcustomers->execute();

  $names_string = '';

  while ( $Qcustomers->next() ) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qcustomers->valueInt('customers_id')) . '<b>' . $Qcustomers->valueProtected('customers_firstname') . ' ' . $Qcustomers->valueProtected('customers_lastname') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2);
  }

  echo '  <p>' . $names_string . '</p>';

  echo '  <p>' . osc_draw_checkbox_field('delete_reviews', null, true) . ' ' . TEXT_BATCH_DELETE_REVIEWS . '</p>';

?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&search=' . $_GET['search'] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
