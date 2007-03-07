<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Categories_Admin::getData($_GET['cID']));

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

<div class="infoBoxHeading"><?php echo osc_icon('move.png', IMAGE_MOVE) . ' ' . $osC_ObjectInfo->get('categories_name'); ?></div>
<div class="infoBoxContent">
  <form name="cMove" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . '&cID=' . $osC_ObjectInfo->get('categories_id') . '&action=move'); ?>" method="post">

  <p><?php echo sprintf(TEXT_MOVE_CATEGORIES_INTRO, $osC_ObjectInfo->get('categories_name')); ?></p>

  <p><?php echo sprintf(TEXT_MOVE, $osC_ObjectInfo->get('categories_name')) . '<br />' . osc_draw_pull_down_menu('new_category_id', $categories_array, $osC_ObjectInfo->get('parent_id')); ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_MOVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
