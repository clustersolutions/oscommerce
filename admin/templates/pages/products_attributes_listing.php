<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div id="infoBox_paeDefault" <?php if (!empty($entriesAction)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo 'Entry'; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qentries = $osC_Database->query('select pov.products_options_values_id, pov.products_options_values_name from :table_products_options_values pov, :table_products_options_values_to_products_options pov2po where pov2po.products_options_id = :products_options_id and pov2po.products_options_values_id = pov.products_options_values_id and pov.language_id = :language_id order by pov.products_options_values_name');
  $Qentries->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
  $Qentries->bindTable(':table_products_options_values_to_products_options', TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);
  $Qentries->bindInt(':products_options_id', $_GET['paID']);
  $Qentries->bindInt(':language_id', $osC_Language->getID());
  $Qentries->setBatchLimit($_GET['entriesPage'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qentries->execute();

  while ($Qentries->next()) {
    if (!isset($paeInfo) && (!isset($_GET['paeID']) || (isset($_GET['paeID']) && ($_GET['paeID'] == $Qentries->valueInt('products_options_values_id'))))) {
      $Qproducts = $osC_Database->query('select count(*) as total_products from :table_products_attributes where options_values_id = :options_values_id');
      $Qproducts->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
      $Qproducts->bindInt(':options_values_id', $Qentries->valueInt('products_options_values_id'));
      $Qproducts->execute();

      $paeInfo = new objectInfo(array_merge($Qentries->toArray(), $Qproducts->toArray()));
    }

    if (isset($paeInfo) && ($Qentries->valueInt('products_options_values_id') == $paeInfo->products_options_values_id)) {
      echo '      <tr class="selected">' . "\n";
    } else {
      echo '      <tr onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);" onClick="document.location.href=\'' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page'] . '&paID=' . $_GET['paID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&paeID=' . $Qentries->valueInt('products_options_values_id')) . '\';">' . "\n";
    }
?>
        <td><?php echo $Qentries->value('products_options_values_name'); ?></td>
        <td align="right">
<?php
    if (isset($paeInfo) && ($Qentries->valueInt('products_options_values_id') == $paeInfo->products_options_values_id)) {
      echo '<a href="#" onClick="toggleInfoBox(\'paeEdit\');">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="#" onClick="toggleInfoBox(\'paeDelete\');">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page'] . '&paID=' . $_GET['paID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&paeID=' . $Qentries->valueInt('products_options_values_id') . '&entriesAction=paeEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page'] . '&paID=' . $_GET['paID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&paeID=' . $Qentries->valueInt('products_options_values_id') . '&entriesAction=paeDelete') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
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
      <td class="smallText"><?php echo $Qentries->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_COUNTRIES); ?></td>
      <td class="smallText" align="right"><?php echo $Qentries->displayBatchLinksPullDown('entriesPage', 'page=' . $_GET['page'] . '&paID=' . $_GET['paID'] . '&action=list'); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onClick="document.location.href=\'' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page'] . '&paID=' . $_GET['paID']) . '\';" class="infoBoxButton"> <input type="button" value="' . IMAGE_INSERT . '" onClick="toggleInfoBox(\'paeNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_paeNew" <?php if ($entriesAction != 'paeNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/new.png', IMAGE_INSERT, '16', '16') . ' ' . TEXT_INFO_HEADING_NEW_ATTRIBUTE_ENTRY; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('paeNew', FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page'] . '&paID=' . $_GET['paID'] . '&action=list&entriesAction=saveGroupEntry'); ?>

    <p><?php echo TEXT_INFO_INSERT_ATTRIBUTE_ENTRY_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%" valign="top"><?php echo '<b>' . TEXT_INFO_ATTRIBUTE_ENTRY_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%">
<?php
  foreach ($osC_Language->getAll() as $l) {
    echo tep_image('../includes/languages/' . $l['directory'] . '/images/icon.gif', $l['name']) . '&nbsp;' .  osc_draw_input_field('entry_name[' . $l['id'] . ']') . '<br>';
  }
?>
        </td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'paeDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($paeInfo)) {
?>

<div id="infoBox_paeEdit" <?php if ($entriesAction != 'paeEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $paeInfo->products_options_values_name; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('paeEdit', FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page'] . '&paID=' . $_GET['paID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&paeID=' . $paeInfo->products_options_values_id . '&entriesAction=saveGroupEntry'); ?>

    <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%" valign="top"><?php echo '<b>' . TEXT_INFO_ATTRIBUTE_ENTRY_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%">
<?php
    $Qed = $osC_Database->query('select language_id, products_options_values_name from :table_products_options_values where products_options_values_id = :products_options_values_id');
    $Qed->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
    $Qed->bindInt(':products_options_values_id', $paeInfo->products_options_values_id);
    $Qed->execute();

    $entry_names = array();
    while ($Qed->next()) {
      $entry_names[$Qed->valueInt('language_id')] = $Qed->value('products_options_values_name');
    }

    foreach ($osC_Language->getAll() as $l) {
      echo tep_image('../includes/languages/' . $l['directory'] . '/images/icon.gif', $l['name']) . '&nbsp;' .  osc_draw_input_field('entry_name[' . $l['id'] . ']', (isset($entry_names[$l['id']]) ? $entry_names[$l['id']] : '')) . '<br>';
    }
?>
        </td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'paeDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_paeDelete" <?php if ($entriesAction != 'paeDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' ' . $paeInfo->products_options_values_name; ?></div>
  <div class="infoBoxContent">

<?php
    if ($paeInfo->total_products > 0) {
      echo '    <p><b>' . sprintf(TEXT_INFO_DELETE_ATTRIBUTE_ENTRY_PROHIBITED, $paeInfo->total_products) . '</b></p>' . "\n" .
           '    <p align="center"><input type="button" value="' . IMAGE_BACK . '" onClick="toggleInfoBox(\'paeDefault\');" class="operationButton"></p>' . "\n";
    } else {
      echo '    <p>' . TEXT_INFO_DELETE_ATTRIBUTE_ENTRY_INTRO . '</p>' . "\n" .
           '    <p><b>' . $paeInfo->products_options_values_name . '</b></p>' . "\n" .
           '    <p align="center"><input type="button" value="' . IMAGE_DELETE . '" onClick="document.location.href=\'' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page'] . '&paID=' . $_GET['paID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&paeID=' . $paeInfo->products_options_values_id . '&entriesAction=deleteConfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'paeDefault\');" class="operationButton"></p>' . "\n";
    }
?>

  </div>
</div>

<?php
  }
?>
