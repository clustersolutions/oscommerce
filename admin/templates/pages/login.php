<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div id="infoBox_cNew">
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/new.png', IMAGE_INSERT, '16', '16') . ' ' . TEXT_INFO_HEADING_LOGIN; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('login', FILENAME_LOGIN, 'action=process'); ?>

    <p><?php echo TEXT_INFO_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_USER_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('user_name', '', 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_USER_PASSWORD . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_password_field('user_password', '', 'style="width: 100%;"'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_LOGIN . '" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<script language="javascript" type="text/javascript">
<!--

document.login.user_name.focus();

//-->
</script>
