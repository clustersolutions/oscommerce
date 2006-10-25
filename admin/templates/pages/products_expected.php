<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<style type="text/css">@import url('external/jscalendar/calendar-win2k-1.css');</style>
<script type="text/javascript" src="external/jscalendar/calendar.js"></script>
<script type="text/javascript" src="external/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="external/jscalendar/calendar-setup.js"></script>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div id="infoBox_pDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>
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
  $Qproducts->bindInt(':language_id', $osC_Language->getID());
  $Qproducts->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qproducts->execute();

  while ($Qproducts->next()) {
    if (!isset($pInfo) && (!isset($_GET['pID']) || (isset($_GET['pID']) && ($_GET['pID'] == $Qproducts->valueInt('products_id'))))) {
      $pInfo = new objectInfo($Qproducts->toArray());
    }
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td><?php echo $Qproducts->value('products_name'); ?></td>
        <td><?php echo osC_DateTime::getShort($Qproducts->value('products_date_available')); ?></td>
        <td align="right">

<?php
    if (isset($pInfo) && ($Qproducts->valueInt('products_id') == $pInfo->products_id)) {
      echo osc_link_object('#', osc_icon('configure.png', IMAGE_EDIT), 'onclick="toggleInfoBox(\'pEdit\');"');
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&pID=' . $Qproducts->valueInt('products_id') . '&action=pEdit'), osc_icon('configure.png', IMAGE_EDIT));
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
      <td class="smallText" align="right"><?php echo $Qproducts->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
    </tr>
  </table>
</div>

<?php
  if (isset($pInfo)) {
?>

<div id="infoBox_pEdit" <?php if ($_GET['action'] != 'pEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $pInfo->products_name; ?></div>
  <div class="infoBoxContent">
    <form name="pEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&pID=' . $pInfo->products_id . '&action=save'); ?>" method="post">

    <p><?php echo TEXT_EDIT_INTRO; ?></p>
    <p><?php echo TEXT_INFO_DATE_EXPECTED . '<br />' . osc_draw_input_field('products_date_available', $pInfo->products_date_available); ?><input type="button" value="..." id="calendarTrigger" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "products_date_available", ifFormat: "%Y-%m-%d", button: "calendarTrigger" } );</script></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'pDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>
