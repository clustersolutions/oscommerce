<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo osc_image(DIR_WS_IMAGES . $osC_Template->getPageImage(), $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<div class="moduleBox" style="width: 40%; float: right; margin: 0 0 10px 10px;">
  <h6><?php echo $osC_Language->get('ssl_check_box_heading'); ?></h6>

  <div class="content">
    <?php echo $osC_Language->get('ssl_check_box_contents'); ?>
  </div>
</div>

<p><?php echo $osC_Language->get('ssl_check'); ?></p>

<div class="submitFormButtons" style="text-align: right;">
  <?php echo osc_link_object(osc_href_link(FILENAME_INFO), osc_draw_image_button('button_continue.gif', $osC_Language->get('button_continue'))); ?>
</div>
