<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $db_table_types = array(array('id' => 'mysql', 'text' => 'MySQL - MyISAM (Default)'),
                          array('id' => 'mysql_innodb', 'text' => 'MySQL - InnoDB (Transaction-Safe)'));
?>

<script language="javascript" type="text/javascript" src="../includes/javascript/xmlhttp/xmlhttp.js"></script>
<script language="javascript" type="text/javascript">
<!--

  var dbServer;
  var dbUsername;
  var dbPassword;
  var dbName;
  var dbClass;
  var dbImport = "1";
  var dbImportSample = "0";
  var dbPrefix;

  var formSubmited = false;

  function handleHttpResponse_DoImport() {
    if (http.readyState == 4) {
      if (http.status == 200) {
        var result = http.responseText.split(':osCRPC:', 2);

        if (result[0] == '1') {
          document.getElementById('mBoxContents').innerHTML = '<p><img src="images/success.gif" align="right" hspace="5" vspace="5" border="0" /><?php echo $osC_Language->get('rpc_database_imported'); ?></p>';

          setTimeout("document.getElementById('installForm').submit();", 2000);
        } else {
          document.getElementById('mBoxContents').innerHTML = '<p><img src="images/failed.gif" align="right" hspace="5" vspace="5" border="0" /><?php echo $osC_Language->get('rpc_database_import_error'); ?></p>'.replace('%s', result[1]);
        }
      }

      formSubmited = false;
    }
  }

  function handleHttpResponse() {
    if (http.readyState == 4) {
      if (http.status == 200) {
        var result = http.responseText.split(':osCRPC:', 2);

        if (result[0] == '1') {
          if (dbImport == '1') {
            document.getElementById('mBoxContents').innerHTML = '<p><img src="images/progress.gif" align="right" hspace="5" vspace="5" border="0" /><?php echo $osC_Language->get('rpc_database_importing'); ?></p>';

            loadXMLDoc("rpc.php?action=dbImport&server=" + urlEncode(dbServer) + "&username=" + urlEncode(dbUsername) + "&password=" + urlEncode(dbPassword) + "&name=" + urlEncode(dbName) + "&class=" + urlEncode(dbClass) + "&import=" + urlEncode(dbImportSample) + "&prefix=" + urlEncode(dbPrefix), handleHttpResponse_DoImport);
          } else {
            document.getElementById('mBoxContents').innerHTML = '<p><img src="images/success.gif" align="right" hspace="5" vspace="5" border="0" /><?php echo $osC_Language->get('rpc_database_connected'); ?></p>';

            setTimeout("document.getElementById('installForm').submit();", 2000);

            formSubmited = false
          }
        } else {
          document.getElementById('mBoxContents').innerHTML = '<p><img src="images/failed.gif" align="right" hspace="5" vspace="5" border="0" /><?php echo $osC_Language->get('rpc_database_connection_error'); ?></p>'.replace('%s', result[1]);
          formSubmited = false;
        }
      } else {
        formSubmited = false;
      }
    }
  }

  function prepareDB() {
    if (formSubmited == true) {
      return false;
    }

    formSubmited = true;

    showDiv(document.getElementById('mBox'));

    document.getElementById('mBoxContents').innerHTML = '<p><img src="images/progress.gif" align="right" hspace="5" vspace="5" border="0" /><?php echo $osC_Language->get('rpc_database_connection_test'); ?></p>';

    dbServer = document.getElementById("cfg_dbserver").value;
    dbUsername = document.getElementById("cfg_dbusername").value;
    dbPassword = document.getElementById("cfg_dbpassword").value;
    dbName = document.getElementById("cfg_dbname").value;
    dbClass = document.getElementById("cfg_dbclass").value;
    if (document.getElementById("cfg_dbimport") && document.getElementById("cfg_dbimport").checked) {
      dbImportSample = "1";
    }
    dbPrefix = document.getElementById("cfg_dbprefix").value;

    loadXMLDoc("rpc.php?action=dbCheck&server=" + urlEncode(dbServer) + "&username=" + urlEncode(dbUsername) + "&password=" + urlEncode(dbPassword) + "&name=" + urlEncode(dbName) + "&class=" + urlEncode(dbClass), handleHttpResponse);
  }

