<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo osc_image(DIR_WS_IMAGES . $osC_Template->getPageImage(), $osC_Template->getPageTitle(), null, null, 'id="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  require('includes/modules/product_listing.php');
?>

<div class="submitFormButtons">
  <?php echo osc_link_object(osc_href_link(FILENAME_SEARCH), osc_draw_image_button('button_back.gif', $osC_Language->get('button_back'))); ?>
</div>
