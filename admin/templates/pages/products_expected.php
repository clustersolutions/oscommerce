<?php
/*
  $Id: products_expected.php,v 1.3 2004/08/02 12:30:09 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
?>

<style type="text/css">@import url('external/jscalendar/calendar-win2k-1.css');</style>
<script type="text/javascript" src="external/jscalendar/calendar.js"></script>
<script type="text/javascript" src="external/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="external/jscalendar/calendar-setup.js"></script>

<h1><?php echo HEADING_TITLE; ?></h1>

<div id="infoBox_pDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_PRODUCTS; ?></th>
        <th><?php echo TABLE_HEADING_DATE_EXPECTED; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qproducts = $osC_Database->query('select p.products_id, p.products_date_available, pd.products_name from :table_products p, :table_products_description pd where p.products_date_available is not null and p.products_id = pd.products_id and pd.language_id = :language_id order by p.products_date_available');
  $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
  $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qproducts->bindInt(':language_id', $osC_Session->value('languages_id'));
  $Qproducts->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qproducts->execute();

  while ($Qproducts->next()) {
    if (!isset($pInfo) && (!isset($_GET['pID']) || (isset($_GET['pID']) && ($_GET['pID'] == $Qproducts->valueInt('products_id'))))) {
      $pInfo = new objectInfo($Qproducts->toArray());
    }

    if (isset($pInfo) && ($Qproducts->valueInt('products_id') == $pInfo->products_id)) {
      echo '          <tr class="selected">' . "\n";
    } else {
      echo '          <tr onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);" onClick="document.location.href=\'' . tep_href_link(FILENAME_PRODUCTS_EXPECTED, 'page=' . $_GET['page'] . '&pID=' . $Qproducts->valueInt('products_id')) . '\'">' . "\n";
    }
?>
        <td><?php echo $Qproducts->value('products_name'); ?></td>
        <td><?php echo tep_date_short($Qproducts->value('products_date_available')); ?></td>
        <td align="right">
<?php
    if (isset($pInfo) && ($Qproducts->valueInt('products_id') == $pInfo->products_id)) {
      echo '<a href="#" onClick="toggleInfoBox(\'pEdit\');">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_EXPECTED, 'page=' . $_GET['page'] . '&pID=' . $Qproducts->valueInt('products_id') . '&action=pEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>';
    }
?>
        </td>
      </tr>
<?php
  }
?>
    </tbody>
  </table>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td class="smallText"><?php echo $Qproducts->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED); ?></td>
      <td class="smallText" align="right"><?php echo $Qproducts->displayBatchLinksPullDown(); ?></td>
    </tr>
  </table>
</div>

<?php
  if (isset($pInfo)) {
?>

<div id="infoBox_pEdit" <?php if ($action != 'pEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $pInfo->products_name; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('pEdit', FILENAME_PRODUCTS_EXPECTED, 'page=' . $_GET['page'] . '&pID=' . $pInfo->products_id . '&action=save'); ?>

    <p><?php echo TEXT_EDIT_INTRO; ?></p>
    <p><?php echo TEXT_INFO_DATE_EXPECTED . '<br>' . osc_draw_input_field('products_date_available', $pInfo->products_date_available, 'id="calendarValue"'); ?><input type="button" value="..." id="calendarTrigger" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "calendarValue", ifFormat: "%Y-%m-%d", button: "calendarTrigger" } );</script></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'pDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>
