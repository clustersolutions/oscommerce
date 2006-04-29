<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

/*
      if (DOWNLOAD_ENABLED == '1') {
        $download_query_raw ="select products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount
                              from " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                              where products_attributes_id='" . $attributes_values['products_attributes_id'] . "'";
        $download_query = tep_db_query($download_query_raw);
        if (tep_db_num_rows($download_query) > 0) {
          $download = tep_db_fetch_array($download_query);
          $products_attributes_filename = $download['products_attributes_filename'];
          $products_attributes_maxdays  = $download['products_attributes_maxdays'];
          $products_attributes_maxcount = $download['products_attributes_maxcount'];
        }
?>
          <tr class="<?php echo (!($rows % 2)? 'attributes-even' : 'attributes-odd');?>">
            <td>&nbsp;</td>
            <td colspan="5">
              <table>
                <tr class="<?php echo (!($rows % 2)? 'attributes-even' : 'attributes-odd');?>">
                  <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_DOWNLOAD; ?>&nbsp;</td>
                  <td class="smallText"><?php echo TABLE_TEXT_FILENAME; ?></td>
                  <td class="smallText"><?php echo tep_draw_input_field('products_attributes_filename', $products_attributes_filename, 'size="15"'); ?>&nbsp;</td>
                  <td class="smallText"><?php echo TABLE_TEXT_MAX_DAYS; ?></td>
                  <td class="smallText"><?php echo tep_draw_input_field('products_attributes_maxdays', $products_attributes_maxdays, 'size="5"'); ?>&nbsp;</td>
                  <td class="smallText"><?php echo TABLE_TEXT_MAX_COUNT; ?></td>
                  <td class="smallText"><?php echo tep_draw_input_field('products_attributes_maxcount', $products_attributes_maxcount, 'size="5"'); ?>&nbsp;</td>
                </tr>
              </table>
            </td>
            <td>&nbsp;</td>
          </tr>
<?php
      }
*/
?>

<h1><?php echo HEADING_TITLE;?></h1>

<div id="infoBox_paDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_ATTRIBUTE_GROUPS; ?></th>
        <th><?php echo TABLE_HEADING_TOTAL_ENTRIES; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qgroups = $osC_Database->query('select products_options_id, products_options_name from :table_products_options where language_id = :language_id order by products_options_name');
  $Qgroups->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
  $Qgroups->bindInt(':language_id', $osC_Language->getID());
  $Qgroups->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qgroups->execute();

  while ($Qgroups->next()) {
    $Qentries = $osC_Database->query('select count(*) as total_entries from :table_products_options_values_to_products_options where products_options_id = :products_options_id');
    $Qentries->bindTable(':table_products_options_values_to_products_options', TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);
    $Qentries->bindInt(':products_options_id', $Qgroups->valueInt('products_options_id'));
    $Qentries->execute();

    if (!isset($paInfo) && (!isset($_GET['paID']) || (isset($_GET['paID']) && ($_GET['paID'] == $Qgroups->valueInt('products_options_id'))))) {
      $Qproducts = $osC_Database->query('select count(*) as total_products from :table_products_attributes where options_id = :options_id');
      $Qproducts->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
      $Qproducts->bindInt(':options_id', $Qgroups->valueInt('products_options_id'));
      $Qproducts->execute();

      $paInfo = new objectInfo(array_merge($Qgroups->toArray(), $Qentries->toArray(), $Qproducts->toArray()));
    }

    if (isset($paInfo) && ($Qgroups->valueInt('products_options_id') == $paInfo->products_options_id)) {
      echo '      <tr class="selected">' . "\n";
    } else {
      echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page'] . '&paID=' . $Qgroups->valueInt('products_options_id')) . '\';">' . "\n";
    }
?>
        <td><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page'] . '&paID=' . $Qgroups->valueInt('products_options_id') . '&action=list') . '">' . tep_image('images/icons/folder.gif', ICON_FOLDER) . '&nbsp;' . $Qgroups->value('products_options_name') . '</a>'; ?></td>
        <td><?php echo $Qentries->valueInt('total_entries'); ?></td>
        <td align="right">
<?php
    if (isset($paInfo) && ($Qgroups->valueInt('products_options_id') == $paInfo->products_options_id)) {
      echo '<a href="#" onclick="toggleInfoBox(\'paEdit\');">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="#" onclick="toggleInfoBox(\'paDelete\');">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page'] . '&paID=' . $Qgroups->valueInt('products_options_id') . '&action=paEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page'] . '&paID=' . $Qgroups->valueInt('products_options_id') . '&action=paDelete') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
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
      <td class="smallText"><?php echo $Qgroups->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_PRODUCT_ATTRIBUTES_GROUPS); ?></td>
      <td class="smallText" align="right"><?php echo $Qgroups->displayBatchLinksPullDown(); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'paNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_paNew" <?php if ($action != 'paNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/new.png', IMAGE_INSERT, '16', '16') . ' ' . TEXT_INFO_HEADING_NEW_ATTRIBUTE_GROUP; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('paNew', FILENAME_PRODUCTS_ATTRIBUTES, 'action=saveGroup'); ?>

    <p><?php echo TEXT_INFO_INSERT_ATTRIBUTE_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%" valign="top"><?php echo '<b>' . TEXT_INFO_ATTRIBUTE_GROUP_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%">
