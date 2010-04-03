<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php echo osc_link_object(OSCOM::getLink(null, null, 'gID=' . (int)$_GET['gID']), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('edit.png') . ' ' . __('action_heading_batch_edit_configuration_parameters'); ?></h3>

  <form name="cEditBatch" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'gID=' . (int)$_GET['gID'] . '&action=BatchSave'); ?>" method="post">

  <p><?php echo __('introduction_batch_edit_configuration_parameters'); ?></p>

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

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => __('button_save'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, null, 'gID=' . $_GET['gID']), 'priority' => 'secondary', 'icon' => 'close', 'title' => __('button_cancel'))); ?></p>

  </form>
</div>
