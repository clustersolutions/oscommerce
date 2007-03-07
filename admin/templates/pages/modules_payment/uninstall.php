<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  include('includes/modules/payment/' . $_GET['module'] . '.php');

  $osC_Language->injectDefinitions('modules/payment/' . $_GET['module'] . '.xml');

  $module = 'osC_Payment_' . $_GET['module'];
  $module = new $module();
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('stop.png', IMAGE_MODULE_REMOVE) . ' ' . $module->getTitle(); ?></div>
<div class="infoBoxContent">
  <form name="mUninstall" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&module=' . $module->getCode() . '&action=uninstall'); ?>" method="post">

  <p><?php echo INFO_MODULE_UNINSTALL_INTRO; ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_MODULE_REMOVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
