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

<div id="infoBox_mDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_MANUFACTURERS; ?></th>
        <th><?php echo TABLE_HEADING_URL_CLICKS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qmanufacturers = $osC_Database->query('select manufacturers_id, manufacturers_name, manufacturers_image, date_added, last_modified from :table_manufacturers order by manufacturers_name');
  $Qmanufacturers->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
  $Qmanufacturers->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qmanufacturers->execute();

  while ($Qmanufacturers->next()) {
    $Qclicks = $osC_Database->query('select sum(url_clicked) as total from :table_manufacturers_info where manufacturers_id = :manufacturers_id');
    $Qclicks->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
    $Qclicks->bindInt(':manufacturers_id', $Qmanufacturers->valueInt('manufacturers_id'));
    $Qclicks->execute();

    if (!isset($mInfo) && (!isset($_GET['mID']) || (isset($_GET['mID']) && ($_GET['mID'] == $Qmanufacturers->value('manufacturers_id')))) && ($action != 'mNew')) {
      $Qproducts = $osC_Database->query('select count(*) as products_count from :table_products where manufacturers_id = :manufacturers_id');
      $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
      $Qproducts->bindInt(':manufacturers_id', $Qmanufacturers->valueInt('manufacturers_id'));
      $Qproducts->execute();

      $mInfo = new objectInfo(array_merge($Qmanufacturers->toArray(), $Qproducts->toArray()));
    }

    if (isset($mInfo) && ($Qmanufacturers->valueInt('manufacturers_id') == $mInfo->manufacturers_id)) {
      echo '      <tr class="selected">' . "\n";
    } else {
      echo '      <tr onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);" onClick="document.location.href=\'' . tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $Qmanufacturers->valueInt('manufacturers_id')) . '\';">' . "\n";
    }
?>
        <td><?php echo $Qmanufacturers->value('manufacturers_name'); ?></td>
        <td><?php echo $Qclicks->valueInt('total'); ?></td>
        <td align="right">
<?php
    if (isset($mInfo) && ($Qmanufacturers->valueInt('manufacturers_id') == $mInfo->manufacturers_id)) {
      echo '<a href="#" onClick="toggleInfoBox(\'mEdit\');">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="#" onClick="toggleInfoBox(\'mDelete\');">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $Qmanufacturers->valueInt('manufacturers_id') . '&action=mEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="' . tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $Qmanufacturers->valueInt('manufacturers_id') . '&action=mDelete') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
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
      <td class="smallText"><?php echo $Qmanufacturers->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS); ?></td>
      <td class="smallText" align="right"><?php echo $Qmanufacturers->displayBatchLinksPullDown(); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onClick="toggleInfoBox(\'mNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_mNew" <?php if ($action != 'mNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/new.png', IMAGE_INSERT, '16', '16') . ' ' . TEXT_HEADING_NEW_MANUFACTURER; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('mNew', FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&action=save', 'post', 'enctype="multipart/form-data"'); ?>

    <p><?php echo TEXT_NEW_INTRO; ?></p>
    <p><?php echo TEXT_MANUFACTURERS_NAME . '<br>' . osc_draw_input_field('manufacturers_name'); ?></p>
    <p><?php echo TEXT_MANUFACTURERS_IMAGE . '<br>' . osc_draw_file_field('manufacturers_image'); ?></p>
    <p>
<?php
  echo TEXT_MANUFACTURERS_URL;

  foreach ($osC_Language->getAll() as $l) {
    echo '<br>' . tep_image('../includes/languages/' . $l['directory'] . '/images/' . $l['image'], $l['name']) . '&nbsp;' . osc_draw_input_field('manufacturers_url[' . $l['id'] . ']');
  }
?>
    </p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'mDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($mInfo)) {
?>

<div id="infoBox_mEdit" <?php if ($action != 'mEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $mInfo->manufacturers_name; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('mEdit', FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=save', 'post', 'enctype="multipart/form-data"'); ?>

    <p><?php echo TEXT_EDIT_INTRO; ?></p>
    <p><?php echo TEXT_MANUFACTURERS_NAME . '<br>' . osc_draw_input_field('manufacturers_name', $mInfo->manufacturers_name); ?></p>
    <p><?php echo tep_image('../images/' . $mInfo->manufacturers_image, $mInfo->manufacturers_name) . '<br>' . DIR_WS_CATALOG . 'images/<br><b>' . $mInfo->manufacturers_image . '</b>'; ?></p>
    <p><?php echo TEXT_MANUFACTURERS_IMAGE . '<br>' . osc_draw_file_field('manufacturers_image'); ?></p>
    <p>
<?php
    echo TEXT_MANUFACTURERS_URL;

    foreach ($osC_Language->getAll() as $l) {
      echo '<br>' . tep_image('../includes/languages/' . $l['directory'] . '/images/' . $l['image'], $l['name']) . '&nbsp;' . osc_draw_input_field('manufacturers_url[' . $l['id'] . ']', tep_get_manufacturer_url($mInfo->manufacturers_id, $l['id']));
    }
?>
    </p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'mDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_mDelete" <?php if ($action != 'mDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' ' . $mInfo->manufacturers_name; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('mDelete', FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=deleteconfirm'); ?>

    <p><?php echo TEXT_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $mInfo->manufacturers_name . '</b>'; ?></p>

<?php
    if (!empty($mInfo->manufacturers_image)) {
      echo '    <p>' . osc_draw_checkbox_field('delete_image', '', true) . ' ' . TEXT_DELETE_IMAGE . '</p>';
    }

    if ($mInfo->products_count > 0) {
      echo '    <p>' . osc_draw_checkbox_field('delete_products') . ' ' . TEXT_DELETE_PRODUCTS . '</p>' .
           '    <p>' . sprintf(TEXT_DELETE_WARNING_PRODUCTS, $mInfo->products_count) . '</p>';
    }
?>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'mDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>
