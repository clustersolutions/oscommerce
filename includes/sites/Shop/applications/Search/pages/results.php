<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\OSCOM;

  $Qlisting = $OSCOM_Search->execute();
?>

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  require('includes/modules/product_listing.php');
?>

<div class="submitFormButtons">
  <?php echo osc_link_object(OSCOM::getLink(), osc_draw_image_button('button_back.gif', OSCOM::getDef('button_back'))); ?>
</div>
