<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $categories_array = array(array('id' => '0', 'text' => TEXT_TOP));

  foreach ($osC_CategoryTree->getTree() as $value) {
    $categories_array[] = array('id' => $value['id'], 'text' => $value['title']);
  }
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' Batch Copy'; ?></div>
<div class="infoBoxContent">
  <form name="pDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . '&action=batchCopy'); ?>" method="post">

  <p><?php echo TEXT_INFO_COPY_TO_BATCH_INTRO; ?></p>

<?php
  $Qproducts = $osC_Database->query('select products_id, products_name from :table_products_description where products_id in (":products_id") and language_id = :language_id order by products_name');
  $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qproducts->bindRaw(':products_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qproducts->bindInt(':language_id', $osC_Language->getID());
  $Qproducts->execute();

  $names_string = '';

  while ($Qproducts->next()) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qproducts->valueInt('products_id')) . '<b>' . $Qproducts->value('products_name') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';
?>

  <p><?php echo TEXT_CATEGORIES . '<br />' . osc_draw_pull_down_menu('new_category_id', $categories_array); ?></p>

  <p><?php echo TEXT_HOW_TO_COPY . '<br />' . osc_draw_radio_field('copy_as', array(array('id' => 'link', 'text' => TEXT_COPY_AS_LINK), array('id' => 'duplicate', 'text' => TEXT_COPY_AS_DUPLICATE)), 'link', null, '<br />'); ?></p>

  <p align="center"><?php echo '<input type="submit" value="' . IMAGE_COPY . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
