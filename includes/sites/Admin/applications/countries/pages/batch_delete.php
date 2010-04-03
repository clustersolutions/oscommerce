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

  $Qcountries = $osC_Database->query('select countries_id, countries_name from :table_countries where countries_id in (":countries_id") order by countries_name');
  $Qcountries->bindTable(':table_countries', TABLE_COUNTRIES);
  $Qcountries->bindRaw(':countries_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qcountries->execute();

  $names_string = '';

  while ( $Qcountries->next() ) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qcountries->valueInt('countries_id')) . '<b>' . $Qcountries->valueProtected('countries_name') . '</b>, ';
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

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_batch_delete_countries'); ?></div>
<div class="infoBoxContent">
  <form name="cDeleteBatch" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=batch_delete'); ?>" method="post">
    <p><?php echo $osC_Language->get('introduction_batch_delete_countries'); ?></p>
    <p><?php echo $names_string; ?></p>
    <p align="center"><?php echo '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>
  </form>
</div>
