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
  <form name="paeDeleteBatch" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&action=batchDeleteEntries'); ?>" method="post">

  <p><?php echo TEXT_INFO_DELETE_ATTRIBUTE_ENTRY_BATCH_INTRO; ?></p>

<?php
  $Qentries = $osC_Database->query('select products_options_values_id, products_options_values_name from :table_products_options_values where products_options_values_id in (":products_options_values_id") and language_id = :language_id order by products_options_values_name');
  $Qentries->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
  $Qentries->bindRaw(':products_options_values_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qentries->bindInt(':language_id', $osC_Language->getID());
  $Qentries->execute();

  $names_string = '';

  while ( $Qentries->next() ) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qentries->valueInt('products_options_values_id')) . '<b>' . $Qentries->value('products_options_values_name') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2);
  }

  echo '<p>' . $names_string . '</p>';
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
