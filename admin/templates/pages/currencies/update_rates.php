<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $services = array(array('id' => 'oanda',
                          'text' => 'Oanda (http://www.oanda.com)'),
                    array('id' => 'xe',
                          'text' => 'XE (http://www.xe.com)'));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('reload.png', IMAGE_UPDATE) . ' ' . $osC_Language->get('action_heading_update_rates'); ?></div>
<div class="infoBoxContent">
  <form name="cUpdate" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=updateRates'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_update_exchange_rates'); ?></p>

  <p><?php echo osc_draw_radio_field('service', $services, null, null, '<br />'); ?></p>

  <p><?php echo $osC_Language->get('service_terms_agreement'); ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_UPDATE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
