<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $specials_array = array();

  if (($_GET['action'] == 'sEdit') && isset($_GET['sID'])) {
    $Qspecial = $osC_Database->query('select p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, s.specials_new_products_price, s.expires_date, s.start_date, s.status from :table_specials s, :table_products p, :table_products_description pd where s.specials_id = :specials_id and s.products_id = p.products_id and p.products_id = pd.products_id and pd.language_id = :language_id');
    $Qspecial->bindTable(':table_specials', TABLE_SPECIALS);
    $Qspecial->bindTable(':table_products', TABLE_PRODUCTS);
    $Qspecial->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qspecial->bindInt(':specials_id', $_GET['sID']);
    $Qspecial->bindInt(':language_id', $osC_Language->getID());
    $Qspecial->execute();

    $specials_array[] = array('id' => $Qspecial->valueInt('products_id'),
                              'text' => $Qspecial->value('products_name') . ' (' . $osC_Currencies->format($Qspecial->value('products_price')) . ')',
                              'tax_class_id' => $Qspecial->valueInt('products_tax_class_id'));

    $sInfo = new objectInfo($Qspecial->toArray());
  } else {
    $Qspecials = $osC_Database->query('select p.products_id, p.products_price, p.products_tax_class_id, pd.products_name, s.specials_new_products_price from :table_products p left join :table_specials s on (p.products_id = s.products_id), :table_products_description pd where p.products_id = pd.products_id and pd.language_id = :language_id order by pd.products_name');
    $Qspecials->bindTable(':table_products', TABLE_PRODUCTS);
    $Qspecials->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qspecials->bindTable(':table_specials', TABLE_SPECIALS);
    $Qspecials->bindInt(':language_id', $osC_Language->getID());
    $Qspecials->execute();

    while ($Qspecials->next()) {
      if ($Qspecials->valueDecimal('specials_new_products_price') < 1) {
        $specials_array[] = array('id' => $Qspecials->valueInt('products_id'),
                                  'text' => $Qspecials->value('products_name') . ' (' . $osC_Currencies->format($Qspecials->value('products_price')) . ')',
                                  'tax_class_id' => $Qspecials->valueInt('products_tax_class_id'));
      }
    }
  }

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
  foreach ($specials_array as $specials) {
    echo '  product_tax["' . $specials['id'] . '"] = ' . $specials['tax_class_id'] . ';' . "\n";
  }

  foreach ($tax_class_array as $tc_entry) {
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

<?php
  if (isset($sInfo)) {
?>

  function getTaxRate() {
    var products_id = document.forms["special"].products_id.value;
    var tax_class = product_tax[products_id];

    if ( (tax_class > 0) && (tax_rates[tax_class] > 0) ) {
      return tax_rates[tax_class];
    } else {
      return 0;
    }
  }

<?php
  } else {
?>

  function getTaxRate() {
    var selected_value = document.forms["special"].products_id.selectedIndex;
    var products_id = document.forms["special"].products_id[selected_value].value;
    var tax_class = product_tax[products_id];

    if ( (tax_class > 0) && (tax_rates[tax_class] > 0) ) {
      return tax_rates[tax_class];
    } else {
      return 0;
    }
  }

<?php
  }
?>

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

<form name="special" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . (isset($_GET['sID']) ? '&sID=' . $_GET['sID'] : '') . '&action=save'); ?>" method="post">

<table border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td class="main"><?php echo TEXT_SPECIALS_PRODUCT; ?></td>
    <td class="main"><?php echo (isset($sInfo) ? $sInfo->products_name . ' <small>(' . $osC_Currencies->format($sInfo->products_price) . ')</small>' . osc_draw_hidden_field('products_id', $sInfo->products_id) : osc_draw_pull_down_menu('products_id', $specials_array)); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo TEXT_SPECIALS_SPECIAL_PRICE_NET; ?></td>
    <td class="main"><?php echo osc_draw_input_field('specials_price', (isset($sInfo) ? $sInfo->specials_new_products_price : null), 'onkeyup="updateGross(\'specials_price\', event)"'); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo TEXT_SPECIALS_SPECIAL_PRICE_GROSS; ?></td>
    <td class="main"><?php echo osc_draw_input_field('specials_price_gross', (isset($sInfo) ? $sInfo->specials_new_products_price : null), 'onkeyup="updateNet(\'specials_price\', event)"'); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo TEXT_SPECIALS_STATUS; ?></td>
    <td class="main"><?php echo osc_draw_checkbox_field('specials_status', '1', (isset($sInfo) ? $sInfo->status : null)); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo TEXT_SPECIALS_START_DATE; ?></td>
    <td class="main"><?php echo osc_draw_input_field('specials_start_date', (isset($sInfo) ? $sInfo->start_date : null)); ?><input type="button" value="..." id="calendarTriggerStartDate" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "specials_start_date", ifFormat: "%Y-%m-%d", button: "calendarTriggerStartDate" } );</script></td>
  </tr>
  <tr>
    <td class="main"><?php echo TEXT_SPECIALS_EXPIRES_DATE; ?></td>
    <td class="main"><?php echo osc_draw_input_field('specials_expires_date', (isset($sInfo) ? $sInfo->expires_date : null)); ?><input type="button" value="..." id="calendarTriggerEndDate" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "specials_expires_date", ifFormat: "%Y-%m-%d", button: "calendarTriggerEndDate" } );</script></td>
  </tr>
</table>

<script type="text/javascript"><!--
  updateGross('specials_price', false);
//--></script>

<p align="right"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . (isset($_GET['sID']) ? '&sID=' . $_GET['sID'] : '')) . '\';">'; ?></p>

<p class="main"><?php echo TEXT_SPECIALS_PRICE_TIP; ?></p>

</form>
