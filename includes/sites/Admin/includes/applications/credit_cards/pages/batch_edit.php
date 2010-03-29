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
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_Language->get('action_heading_batch_edit_cards'); ?></div>
<div class="infoBoxContent">
  <form name="ccEditBatch" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=batch_save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_edit_cards'); ?></p>

<?php
  $Qcc = $osC_Database->query('select id, credit_card_name from :table_credit_cards where id in (":id") order by credit_card_name');
  $Qcc->bindTable(':table_credit_cards', TABLE_CREDIT_CARDS);
  $Qcc->bindRaw(':id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qcc->execute();

  $names_string = '';

  while ( $Qcc->next() ) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qcc->valueInt('id')) . '<b>' . $Qcc->valueProtected('credit_card_name') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';

  echo '<p>' . osc_draw_radio_field('type', array(array('id' => 'activate', 'text' => $osC_Language->get('activate')), array('id' => 'deactivate', 'text' => $osC_Language->get('deactivate'))), 'activate') . '</p>';
?>

  <p align="center"><?php echo '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
