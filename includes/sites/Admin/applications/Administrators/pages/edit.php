<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(OSCOM_Site_Admin_Application_Administrators_Administrators::get($_GET['id']));
?>

<h1><?php echo osc_icon('administrators.png', $osC_Template->getPageTitle(), '32x32') . osc_link_object(OSCOM::getLink(), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->getProtected('user_name'); ?></h3>

  <form name="aEdit" class="dataForm" autocomplete="off" action="<?php echo OSCOM::getLink(null, null, 'id=' . $osC_ObjectInfo->getInt('id') . '&action=Save'); ?>" method="post">

  <p><?php echo __('introduction_edit_administrator'); ?></p>

  <fieldset>
    <p><label for="user_name"><?php echo __('field_username'); ?></label><?php echo osc_draw_input_field('user_name', $osC_ObjectInfo->get('user_name')); ?></p>
    <p><label for="user_password"><?php echo __('field_password'); ?></label><?php echo osc_draw_password_field('user_password'); ?></p>

    <p><select name="accessModules" id="modulesList"><option value="-1" disabled="disabled">-- Access Modules --</option><option value="0"><?php echo __('global_access'); ?></option></select></p>

    <ul id="accessToModules" class="modulesListing"></ul>
  </fieldset>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => __('button_save'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => __('button_cancel'))); ?></p>

  </form>
</div>

<script type="text/javascript">
  var accessModules = <?php echo json_encode(OSCOM_Site_Admin_Application_Administrators_Administrators::getAccessModules()); ?>;
  var hasAccessTo = <?php echo json_encode($osC_ObjectInfo->get('access_modules')); ?>;
  var deleteAccessModuleIcon = '<?php echo osc_icon('uninstall.png'); ?>';

  var $modulesList = $('#modulesList');

  $.each(accessModules, function(i, item) {
    var sGroup = document.createElement('optgroup');
    sGroup.label = i;

    $.each(item, function(key, value) {
      var sOption = new Option(value['text'], value['id']);
      sOption.id = 'am' + value['id'];

      sGroup.appendChild(sOption);

      if ( $.inArray(value['id'], hasAccessTo) != -1 ) {
        $('#accessToModules').append('<li id="atm' + value['id'] + '">' + i + ' &raquo; ' + value['text'] + ' <span style="float: right;"><a href="#" onclick="removeAccessToModule(\'' + value['id'] + '\');">' + deleteAccessModuleIcon + '</a><input type="hidden" name="modules[]" value="' + value['id'] + '" /></span></li>');
        sOption.disabled = 'disabled';
      }
    });

    $modulesList.append(sGroup); 
  });

  if ( $.inArray('*', hasAccessTo) != -1 ) {
    $('#modulesList').val('0');

    $('#accessToModules').append('<li id="atm' + $('#modulesList :selected').val() + '">' + $('#modulesList :selected').text() + ' <span style="float: right;"><a href="#" onclick="removeAccessToModule(\'' + $('#modulesList :selected').val() + '\');">' + deleteAccessModuleIcon + '</a><input type="hidden" name="modules[]" value="' + $('#modulesList :selected').val() + '" /></span></li>');

    $('#modulesList').attr('disabled', 'disabled');
    $('#modulesList').val('-1');
  }

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
