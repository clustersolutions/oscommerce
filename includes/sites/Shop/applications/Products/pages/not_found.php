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

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<p><?php echo OSCOM::getDef('product_not_found'); ?></p>

<div class="submitFormButtons" style="text-align: right;">
  <?php echo osc_link_object(OSCOM::getLink(null, OSCOM::getDefaultSiteApplication()), osc_draw_image_button('button_continue.gif', OSCOM::getDef('button_continue'))); ?>
</div>
