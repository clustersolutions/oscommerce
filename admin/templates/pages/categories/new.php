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
    $categories_array[] = array('id' => $value['id'],
                                'text' => $value['title']);
  }
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_CATEGORY; ?></div>
<div class="infoBoxContent">
  <form name="cNew" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . '&action=save'); ?>" method="post" enctype="multipart/form-data">

  <p><?php echo TEXT_NEW_CATEGORY_INTRO; ?></p>

  <p><?php echo TEXT_EDIT_PARENT_CATEGORY . '<br />' . osc_draw_pull_down_menu('parent_id', $categories_array, $current_category_id); ?></p>

  <p>

<?php
  echo TEXT_CATEGORIES_NAME;

  foreach ($osC_Language->getAll() as $l) {
    echo '<br />' . osc_image('../includes/languages/' . $l['code'] . '/images/' . $l['image'], $l['name']) . '&nbsp;' . osc_draw_input_field('categories_name[' . $l['id'] . ']');
  }
?>

  </p>

  <p><?php echo TEXT_CATEGORIES_IMAGE . '<br />' . osc_draw_file_field('categories_image', true); ?></p>
  <p><?php echo TEXT_EDIT_SORT_ORDER . '<br />' . osc_draw_input_field('sort_order'); ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
