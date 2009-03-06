<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $categories_array = array(array('id' => '0',
                                  'text' => $osC_Language->get('top_category')));

  foreach ($osC_CategoryTree->getArray() as $value) {
    $id = explode('_', $value['id']);
    $id = end($id);

    $categories_array[] = array('id' => $id,
                                'text' => $value['title']);
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_new_category'); ?></div>
<div class="infoBoxContent">
  <form name="cNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . '&action=save'); ?>" method="post" enctype="multipart/form-data">

  <p><?php echo $osC_Language->get('introduction_new_category'); ?></p>

  <p><?php echo $osC_Language->get('field_parent_category') . '<br />' . osc_draw_pull_down_menu('parent_id', $categories_array, $current_category_id); ?></p>

  <p>

<?php
  echo $osC_Language->get('field_name');

  foreach ($osC_Language->getAll() as $l) {
    echo '<br />' . $osC_Language->showImage($l['code']) . '&nbsp;' . osc_draw_input_field('categories_name[' . $l['id'] . ']');
  }
?>

  </p>

  <p><?php echo $osC_Language->get('field_image') . '<br />' . osc_draw_file_field('categories_image', true); ?></p>
  <p><?php echo $osC_Language->get('field_sort_order') . '<br />' . osc_draw_input_field('sort_order'); ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
