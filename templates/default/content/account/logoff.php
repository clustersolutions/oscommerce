<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<div>
  <div style="float: left;"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_man_on_board.gif', $osC_Template->getPageTitle()); ?></div>

  <div style="padding-top: 30px;">
    <p><?php echo $osC_Language->get('sign_out_text'); ?></p>
  </div>
</div>

<div class="submitFormButtons" style="text-align: right;">
  <?php echo osc_link_object(tep_href_link(FILENAME_DEFAULT), tep_image_button('button_continue.gif', $osC_Language->get('button_continue'))); ?>
</div>
