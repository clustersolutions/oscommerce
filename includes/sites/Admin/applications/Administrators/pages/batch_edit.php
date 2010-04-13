<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php echo osc_icon('administrators.png', $osC_Template->getPageTitle(), '32x32') . osc_link_object(OSCOM::getLink(), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('edit.png') . ' ' . OSCOM::getDef('action_heading_batch_edit_administrators'); ?></h3>

  <form name="aEditBatch" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'action=BatchSave'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_batch_edit_administrators'); ?></p>

<?php
  $Qadmins = $OSCOM_Database->query('select id, user_name from :table_administrators where id in (":id") order by user_name');
  $Qadmins->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
  $Qadmins->bindRaw(':id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qadmins->execute();

  $names_string = '';

  while ( $Qadmins->next() ) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qadmins->valueInt('id')) . '<b>' . $Qadmins->valueProtected('user_name') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2);
  }

  echo '<p>' . $names_string . '</p>';
?>

  <fieldset>
    <p><?php echo osc_draw_radio_field('mode', array(array('id' => OSCOM_Site_Admin_Application_Administrators_Administrators::ACCESS_MODE_ADD, 'text' => OSCOM::getDef('add_to')), array('id' => OSCOM_Site_Admin_Application_Administrators_Administrators::ACCESS_MODE_REMOVE, 'text' => OSCOM::getDef('remove_from')), array('id' => OSCOM_Site_Admin_Application_Administrators_Administrators::ACCESS_MODE_SET, 'text' => OSCOM::getDef('set_to'))), OSCOM_Site_Admin_Application_Administrators_Administrators::ACCESS_MODE_ADD); ?></p>

    <p><select name="accessModules" id="modulesList"><option value="-1" disabled="disabled">-- Access Modules --</option><option value="0"><?php echo OSCOM::getDef('global_access'); ?></option></select></p>

    <ul id="accessToModules" class="modulesListing"></ul>
  </fieldset>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>

<script type="text/javascript">
  var accessModules = <?php echo json_encode(OSCOM_Site_Admin_Application_Administrators_Administrators::getAccessModules()); ?>;
  var deleteAccessModuleIcon = '<?php echo osc_icon('uninstall.png'); ?>';

  var $modulesList = $('#modulesList');

  $.each(accessModules, function(i, item) {
    var sGroup = document.createElement('optgroup');
    sGroup.label = i;

    $.each(item, function(key, value) {
      var sOption = new Option(value['text'], value['id']);
      sOption.id = 'am' + value['id'];

      sGroup.appendChild(sOption);
    });

    $modulesList.append(sGroup); 
  });

  $('#modulesList').change(function() {
    if ( $('#modulesList :selected').val() == '0' ) {
      $('#accessToModules li').remove();
      $('#accessToModules').append('<li id="atm' + $('#modulesList :selected').val() + '">' + $('#modulesList :selected').text() + ' <span style="float: right;"><a href="#" onclick="removeAccessToModule(\'' + $('#modulesList :selected').val() + '\');">' + deleteAccessModuleIcon + '</a><input type="hidden" name="modules[]" value="' + $('#modulesList :selected').val() + '" /></span></li>');

      $('#modulesList').attr('disabled', 'disabled');
      $('#modulesList').val('-1');

      $('#accessToModules li').tsort();
    } else if ( $('#modulesList :selected').val() != '-1' ) {
      $('#accessToModules').append('<li id="atm' + $('#modulesList :selected').val() + '">' + $('#modulesList :selected').parent().attr('label') + ' &raquo; ' + $('#modulesList :selected').text() + ' <span style="float: right;"><a href="#" onclick="removeAccessToModule(\'' + $('#modulesList :selected').val() + '\');">' + deleteAccessModuleIcon + '</a><input type="hidden" name="modules[]" value="' + $('#modulesList :selected').val() + '" /></span></li>');

      $('#modulesList :selected').attr('disabled', 'disabled');
      $('#modulesList').val('-1');

      $('#accessToModules li').tsort();
    }
  });

  function removeAccessToModule(module) {
    if ( module == '0' ) {
      $('#modulesList').removeAttr('disabled');
      $('#modulesList :disabled').removeAttr('disabled');
      $('#modulesList :first').attr('disabled', 'disabled');
    } else {
      $('#am' + module).removeAttr('disabled');
    }

    $('#atm' + module).remove();
  }
</script>