<?php
  foreach ($osC_Language->getAll() as $l) {
    echo tep_image('../includes/languages/' . $l['code'] . '/images/icon.gif', $l['name']) . '&nbsp;' .  osc_draw_input_field('group_name[' . $l['id'] . ']') . '<br />';
  }
?>
        </td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'paDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($paInfo)) {
?>

<div id="infoBox_paEdit" <?php if ($action != 'paEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $paInfo->products_options_name; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('paEdit', FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page'] . '&paID=' . $paInfo->products_options_id . '&action=saveGroup'); ?>

    <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%" valign="top"><?php echo '<b>' . TEXT_INFO_ATTRIBUTE_GROUP_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%">
<?php
    $Qgd = $osC_Database->query('select language_id, products_options_name from :table_products_options where products_options_id = :products_options_id');
    $Qgd->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
    $Qgd->bindInt(':products_options_id', $paInfo->products_options_id);
    $Qgd->execute();

    $group_names = array();
    while ($Qgd->next()) {
      $group_names[$Qgd->valueInt('language_id')] = $Qgd->value('products_options_name');
    }

    foreach ($osC_Language->getAll() as $l) {
      echo tep_image('../includes/languages/' . $l['code'] . '/images/icon.gif', $l['name']) . '&nbsp;' .  osc_draw_input_field('group_name[' . $l['id'] . ']', (isset($group_names[$l['id']]) ? $group_names[$l['id']] : '')) . '<br />';
    }
?>
        </td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'paDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_paDelete" <?php if ($action != 'paDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' ' . $paInfo->products_options_name; ?></div>
  <div class="infoBoxContent">

<?php
    if ($paInfo->total_products > 0) {
      echo '    <p><b>' . sprintf(TEXT_INFO_DELETE_ATTRIBUTE_GROUP_PROHIBITED, $paInfo->total_products) . '</b></p>' . "\n" .
           '    <p align="center"><input type="button" value="' . IMAGE_BACK . '" onclick="toggleInfoBox(\'paDefault\');" class="operationButton"></p>' . "\n";
    } else {
      echo '    <p>' . TEXT_INFO_DELETE_ATTRIBUTE_INTRO . '</p>' . "\n" .
           '    <p><b>' . $paInfo->products_options_name . '</b></p>' . "\n";

      if ($paInfo->total_entries > 0) {
        echo '    <p><b>' . sprintf(TEXT_INFO_DELETE_ATTRIBUTE_GROUP_WARNING, $paInfo->total_entries) . '</b></p>' . "\n";
      }

      echo '    <p align="center"><input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page'] . '&paID=' . $paInfo->products_options_id . '&action=deleteConfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'paDefault\');" class="operationButton"></p>' . "\n";
    }
?>

  </div>
</div>

<?php
  }
?>
