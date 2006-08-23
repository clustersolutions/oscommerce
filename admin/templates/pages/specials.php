<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo HEADING_TITLE; ?></p>

<div id="infoBox_sDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_PRODUCTS; ?></th>
        <th><?php echo TABLE_HEADING_PRODUCTS_PRICE; ?></th>
        <th><?php echo TABLE_HEADING_STATUS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qspecials = $osC_Database->query('select p.products_id, pd.products_name, p.products_price, s.specials_id, s.specials_new_products_price, s.specials_date_added, s.specials_last_modified, s.expires_date, s.date_status_change, s.status from :table_products p, :table_specials s, :table_products_description pd where p.products_id = pd.products_id and pd.language_id = :language_id and p.products_id = s.products_id order by pd.products_name');
  $Qspecials->bindTable(':table_specials', TABLE_SPECIALS);
  $Qspecials->bindTable(':table_products', TABLE_PRODUCTS);
  $Qspecials->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qspecials->bindInt(':language_id', $osC_Language->getID());
  $Qspecials->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qspecials->execute();

  while ($Qspecials->next()) {
    if (!isset($sInfo) && (!isset($_GET['sID']) || (isset($_GET['sID']) && ($_GET['sID'] == $Qspecials->valueInt('specials_id'))))) {
      $sInfo = new objectInfo($Qspecials->toArray());
    }

    if (isset($sInfo) && ($Qspecials->valueInt('specials_id') == $sInfo->specials_id)) {
      echo '          <tr class="selected">' . "\n";
    } else {
      echo '          <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $Qspecials->valueInt('specials_id')) . '\';">' . "\n";
    }
?>
        <td><?php echo $Qspecials->value('products_name'); ?></td>
        <td><span class="oldPrice"><?php echo $osC_Currencies->format($Qspecials->value('products_price')); ?></span> <span class="specialPrice"><?php echo $osC_Currencies->format($Qspecials->value('specials_new_products_price')); ?></span></td>
        <td align="center"><?php echo osc_icon((($Qspecials->valueInt('status') === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif'), null, null); ?></td>
        <td align="right">
<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $Qspecials->valueInt('specials_id') . '&action=sEdit'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;';

    if (isset($sInfo) && ($Qspecials->valueInt('specials_id') == $sInfo->specials_id)) {
      echo '<a href="#" onclick="toggleInfoBox(\'sDelete\');">' . osc_icon('trash.png', IMAGE_DELETE) . '</a>';
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $Qspecials->valueInt('specials_id') . '&action=sDelete'), osc_icon('trash.png', IMAGE_DELETE));
    }
?>
        </td>
      </tr>
<?php
  }
?>
    </tbody>
  </table>

  <table border="0" width="100%" cellpadding="0"cellspacing="2">
    <tr>
      <td class="smallText"><?php echo $Qspecials->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_SPECIALS); ?></td>
      <td class="smallText" align="right"><?php echo $Qspecials->displayBatchLinksPullDown(); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" class="infoBoxButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&action=sNew') . '\';">'; ?></p>
</div>

<?php
  if (isset($sInfo)) {
?>

<div id="infoBox_sDelete" <?php if ($action != 'sDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $sInfo->products_name; ?></div>
  <div class="infoBoxContent">
    <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $sInfo->products_name . '</b>'; ?></p>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_id . '&action=deleteconfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'sDefault\');" class="operationButton">'; ?></p>
  </div>
</div>

<?php
  }
?>
