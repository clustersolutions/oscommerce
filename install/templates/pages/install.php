<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<div class="mainBlock">
  <div class="stepsBox">
    <ol>
      <li style="font-weight: bold;"><?php echo $osC_Language->get('box_steps_step_1'); ?></li>
      <li><?php echo $osC_Language->get('box_steps_step_2'); ?></li>
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
    <h3><?php echo $osC_Language->get('box_info_step_1_title'); ?></h3>

    <div class="infoPaneContents">
      <?php echo $osC_Language->get('box_info_step_1_text'); ?>
    </div>
  </div>

  <div class="contentPane">
    <h2><?php echo $osC_Language->get('page_heading_step_1'); ?></h2>

    <form name="install" action="install.php?step=2" method="post">

    <table border="0" width="99%" cellspacing="0" cellpadding="5" class="inputForm">
      <tr>
        <td class="inputField"><?php echo osc_draw_checkbox_field('install[]', 'database', true) . '&nbsp;' . $osC_Language->get('param_import_database'); ?></td>
        <td class="inputDescription"><?php echo $osC_Language->get('param_import_database_description'); ?></td>
      </tr>
      <tr>
        <td class="inputField"><?php echo osc_draw_checkbox_field('install[]', 'configure', true) . '&nbsp;' . $osC_Language->get('param_automatic_configuration'); ?></td>
        <td class="inputDescription"><?php echo $osC_Language->get('param_automatic_configuration_description'); ?></td>
      </tr>
    </table>

    <p align="right"><input type="image" src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/continue.gif" border="0" alt="<?php echo $osC_Language->get('image_button_continue'); ?>">&nbsp;&nbsp;<a href="index.php"><img src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/cancel.gif" border="0" alt="<?php echo $osC_Language->get('image_button_cancel'); ?>"></a></p>

    </form>
  </div>
</div>
