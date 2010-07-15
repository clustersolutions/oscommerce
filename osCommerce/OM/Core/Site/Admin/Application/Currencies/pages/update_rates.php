<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;

  $services = array(array('id' => 'oanda',
                          'text' => 'Oanda (http://www.oanda.com)'),
                    array('id' => 'xe',
                          'text' => 'XE (http://www.xe.com)'));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('update.png') . ' ' . OSCOM::getDef('action_heading_update_rates'); ?></h3>

  <form name="cUpdate" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'action=UpdateRates'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_update_exchange_rates'); ?></p>

  <fieldset>
    <p><?php echo osc_draw_radio_field('service', $services, null, null, '<br />'); ?></p>
  </fieldset>

  <p><?php echo OSCOM::getDef('service_terms_agreement'); ?></p>

  <p><?php echo osc_draw_hidden_field('subaction', 'confirm') . osc_draw_button(array('priority' => 'primary', 'icon' => 'refresh', 'title' => OSCOM::getDef('button_update'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
