<?php
/*
  $Id: install_5.php,v 1.25 2004/05/24 11:06:57 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $https_www_address = str_replace('http://', 'https://', $_POST['HTTP_WWW_ADDRESS']);
?>

<p class="pageTitle"><?php echo PAGE_TITLE_INSTALLATION; ?></p>

<p class="pageSubTitle"><?php echo PAGE_SUBTITLE_OSCOMMERCE_CONFIGURATION; ?></p>

<form name="install" action="install.php?step=6" method="post">

<p><?php echo TEXT_ENTER_SECURE_WEBSERVER_INFORMATION; ?></p>

<table width="95%" border="0" cellpadding="2" class="formPage">
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_WWW_HTTPS_ADDRESS; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_input_field('HTTPS_WWW_ADDRESS', $https_www_address); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('httpsWWW');"><br>
      <div id="httpsWWWSD"><?php echo CONFIG_WWW_HTTPS_ADDRESS_DESCRIPTION; ?></div>
      <div id="httpsWWW" class="longDescription"><?php echo CONFIG_WWW_HTTPS_ADDRESS_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_WWW_HTTPS_COOKIE_DOMAIN; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_input_field('HTTPS_COOKIE_DOMAIN', $_POST['HTTP_COOKIE_DOMAIN']); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('httpsCookieD');"><br>
      <div id="httpsCookieDSD"><?php echo CONFIG_WWW_HTTPS_COOKIE_DOMAIN_DESCRIPTION; ?></div>
      <div id="httpsCookieD" class="longDescription"><?php echo CONFIG_WWW_HTTPS_COOKIE_DOMAIN_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
  <tr>
    <td width="30%" valign="top"><?php echo CONFIG_WWW_HTTPS_COOKIE_PATH; ?></td>
    <td width="70%" class="smallDesc">
      <?php echo osc_draw_input_field('HTTPS_COOKIE_PATH', $_POST['HTTP_COOKIE_PATH']); ?>
      <img src="templates/<?php echo $template; ?>/images/help_icon.gif" onClick="toggleBox('httpsCookieP');"><br>
      <div id="httpsCookiePSD"><?php echo CONFIG_WWW_HTTPS_COOKIE_PATH_DESCRIPTION; ?></div>
      <div id="httpsCookieP" class="longDescription"><?php echo CONFIG_WWW_HTTPS_COOKIE_PATH_DESCRIPTION_LONG; ?></div>
    </td>
  </tr>
</table>

<p>&nbsp;</p>

<table width="95%" border="0" cellspacing="2">
  <tr>
    <td align="right"><input type="image" src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/continue.gif" border="0" alt="<?php echo IMAGE_BUTTON_CONTINUE; ?>">&nbsp;&nbsp;<a href="index.php"><img src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/cancel.gif" border="0" alt="<?php echo IMAGE_BUTTON_CANCEL; ?>"></a></td>
  </tr>
</table>

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
