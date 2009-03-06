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

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . $_GET['gID']), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_Language->get('action_heading_batch_edit_configuration_parameters'); ?></div>
<div class="infoBoxContent">
  <form name="cEditBatch" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . (int)$_GET['gID'] . '&action=batch_save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_edit_configuration_parameters'); ?></p>

  <fieldset>

<?php
  $Qcfg = $osC_Database->query('select configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, use_function, set_function from :table_configuration where configuration_id in (:configuration_id)');
  $Qcfg->bindTable(':table_configuration', TABLE_CONFIGURATION);
  $Qcfg->bindRaw(':configuration_id', implode(',', array_unique(array_filter($_POST['batch'], 'is_numeric'))));
  $Qcfg->execute();

  while ( $Qcfg->next() ) {
    if ( !osc_empty($Qcfg->value('set_function')) ) {
      $value_field = osc_call_user_func($Qcfg->value('set_function'), $Qcfg->value('configuration_value'), $Qcfg->value('configuration_key'));
    } else {
      $value_field = osc_draw_input_field('configuration[' . $Qcfg->value('configuration_key') . ']', $Qcfg->value('configuration_value'));
    }
?>

    <div><label for="configuration[<?php echo $Qcfg->valueProtected('configuration_key'); ?>]"><?php echo $Qcfg->valueProtected('configuration_title'); ?></label><?php echo $value_field . osc_draw_hidden_field('batch[]', $Qcfg->valueInt('configuration_id')); ?></div>

    <p><?php echo $Qcfg->value('configuration_description'); ?></p>

<?php
  }
?>

  </fieldset>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . $_GET['gID']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
