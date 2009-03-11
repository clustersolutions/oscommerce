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

<div class="infoBoxHeading"><?php echo osc_icon('move.png') . ' ' . $osC_Language->get('action_heading_batch_move_categories'); ?></div>
<div class="infoBoxContent">
  <form name="cMoveBatch" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&action=batch_move'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_move_categories'); ?></p>

  <fieldset>

<?php
  $Qcategories = $osC_Database->query('select c.categories_id, cd.categories_name from :table_categories c, :table_categories_description cd where c.categories_id in (":categories_id") and c.categories_id = cd.categories_id and cd.language_id = :language_id order by cd.categories_name');
  $Qcategories->bindTable(':table_categories', TABLE_CATEGORIES);
  $Qcategories->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
  $Qcategories->bindRaw(':categories_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qcategories->bindInt(':language_id', $osC_Language->getID());
  $Qcategories->execute();

  $names_string = '';

  while ( $Qcategories->next() ) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qcategories->valueInt('categories_id')) . '<b>' . $Qcategories->value('categories_name') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2);
  }

  echo '<p>' . $names_string . '</p>';
?>

    <div><label for="new_category_id"><?php echo $osC_Language->get('field_parent_category'); ?></label><?php echo osc_draw_pull_down_menu('new_category_id', $categories_array); ?></div>
  </fieldset>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_move') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()]) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
