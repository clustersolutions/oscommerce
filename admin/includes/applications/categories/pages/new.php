<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $categories_array = array(array('id' => '0',
                                  'text' => $osC_Language->get('top_category')));

  foreach ( $osC_CategoryTree->getArray() as $value ) {
    $id = explode('_', $value['id']);
    $id = end($id);

    $categories_array[] = array('id' => $id,
                                'text' => $value['title']);
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_new_category'); ?></div>
<div class="infoBoxContent">
  <form name="cNew" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&action=save'); ?>" method="post" enctype="multipart/form-data">

  <p><?php echo $osC_Language->get('introduction_new_category'); ?></p>

  <fieldset>
    <div><label for="parent_id"><?php echo $osC_Language->get('field_parent_category'); ?></label><?php echo osc_draw_pull_down_menu('parent_id', $categories_array, $current_category_id); ?></div>
    <div><label><?php echo $osC_Language->get('field_name'); ?></label>

<?php
  foreach ( $osC_Language->getAll() as $l ) {
    echo '<p>' . $osC_Language->showImage($l['code']) . '&nbsp;' . $l['name'] . '<br />' . osc_draw_input_field('categories_name[' . $l['id'] . ']') . '</p>';
  }
?>

    </div>
    <div><label for="categories_image"><?php echo $osC_Language->get('field_image'); ?></label><?php echo osc_draw_file_field('categories_image', true); ?></div>
    <div><label for="sort_order"><?php echo $osC_Language->get('field_sort_order'); ?></label><?php echo osc_draw_input_field('sort_order'); ?></div>
  </fieldset>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()]) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