//-->
</script>

<div class="mainBlock">
  <div class="stepsBox">
    <ol>
      <li><?php echo $osC_Language->get('box_steps_step_1'); ?></li>
      <li style="font-weight: bold;"><?php echo $osC_Language->get('box_steps_step_2'); ?></li>
      <li><?php echo $osC_Language->get('box_steps_step_3'); ?></li>
      <li><?php echo $osC_Language->get('box_steps_step_4'); ?></li>
      <li><?php echo $osC_Language->get('box_steps_step_5'); ?></li>
    </ol>
  </div>

  <h1><?php echo $osC_Language->get('page_title_installation'); ?></h1>

  <?php echo $osC_Language->get('text_installation'); ?>
</div>

<div class="contentBlock">
  <div class="infoPane">
    <h3><?php echo $osC_Language->get('box_info_step_2_title'); ?></h3>

    <div class="infoPaneContents">
      <?php echo $osC_Language->get('box_info_step_2_text'); ?>
    </div>
  </div>

  <div id="mBox">
    <div id="mBoxContents"></div>
  </div>

  <div class="contentPane">
    <h2><?php echo $osC_Language->get('page_heading_step_2'); ?></h2>

    <form name="install" id="installForm" action="install.php?step=3" method="post" onsubmit="prepareDB(); return false;">

    <table border="0" width="99%" cellspacing="0" cellpadding="5" class="inputForm">
      <tr>
        <td class="inputField"><?php echo $osC_Language->get('param_database_server') . '<br />' . osc_draw_input_field('DB_SERVER', '', 'id="cfg_dbserver" class="text"'); ?></td>
        <td class="inputDescription"><?php echo $osC_Language->get('param_database_server_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo $osC_Language->get('param_database_username') . '<br />' . osc_draw_input_field('DB_SERVER_USERNAME', '', 'id="cfg_dbusername" class="text"'); ?></td>
        <td class="inputDescription"><?php echo $osC_Language->get('param_database_username_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo $osC_Language->get('param_database_password') . '<br />' . osc_draw_password_field('DB_SERVER_PASSWORD', 'id="cfg_dbpassword" class="text"'); ?></td>
        <td class="inputDescription"><?php echo $osC_Language->get('param_database_password_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo $osC_Language->get('param_database_name') . '<br />' . osc_draw_input_field('DB_DATABASE', '', 'id="cfg_dbname" class="text"'); ?></td>
        <td class="inputDescription"><?php echo $osC_Language->get('param_database_name_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo $osC_Language->get('param_database_type') . '<br />' . osc_draw_pull_down_menu('DB_DATABASE_CLASS', $db_table_types, '', 'id="cfg_dbclass"'); ?></td>
        <td class="inputDescription"><?php echo $osC_Language->get('param_database_type_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo $osC_Language->get('param_database_prefix') . '<br />' . osc_draw_input_field('DB_TABLE_PREFIX', 'osc_', 'id="cfg_dbprefix" class="text"'); ?></td>
        <td class="inputDescription"><?php echo $osC_Language->get('param_database_prefix_description'); ?></td>
      </tr>
    </table>

    <p align="right"><?php echo '<input type="image" src="templates/' . $template . '/languages/' . $osC_Language->getDirectory() . '/images/buttons/continue.gif" border="0" alt="' . $osC_Language->get('image_button_continue') . '" id="inputButton" />'; ?>&nbsp;&nbsp;<?php echo '<a href="index.php"><img src="templates/' . $template . '/languages/' . $osC_Language->getDirectory() . '/images/buttons/cancel.gif" border="0" alt="' . $osC_Language->get('image_button_cancel') . '" /></a>'; ?></p>

<?php
  foreach ($_POST as $key => $value) {
    if (($key != 'x') && ($key != 'y') && ($key != 'DB_SERVER') && ($key != 'DB_SERVER_USERNAME') && ($key != 'DB_SERVER_PASSWORD') && ($key != 'DB_DATABASE') && ($key != 'DB_DATABASE_CLASS') && ($key != 'DB_TABLE_PREFIX') && ($key != 'DB_INSERT_SAMPLE_DATA')) {
      if (is_array($value)) {
        for ($i=0, $n=sizeof($value); $i<$n; $i++) {
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
