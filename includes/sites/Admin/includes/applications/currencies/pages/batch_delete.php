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

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_batch_delete_currency'); ?></div>
<div class="infoBoxContent">
  <form name="aDeleteBatch" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=batch_delete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_delete_currencies'); ?></p>

<?php
  $check_default_flag = false;

  $Qcurrencies = $osC_Database->query('select currencies_id, title, code from :table_currencies where currencies_id in (":currencies_id") order by title');
  $Qcurrencies->bindTable(':table_currencies', TABLE_CURRENCIES);
  $Qcurrencies->bindRaw(':currencies_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qcurrencies->execute();

  $names_string = '';

  while ( $Qcurrencies->next() ) {
    if ( $Qcurrencies->value('code') == DEFAULT_CURRENCY ) {
      $check_default_flag = true;
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qcurrencies->valueInt('currencies_id')) . '<b>' . $Qcurrencies->value('title') . ' (' . $Qcurrencies->value('code') . ')</b>, ';
  }

  if ( empty($names_string) === false ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';

  if ( $check_default_flag === false ) {
    echo '<p align="center"><input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" /></p>';
  } else {
    echo '<p><b>' . $osC_Language->get('introduction_delete_currency_invalid') . '</b></p>';

    echo '<p align="center"><input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" /></p>';
  }
?>

  </form>
</div>
