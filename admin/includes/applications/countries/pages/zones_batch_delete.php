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

  $Qzones = $osC_Database->query('select zone_id, zone_name from :table_zones where zone_id in (":zone_id") order by zone_name');
  $Qzones->bindTable(':table_zones', TABLE_ZONES);
  $Qzones->bindRaw(':zone_id', implode('", "', array_unique(array_filter($_POST['batch'], 'is_numeric'))));
  $Qzones->execute();

  $names_string = '';

  while ( $Qzones->next() ) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qzones->valueInt('zone_id')) . '<b>' . $Qzones->valueProtected('zone_name') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_batch_delete_zones'); ?></div>
<div class="infoBoxContent">
  <form name="cDeleteBatch" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . (int)$_GET[$osC_Template->getModule()] . '&action=batch_delete_zones'); ?>" method="post">
    <p><?php echo $osC_Language->get('introduction_batch_delete_zones'); ?></p>
    <p><?php echo $names_string; ?></p>
    <p align="center"><?php echo '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . (int)$_GET[$osC_Template->getModule()]) . '\';" class="operationButton" />'; ?></p>
  </form>
</div>
