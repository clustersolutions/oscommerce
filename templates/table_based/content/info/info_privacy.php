<?php
/*
  $Id: shipping.php 5 2005-01-31 01:40:15Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_specials.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<p><?php echo $osC_Language->get('privacy'); ?></p>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo '<a href="' . tep_href_link(FILENAME_INFO) . '">' . tep_image_button('button_continue.gif', $osC_Language->get('button_continue')) . '</a>'; ?></span>
</div>
