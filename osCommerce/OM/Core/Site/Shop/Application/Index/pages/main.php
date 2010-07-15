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

<?php
  if ( $OSCOM_Customer->isLoggedOn() ) {
    echo '<p>' . sprintf(OSCOM::getDef('greeting_customer'), osc_output_string_protected($OSCOM_Customer->getFirstName()), OSCOM::getLink(null, 'Products', 'New')) . '</p>';
  } else {
    echo '<p>' . sprintf(OSCOM::getDef('greeting_guest'), OSCOM::getLink(null, 'Account', 'Login', 'SSL'), OSCOM::getLink(null, 'Products', 'New')) . '</p>';
  }
?>

<p><?php echo OSCOM::getDef('index_text'); ?></p>
