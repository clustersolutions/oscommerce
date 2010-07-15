<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\OSCOM;
?>

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('information_title'); ?></h6>

  <div class="content">
    <?php echo osc_image(DIR_WS_IMAGES . 'account_personal.gif', OSCOM::getDef('information_title'), null, null, 'style="float: left;"'); ?>

    <ul style="padding-left: 100px; list-style-image: url(<?php echo osc_href_link(DIR_WS_IMAGES . 'arrow_green.gif'); ?>);">
      <li><?php echo osc_link_object(OSCOM::getLink(null, null, 'Shipping'), OSCOM::getDef('box_information_shipping')); ?></li>
      <li><?php echo osc_link_object(OSCOM::getLink(null, null, 'Privacy'), OSCOM::getDef('box_information_privacy')); ?></li>
      <li><?php echo osc_link_object(OSCOM::getLink(null, null, 'Conditions'), OSCOM::getDef('box_information_conditions')); ?></li>
      <li><?php echo osc_link_object(OSCOM::getLink(null, null, 'Contact'), OSCOM::getDef('box_information_contact')); ?></li>
      <li><?php echo osc_link_object(OSCOM::getLink(null, null, 'Sitemap'), OSCOM::getDef('box_information_sitemap')); ?></li>
    </ul>

    <div style="clear: both;"></div>
  </div>
</div>
