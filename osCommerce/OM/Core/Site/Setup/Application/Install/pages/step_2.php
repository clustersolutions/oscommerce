<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;
?>

<script language="javascript" type="text/javascript">
  var dbServer = "<?php echo $_POST['DB_SERVER']; ?>";
  var dbUsername = "<?php echo $_POST['DB_SERVER_USERNAME']; ?>";
  var dbPassword = "<?php echo $_POST['DB_SERVER_PASSWORD']; ?>";
  var dbName = "<?php echo $_POST['DB_DATABASE']; ?>";
  var dbPort = "<?php echo $_POST['DB_SERVER_PORT']; ?>";
  var dbClass = "<?php echo $_POST['DB_DATABASE_CLASS']; ?>";
  var dbPrefix = "<?php echo $_POST['DB_TABLE_PREFIX']; ?>";

  var formSubmited = false;
  var formSuccess = false;

  function handleHttpResponse(data) {
    if (data.result == true) {
      $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/success.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_sample_data_imported'); ?></p>');

      formSuccess = true;

      $('#installForm').submit();
    } else {
      $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/failed.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_sample_data_import_error'); ?></p>'.replace('%s', data.error_message));

      formSubmited = false;
    }
  }

  function prepareDB() {
    if ( $('#DB_INSERT_SAMPLE_DATA').attr('checked') ) {
      if (formSubmited == true) {
        return false;
      }

      formSubmited = true;

      $('#mBox').css('visibility', 'visible').show();
      $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/progress.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_sample_data_importing'); ?></p>');

      $.post('<?php echo OSCOM::getRPCLink(null, null, 'DBImportSample'); ?>',
             'server=' + dbServer + '&username=' + dbUsername + '&password=' + dbPassword + '&name=' + dbName + '&port=' + dbPort + '&class=' + dbClass + '&prefix=' + dbPrefix,
             handleHttpResponse, 'json');
    } else {
      formSuccess = true;

      $('#installForm').submit();
    }
  }
</script>

<div class="mainBlock">
  <div class="stepsBox">
    <ol>
      <li><?php echo OSCOM::getDef('box_steps_step_1'); ?></li>
      <li style="font-weight: bold;"><?php echo OSCOM::getDef('box_steps_step_2'); ?></li>
      <li><?php echo OSCOM::getDef('box_steps_step_3'); ?></li>
    </ol>
  </div>

  <h1><?php echo OSCOM::getDef('page_title_installation'); ?></h1>

  <p><?php echo OSCOM::getDef('text_installation'); ?></p>
</div>

<div class="contentBlock">
  <div class="infoPane">
    <h3><?php echo OSCOM::getDef('box_info_step_2_title'); ?></h3>

    <div class="infoPaneContents">
      <p><?php echo OSCOM::getDef('box_info_step_2_text'); ?></p>
    </div>
  </div>

  <div id="mBox">
    <div id="mBoxContents"></div>
  </div>

  <div class="contentPane">
    <form name="install" id="installForm" action="<?php echo OSCOM::getLink(null, null, 'step=3'); ?>" method="post">

    <h2><?php echo OSCOM::getDef('page_heading_store_settings'); ?></h2>

    <table border="0" width="99%" cellspacing="0" cellpadding="5" class="inputForm">
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_store_name') . '<br />' . osc_draw_input_field('CFG_STORE_NAME', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_store_name_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_store_owner_name') . '<br />' . osc_draw_input_field('CFG_STORE_OWNER_NAME', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_store_owner_name_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_store_owner_email_address') . '<br />' . osc_draw_input_field('CFG_STORE_OWNER_EMAIL_ADDRESS', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_store_owner_email_address_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_administrator_username') . '<br />' . osc_draw_input_field('CFG_ADMINISTRATOR_USERNAME', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_administrator_username_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_administrator_password') . '<br />' . osc_draw_input_field('CFG_ADMINISTRATOR_PASSWORD', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_administrator_password_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo osc_draw_checkbox_field('DB_INSERT_SAMPLE_DATA', 'true', true) . '&nbsp;' . OSCOM::getDef('param_database_import_sample_data'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_database_import_sample_data_description'); ?></td>
      </tr>
    </table>

    <p align="right"><?php echo osc_draw_button(array('priority' => 'primary', 'icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, OSCOM::getDefaultSiteApplication()), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

<?php
  foreach ( $_POST as $key => $value ) {
    if ( ($key != 'x') && ($key != 'y') ) {
      if ( is_array($value) ) {
        for ( $i=0, $n=sizeof($value); $i<$n; $i++ ) {
          echo osc_draw_hidden_field($key . '[]', $value[$i]);
        }
      } else {
        echo osc_draw_hidden_field($key, $value);
      }
    }
  }
?>

    </form>
  </div>
</div>

<script type="text/javascript">
  $("#installForm").submit(function(e) {
    if ( formSuccess == false ) {
      e.preventDefault();

      prepareDB();
    }
  });
</script>
