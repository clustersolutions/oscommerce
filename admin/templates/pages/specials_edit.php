<?php
/*
  $Id: specials_edit.php,v 1.4 2004/10/30 14:13:31 sparky Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  if (($action == 'sEdit') && isset($_GET['sID'])) {
    $Qspecial = $osC_Database->query('select p.products_id, pd.products_name, p.products_price, s.specials_new_products_price, s.expires_date, s.start_date, s.status from :table_specials s, :table_products p, :table_products_description pd where s.specials_id = :specials_id and s.products_id = p.products_id and p.products_id = pd.products_id and pd.language_id = :language_id');
    $Qspecial->bindTable(':table_specials', TABLE_SPECIALS);
    $Qspecial->bindTable(':table_products', TABLE_PRODUCTS);
    $Qspecial->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qspecial->bindInt(':specials_id', $_GET['sID']);
    $Qspecial->bindInt(':language_id', $osC_Session->value('languages_id'));
    $Qspecial->execute();

    $sInfo = new objectInfo($Qspecial->toArray());
  } else {
    $specials_array = array();

    $Qspecials = $osC_Database->query('select p.products_id, p.products_price, pd.products_name, s.specials_new_products_price from :table_products p left join :table_specials s on (p.products_id = s.products_id), :table_products_description pd where p.products_id = pd.products_id and pd.language_id = :language_id order by pd.products_name');
    $Qspecials->bindTable(':table_products', TABLE_PRODUCTS);
    $Qspecials->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qspecials->bindTable(':table_specials', TABLE_SPECIALS);
    $Qspecials->bindInt(':language_id', $osC_Session->value('languages_id'));
    $Qspecials->execute();

    while ($Qspecials->next()) {
      if ($Qspecials->valueDecimal('specials_new_products_price') < 1) {
        $specials_array[] = array('id' => $Qspecials->valueInt('products_id'), 'text' => $Qspecials->value('products_name') . ' (' . $osC_Currencies->format($Qspecials->value('products_price')) . ')');
      }
    }
  }
?>

<style type="text/css">@import url('external/jscalendar/calendar-win2k-1.css');</style>
<script type="text/javascript" src="external/jscalendar/calendar.js"></script>
<script type="text/javascript" src="external/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="external/jscalendar/calendar-setup.js"></script>

<h1><?php echo HEADING_TITLE; ?></p>

<?php echo tep_draw_form('special', FILENAME_SPECIALS, 'page=' . $_GET['page'] . (isset($_GET['sID']) ? '&sID=' . $_GET['sID'] : '') . '&action=save'); ?>

<table border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td class="main"><?php echo TEXT_SPECIALS_PRODUCT; ?></td>
    <td class="main"><?php echo (isset($sInfo) ? $sInfo->products_name . ' <small>(' . $osC_Currencies->format($sInfo->products_price) . ')</small>' . osc_draw_hidden_field('products_id', $sInfo->products_id) : osc_draw_pull_down_menu('products_id', $specials_array)); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo TEXT_SPECIALS_SPECIAL_PRICE; ?></td>
    <td class="main"><?php echo osc_draw_input_field('specials_price', (isset($sInfo) ? $sInfo->specials_new_products_price : '')); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo TEXT_SPECIALS_STATUS; ?></td>
    <td class="main"><?php echo osc_draw_checkbox_field('specials_status', '1', (isset($sInfo) ? $sInfo->status : '')); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo TEXT_SPECIALS_START_DATE; ?></td>
    <td class="main"><?php echo osc_draw_input_field('specials_start_date', (isset($sInfo) ? $sInfo->start_date : ''), 'id="calendarValueStartDate"'); ?><input type="button" value="..." id="calendarTriggerStartDate" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "calendarValueStartDate", ifFormat: "%Y-%m-%d", button: "calendarTriggerStartDate" } );</script></td>
  </tr>
  <tr>
    <td class="main"><?php echo TEXT_SPECIALS_EXPIRES_DATE; ?></td>
    <td class="main"><?php echo osc_draw_input_field('specials_expires_date', (isset($sInfo) ? $sInfo->expires_date : ''), 'id="calendarValueEndDate"'); ?><input type="button" value="..." id="calendarTriggerEndDate" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "calendarValueEndDate", ifFormat: "%Y-%m-%d", button: "calendarTriggerEndDate" } );</script></td>
  </tr>
</table>

<p align="right"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" class="operationButton" onClick="document.location.href=\'' . tep_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . (isset($_GET['sID']) ? '&sID=' . $_GET['sID'] : '')) . '\';">'; ?></p>

<p class="main"><?php echo TEXT_SPECIALS_PRICE_TIP; ?></p>

</form>
