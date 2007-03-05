<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $Qspecial = $osC_Database->query('select p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, s.specials_new_products_price, s.expires_date, s.start_date, s.status from :table_specials s, :table_products p, :table_products_description pd where s.specials_id = :specials_id and s.products_id = p.products_id and p.products_id = pd.products_id and pd.language_id = :language_id');
  $Qspecial->bindTable(':table_specials', TABLE_SPECIALS);
  $Qspecial->bindTable(':table_products', TABLE_PRODUCTS);
  $Qspecial->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qspecial->bindInt(':specials_id', $_GET['sID']);
  $Qspecial->bindInt(':language_id', $osC_Language->getID());
  $Qspecial->execute();

  $Qtc = $osC_Database->query('select tax_class_id, tax_class_title from :table_tax_class order by tax_class_title');
  $Qtc->bindTable(':table_tax_class', TABLE_TAX_CLASS);
  $Qtc->execute();

  $tax_class_array = array();

  while ($Qtc->next()) {
    $tax_class_array[] = array('id' => $Qtc->valueInt('tax_class_id'),
                               'text' => $Qtc->value('tax_class_title'));
  }
?>

<style type="text/css">@import url('external/jscalendar/calendar-win2k-1.css');</style>
<script type="text/javascript" src="external/jscalendar/calendar.js"></script>
<script type="text/javascript" src="external/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="external/jscalendar/calendar-setup.js"></script>

<script type="text/javascript"><!--
  var product_tax = new Array();
  var tax_rates = new Array();

<?php
  echo '  product_tax["' . $Qspecial->valueInt('products_id') . '"] = ' . $Qspecial->valueInt('products_tax_class_id') . ';' . "\n";

  foreach ( $tax_class_array as $tc_entry ) {
    echo '  tax_rates["' . $tc_entry['id'] . '"] = ' . $osC_Tax->getTaxRate($tc_entry['id']) . ';' . "\n";
  }
?>

  function pad(s) {
    s = s||'.';

    return (s.length>4) ? s : pad(s+'0');
  }

  function doRound(x, places) {
    return (new String(Math.round(x * Math.pow(10, places)) / Math.pow(10, places))).replace(/(\.\d*)?$/, pad);
  }

  function getTaxRate() {
    var products_id = document.forms["special"].products_id.value;
    var tax_class = product_tax[products_id];

    if ( (tax_class > 0) && (tax_rates[tax_class] > 0) ) {
      return tax_rates[tax_class];
    } else {
      return 0;
    }
  }

  function updateGross(field, evt) {
    if (evt.keyCode == 9) {
      return false;
    }

    if ((document.getElementById(field).value).indexOf('%') > -1) {
      document.getElementById(field + "_gross").value = '';
      document.getElementById(field + "_gross").disabled = true;
      return false;
    } else if (document.getElementById(field + "_gross").disabled == true) {
      document.getElementById(field + "_gross").disabled = false;
    }

    var taxRate = getTaxRate();
    var grossValue = document.getElementById(field).value;

    if (taxRate > 0) {
      grossValue = grossValue * ((taxRate / 100) + 1);
    }

    document.getElementById(field + "_gross").value = doRound(grossValue, 4);
  }

  function updateNet(field, evt) {
    if (evt.keyCode == 9) {
      return false;
    }

    if ((document.getElementById(field + "_gross").value).indexOf('%') > -1) {
      document.getElementById(field).value = document.getElementById(field + "_gross").value;
      document.getElementById(field + "_gross").value = '';
      document.getElementById(field).focus();
      document.getElementById(field + "_gross").disabled = true;
      return false;
    }

    var taxRate = getTaxRate();
    var netValue = document.getElementById(field + "_gross").value;

    if (taxRate > 0) {
      netValue = netValue / ((taxRate / 100) + 1);
    }

    document.getElementById(field).value = doRound(netValue, 4);
  }
//--></script>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $Qspecial->value('products_name'); ?></div>
<div class="infoBoxContent">
  <form name="special" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&sID=' . $_GET['sID'] . '&action=save'); ?>" method="post">

  <p><?php echo TEXT_EDIT_INTRO; ?></p>

  <p><?php echo '<b>' . $Qspecial->value('products_name') . ' (' . $osC_Currencies->format($Qspecial->valueDecimal('products_price')) . ')</b>' . osc_draw_hidden_field('products_id', $Qspecial->valueInt('products_id')); ?></p>
  <p><?php echo TEXT_SPECIALS_SPECIAL_PRICE_NET . '<br />' . osc_draw_input_field('specials_price', $Qspecial->valueDecimal('specials_new_products_price'), 'onkeyup="updateGross(\'specials_price\', event)"'); ?></p>
  <p><?php echo TEXT_SPECIALS_SPECIAL_PRICE_GROSS . '<br />' . osc_draw_input_field('specials_price_gross', $Qspecial->valueDecimal('specials_new_products_price'), 'onkeyup="updateNet(\'specials_price\', event)"'); ?></p>
  <p><?php echo TEXT_SPECIALS_STATUS . '<br />' . osc_draw_checkbox_field('specials_status', '1', $Qspecial->value('status')); ?></p>
  <p><?php echo TEXT_SPECIALS_START_DATE . '<br />' . osc_draw_input_field('specials_start_date', $Qspecial->value('start_date')) . '<input type="button" value="..." id="calendarTriggerStartDate" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "specials_start_date", ifFormat: "%Y-%m-%d", button: "calendarTriggerStartDate" } );</script>'; ?></p>
  <p><?php echo TEXT_SPECIALS_EXPIRES_DATE . '<br />' . osc_draw_input_field('specials_expires_date', $Qspecial->value('expires_date')) . '<input type="button" value="..." id="calendarTriggerEndDate" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "specials_expires_date", ifFormat: "%Y-%m-%d", button: "calendarTriggerEndDate" } );</script>'; ?></p>

<script type="text/javascript"><!--
  updateGross('specials_price', false);
//--></script>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

<p><?php echo TEXT_SPECIALS_PRICE_TIP; ?></p>
