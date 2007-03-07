<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $categories_array = array(array('id' => '0', 'text' => TEXT_TOP));

  foreach ($osC_CategoryTree->getTree() as $value) {
    $categories_array[] = array('id' => $value['id'], 'text' => $value['title']);
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('move.png', IMAGE_MOVE) . ' Batch Move'; ?></div>
<div class="infoBoxContent">
  <form name="cMoveBatch" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . '&action=batchMove'); ?>" method="post">

  <p><?php echo TEXT_MOVE_BATCH_INTRO; ?></p>

<?php
  $Qcategories = $osC_Database->query('select c.categories_id, cd.categories_name from :table_categories c, :table_categories_description cd where c.categories_id in (":categories_id") and c.categories_id = cd.categories_id and cd.language_id = :language_id order by cd.categories_name');
  $Qcategories->bindTable(':table_categories', TABLE_CATEGORIES);
  $Qcategories->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
  $Qcategories->bindRaw(':categories_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qcategories->bindInt(':language_id', $osC_Language->getID());
  $Qcategories->execute();

  $names_string = '';

  while ($Qcategories->next()) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qcategories->valueInt('categories_id')) . '<b>' . $Qcategories->value('categories_name') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2);
  }

  echo '<p>' . $names_string . '</p>';
?>

  <p><?php echo osc_draw_pull_down_menu('new_category_id', $categories_array); ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_MOVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
