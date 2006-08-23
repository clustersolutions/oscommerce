<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $www_location = 'http://' . $_SERVER['HTTP_HOST'];

  if (isset($_SERVER['REQUEST_URI']) && (empty($_SERVER['REQUEST_URI']) === false)) {
    $www_location .= $_SERVER['REQUEST_URI'];
  } else {
    $www_location .= $_SERVER['SCRIPT_FILENAME'];
  }

  $www_location = substr($www_location, 0, strpos($www_location, 'install'));

  $dir_fs_www_root = osc_realpath(dirname(__FILE__) . '/../../../') . '/';
?>

<script language="javascript" type="text/javascript" src="../includes/javascript/xmlhttp/xmlhttp.js"></script>
<script language="javascript" type="text/javascript" src="../includes/javascript/xmlhttp/autocomplete.js"></script>
<script language="javascript" type="text/javascript">
<!--

  var cfgWork;
  var formSubmited = false;

  function handleHttpResponse() {
    if (http.readyState == 4) {
      if (http.status == 200) {
        var result = http.responseText.split(':osCRPC:', 2);

        if (result[0] == '1') {
          document.getElementById('mBoxContents').innerHTML = '<p><img src="images/success.gif" align="right" hspace="5" vspace="5" border="0" /><?php echo $osC_Language->get('rpc_work_directory_configured'); ?></p>';

          setTimeout("document.getElementById('installForm').submit();", 2000);
        } else if (result[0] == '0') {
          document.getElementById('mBoxContents').innerHTML = '<p><img src="images/failed.gif" align="right" hspace="5" vspace="5" border="0" /><?php echo $osC_Language->get('rpc_work_directory_error_not_writeable'); ?></p>'.replace('%s', result[1]);
        } else {
          document.getElementById('mBoxContents').innerHTML = '<p><img src="images/failed.gif" align="right" hspace="5" vspace="5" border="0" /><?php echo $osC_Language->get('rpc_work_directory_error_non_existent'); ?></p>'.replace('%s', result[1]);
        }
      }

      formSubmited = false;
    }
  }

  function prepareWork() {
    if (formSubmited == true) {
      return false;
    }

    if (returnUsed == true) {
      returnUsed = false;

      return false;
    }

    formSubmited = true;

    showDiv(document.getElementById('mBox'));

    document.getElementById('mBoxContents').innerHTML = '<p><img src="images/progress.gif" align="right" hspace="5" vspace="5" border="0" /><?php echo $osC_Language->get('rpc_work_directory_test'); ?></p>';

    cfgWork = document.getElementById("HTTP_WORK_DIRECTORY").value;

    loadXMLDoc("rpc.php?action=checkWorkDir&dir=" + urlEncode(cfgWork), handleHttpResponse);
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

    <form name="install" id="installForm" action="install.php?step=3" method="post" onsubmit="prepareWork(); return false;">

    <table border="0" width="99%" cellspacing="0" cellpadding="5" class="inputForm">
      <tr>
        <td class="inputField"><?php echo $osC_Language->get('param_web_address') . '<br />' . osc_draw_input_field('HTTP_WWW_ADDRESS', $www_location, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo $osC_Language->get('param_web_address_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo $osC_Language->get('param_web_root_directory') . '<br />' . osc_draw_input_field('DIR_FS_DOCUMENT_ROOT', $dir_fs_www_root, 'class="text"'); ?></td>
        <td class="inputDescription"><?php echo $osC_Language->get('param_web_root_directory_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo $osC_Language->get('param_web_work_directory') . '<br /><span style="white-space: nowrap;">' . osc_draw_input_field('HTTP_WORK_DIRECTORY', $dir_fs_www_root . 'includes/work', 'class="text"'); ?><img src="images/progress_pending.gif" border="0" width="22" height="22" id="HTTP_WORK_DIRECTORY_icon" /></span><div class="autoComplete" id="divAutoComplete"></div></td>
        <td class="inputDescription"><?php echo $osC_Language->get('param_web_work_directory_description'); ?></td>
      </tr>
    </table>

    <p align="right"><?php echo '<input type="image" src="templates/' . $template . '/languages/' . $osC_Language->getDirectory() . '/images/buttons/continue.gif" border="0" alt="' . $osC_Language->get('image_button_continue') . '" />'; ?>&nbsp;&nbsp;<?php echo '<a href="index.php"><img src="templates/' . $template . '/languages/' . $osC_Language->getDirectory() . '/images/buttons/cancel.gif" border="0" alt="' . $osC_Language->get('image_button_cancel') . '" /></a>'; ?></p>

<?php
  foreach ($_POST as $key => $value) {
    if (($key != 'x') && ($key != 'y')) {
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

<script language="javascript" type="text/javascript">
<!--

  new autoComplete(document.getElementById('HTTP_WORK_DIRECTORY'), 'divAutoComplete');

//-->
</script>
