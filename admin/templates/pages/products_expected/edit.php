<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Products_Admin::getData($_GET['pID']));
?>

<style type="text/css">@import url('external/jscalendar/calendar-win2k-1.css');</style>
<script type="text/javascript" src="external/jscalendar/calendar.js"></script>
<script type="text/javascript" src="external/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="external/jscalendar/calendar-setup.js"></script>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->get('products_name'); ?></div>
<div class="infoBoxContent">
  <form name="pEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&pID=' . $osC_ObjectInfo->get('products_id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_product_expected'); ?></p>

  <p><?php echo $osC_Language->get('field_date_expected') . '<br />' . osc_draw_input_field('products_date_available', $osC_ObjectInfo->get('products_date_available')); ?><input type="button" value="..." id="calendarTrigger" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "products_date_available", ifFormat: "%Y-%m-%d", button: "calendarTrigger" } );</script></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
