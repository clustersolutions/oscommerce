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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Categories_Admin::get($_GET['cID']));

  $categories_array = array(array('id' => '0',
                                  'text' => $osC_Language->get('top_category')));

  foreach ( $osC_CategoryTree->getArray() as $value ) {
    $categories_array[] = array('id' => $value['id'],
                                'text' => $value['title']);
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('move.png') . ' ' . $osC_ObjectInfo->getProtected('categories_name'); ?></div>
<div class="infoBoxContent">
  <form name="cMove" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&cID=' . $osC_ObjectInfo->getInt('categories_id') . '&action=move'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_move_category'); ?></p>

  <fieldset>
    <div><label for="new_category_id"><?php echo $osC_Language->get('field_parent_category'); ?></label><?php echo osc_draw_pull_down_menu('new_category_id', $categories_array, $osC_ObjectInfo->getInt('parent_id')); ?></div>
  </fieldset>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_move') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()]) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
