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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_ProductAttributes_Admin::getData($_GET['paID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->get('products_options_name'); ?></div>
<div class="infoBoxContent">
  <form name="paEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&paID=' . $osC_ObjectInfo->get('products_options_id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_attribute_group'); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%" valign="top"><?php echo '<b>' . $osC_Language->get('field_group_name') . '</b>'; ?></td>
      <td width="60%">

<?php
  $Qgd = $osC_Database->query('select language_id, products_options_name from :table_products_options where products_options_id = :products_options_id');
  $Qgd->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
  $Qgd->bindInt(':products_options_id', $osC_ObjectInfo->get('products_options_id'));
  $Qgd->execute();

  $group_names = array();

  while ($Qgd->next()) {
    $group_names[$Qgd->valueInt('language_id')] = $Qgd->value('products_options_name');
  }

  foreach ($osC_Language->getAll() as $l) {
    echo $osC_Language->showImage($l['code']) . '&nbsp;' .  osc_draw_input_field('group_name[' . $l['id'] . ']', (isset($group_names[$l['id']]) ? $group_names[$l['id']] : null)) . '<br />';
  }
?>

      </td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
