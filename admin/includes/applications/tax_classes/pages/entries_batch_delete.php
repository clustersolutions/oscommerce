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
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_batch_delete_tax_rates'); ?></div>
<div class="infoBoxContent">
  <form name="trDeleteBatch" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&action=batch_delete_entries'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_delete_tax_rates'); ?></p>

<?php
  $Qentries = $osC_Database->query('select tax_rates_id, tax_description from :table_tax_rates where tax_rates_id in (":tax_rates_id") order by tax_description');
  $Qentries->bindTable(':table_tax_rates', TABLE_TAX_RATES);
  $Qentries->bindRaw(':tax_rates_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qentries->execute();

  $names_string = '';

  while ( $Qentries->next() ) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qentries->valueInt('tax_rates_id')) . '<b>' . $Qentries->valueProtected('tax_description') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';

  echo '<p align="center"><input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()]) . '\';" class="operationButton" /></p>';
?>

  </form>
</div>
