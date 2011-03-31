<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists('Contact') ) {
    echo $OSCOM_MessageStack->get('Contact');
  }
?>

<p><?php echo OSCOM::getDef('contact_email_sent_successfully'); ?></p>

<div class="submitFormButtons" style="text-align: right;">
  <?php echo HTML::button(array('href' => OSCOM::getLink(), 'icon' => 'triangle-1-e', 'title' => OSCOM::getDef('button_continue'))); ?>
</div>
