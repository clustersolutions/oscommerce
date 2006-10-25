<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div id="infoBox_paeDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>
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
  $Qentries->bindInt(':products_options_id', $_GET[$osC_Template->getModule()]);
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
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td><?php echo $Qentries->value('products_options_values_name'); ?></td>
        <td align="right">

<?php
    if (isset($paeInfo) && ($Qentries->valueInt('products_options_values_id') == $paeInfo->products_options_values_id)) {
      echo osc_link_object('#', osc_icon('configure.png', IMAGE_EDIT), 'onclick="toggleInfoBox(\'paeEdit\');"') . '&nbsp;' .
           osc_link_object('#', osc_icon('trash.png', IMAGE_DELETE), 'onclick="toggleInfoBox(\'paeDelete\');"');
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&entriesPage=' . $_GET['entriesPage'] . '&paeID=' . $Qentries->valueInt('products_options_values_id') . '&action=paeEdit'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&entriesPage=' . $_GET['entriesPage'] . '&paeID=' . $Qentries->valueInt('products_options_values_id') . '&action=paeDelete'), osc_icon('trash.png', IMAGE_DELETE));
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
      <td class="smallText"><?php echo $Qentries->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_PRODUCT_ATTRIBUTES); ?></td>
      <td class="smallText" align="right"><?php echo $Qentries->displayBatchLinksPullDown('entriesPage', $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page']); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&paID=' . $_GET[$osC_Template->getModule()]) . '\';" class="infoBoxButton"> <input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'paeNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_paeNew" <?php if ($_GET['action'] != 'paeNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_ATTRIBUTE_ENTRY; ?></div>
  <div class="infoBoxContent">
    <form name="paeNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&action=saveEntry'); ?>" method="post">

    <p><?php echo TEXT_INFO_INSERT_ATTRIBUTE_ENTRY_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%" valign="top"><?php echo '<b>' . TEXT_INFO_ATTRIBUTE_ENTRY_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%">
<?php
  foreach ($osC_Language->getAll() as $l) {
    echo osc_image('../includes/languages/' . $l['code'] . '/images/' . $l['image'], $l['name']) . '&nbsp;' .  osc_draw_input_field('entry_name[' . $l['id'] . ']') . '<br />';
  }
?>
        </td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'paeDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($paeInfo)) {
?>

<div id="infoBox_paeEdit" <?php if ($_GET['action'] != 'paeEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $paeInfo->products_options_values_name; ?></div>
  <div class="infoBoxContent">
    <form name="paeEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&entriesPage=' . $_GET['entriesPage'] . '&paeID=' . $paeInfo->products_options_values_id . '&action=saveEntry'); ?>" method="post">

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
      echo osc_image('../includes/languages/' . $l['code'] . '/images/' . $l['image'], $l['name']) . '&nbsp;' .  osc_draw_input_field('entry_name[' . $l['id'] . ']', (isset($entry_names[$l['id']]) ? $entry_names[$l['id']] : null)) . '<br />';
    }
?>

        </td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'paeDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_paeDelete" <?php if ($_GET['action'] != 'paeDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $paeInfo->products_options_values_name; ?></div>
  <div class="infoBoxContent">

<?php
    if ($paeInfo->total_products > 0) {
      echo '    <p><b>' . sprintf(TEXT_INFO_DELETE_ATTRIBUTE_ENTRY_PROHIBITED, $paeInfo->total_products) . '</b></p>' . "\n" .
           '    <p align="center"><input type="button" value="' . IMAGE_BACK . '" onclick="toggleInfoBox(\'paeDefault\');" class="operationButton"></p>' . "\n";
    } else {
      echo '    <p>' . TEXT_INFO_DELETE_ATTRIBUTE_ENTRY_INTRO . '</p>' . "\n" .
           '    <p><b>' . $paeInfo->products_options_values_name . '</b></p>' . "\n" .
           '    <p align="center"><input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&entriesPage=' . $_GET['entriesPage'] . '&paeID=' . $paeInfo->products_options_values_id . '&action=deleteEntryConfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'paeDefault\');" class="operationButton"></p>' . "\n";
    }
?>

  </div>
</div>

<?php
  }
?>
