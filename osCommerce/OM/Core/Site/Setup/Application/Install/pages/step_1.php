<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;

  $www_location = 'http://' . $_SERVER['HTTP_HOST'];

  if ( isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI']) ) {
    $www_location .= $_SERVER['REQUEST_URI'];
  } else {
    $www_location .= $_SERVER['SCRIPT_FILENAME'];
  }

  $www_location = substr($www_location, 0, strpos($www_location, 'index.php'));

  $db_table_types = array(array('id' => 'MySQL_Standard', 'text' => 'MySQL Standard'),
                          array('id' => 'MySQL_InnoDB', 'text' => 'MySQL InnoDB'));
?>

<script language="javascript" type="text/javascript">
  var dbServer;
  var dbUsername;
  var dbPassword;
  var dbName;
  var dbPort;
  var dbClass;
  var dbPrefix;

  var formSubmited = false;

  function handleHttpResponse_DoImport(data) {
    var result = /\[\[([^|]*?)(?:\|([^|]*?)){0,1}\]\]/.exec(data);
    result.shift();

    if (result[0] == '1') {
      $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/success.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_imported'); ?></p>');

      $('#installForm').submit();
    } else {
      $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/failed.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_import_error'); ?></p>'.replace('%s', result[1]));

      formSubmited = false;
    }
  }

  function handleHttpResponse(data) {
    var result = /\[\[([^|]*?)(?:\|([^|]*?)){0,1}\]\]/.exec(data);
    result.shift();

    if (result[0] == '1') {
      $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/progress.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_importing'); ?></p>');

      $.ajax({
        type: "POST",
        url: "<?php echo OSCOM::getRPCLink(null, null, 'DBImport'); ?>",
        data: "server=" + dbServer + "&username=" + dbUsername + "&password=" + dbPassword + "&name=" + dbName + "&port=" + dbPort + "&class=" + dbClass + "&import=0&prefix=" + dbPrefix,
        success: handleHttpResponse_DoImport
      });
    } else {
      $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/failed.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_connection_error'); ?></p>'.replace('%s', result[1]));

      formSubmited = false;
    }
  }

  function prepareDB() {
    if (formSubmited == true) {
      return false;
    }

    formSubmited = true;

    $('#mBox').css('visibility', 'visible').show();
    $('#mBoxContents').html('<p><img src="<?php echo OSCOM::getPublicSiteLink('templates/default/images/progress.gif'); ?>" align="right" hspace="5" vspace="5" border="0" /><?php echo OSCOM::getDef('rpc_database_connection_test'); ?></p>');

    dbServer = $('#DB_SERVER').val();
    dbUsername = $('#DB_SERVER_USERNAME').val();
    dbPassword = $('#DB_SERVER_PASSWORD').val();
    dbName = $('#DB_DATABASE').val();
    dbPort = $('#DB_SERVER_PORT').val();
    dbClass = $('#DB_DATABASE_CLASS').val();
    dbPrefix = $('#DB_TABLE_PREFIX').val();

    $.ajax({
      type: "POST",
      url: "<?php echo OSCOM::getRPCLink(null, null, 'DBCheck'); ?>",
      data: "server=" + dbServer + "&username=" + dbUsername + "&password=" + dbPassword + "&name=" + dbName + "&port=" + dbPort + "&class=" + dbClass,
      success: handleHttpResponse
    });
  }
</script>

<div class="mainBlock">
  <div class="stepsBox">
    <ol>
      <li style="font-weight: bold;"><?php echo OSCOM::getDef('box_steps_step_1'); ?></li>
      <li><?php echo OSCOM::getDef('box_steps_step_2'); ?></li>
      <li><?php echo OSCOM::getDef('box_steps_step_3'); ?></li>
    </ol>
  </div>

  <h1><?php echo OSCOM::getDef('page_title_installation'); ?></h1>

  <p><?php echo OSCOM::getDef('text_installation'); ?></p>
</div>

<div class="contentBlock">
  <div class="infoPane">
    <h3><?php echo OSCOM::getDef('box_info_step_1_title'); ?></h3>

    <div class="infoPaneContents">
      <p><?php echo OSCOM::getDef('box_info_step_1_text'); ?></p>
    </div>
  </div>

  <div id="mBox">
    <div id="mBoxContents"></div>
  </div>

  <div class="contentPane">
    <form name="install" id="installForm" action="<?php echo OSCOM::getLink(null, null, 'step=2'); ?>" method="post" onsubmit="prepareDB(); return false;">

    <h2><?php echo OSCOM::getDef('page_heading_web_server'); ?></h2>

    <table border="0" width="99%" cellspacing="0" cellpadding="5" class="inputForm">
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_web_address') . '<br />' . osc_draw_input_field('HTTP_WWW_ADDRESS', $www_location, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_web_address_description'); ?></td>
      </tr>
    </table>

    <br />

    <h2><?php echo OSCOM::getDef('page_heading_database_server'); ?></h2>

    <table border="0" width="99%" cellspacing="0" cellpadding="5" class="inputForm">
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_database_server') . '<br />' . osc_draw_input_field('DB_SERVER', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_database_server_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_database_username') . '<br />' . osc_draw_input_field('DB_SERVER_USERNAME', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_database_username_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_database_password') . '<br />' . osc_draw_input_field('DB_SERVER_PASSWORD', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_database_password_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_database_name') . '<br />' . osc_draw_input_field('DB_DATABASE', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_database_name_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_database_port') . '<br />' . osc_draw_input_field('DB_SERVER_PORT', null, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_database_port_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_database_type') . '<br />' . osc_draw_pull_down_menu('DB_DATABASE_CLASS', $db_table_types); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_database_type_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo OSCOM::getDef('param_database_prefix') . '<br />' . osc_draw_input_field('DB_TABLE_PREFIX', 'osc_', 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo OSCOM::getDef('param_database_prefix_description'); ?></td>
      </tr>
    </table>

    <p align="right"><?php echo osc_draw_button(array('priority' => 'primary', 'icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, OSCOM::getDefaultSiteApplication()), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

    </form>
  </div>
</div>
