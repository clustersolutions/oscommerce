<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><h1><?php echo HEADING_TITLE; ?></h1></td>
    <td class="smallText" align="right">
<?php
  echo '<form name="search" action="' . osc_href_link_admin(FILENAME_CATEGORIES) . '" method="get">' .
       HEADING_TITLE_SEARCH . ' ' . osc_draw_input_field('search') .
       osc_draw_pull_down_menu('cPath', array_merge(array(array('id' => '', 'text' => '-- ' . TEXT_TOP . ' --')), $categories_array)) .
       '<input type="submit" value="GO" class="operationButton">' .
       '<input type="button" value="RESET" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_CATEGORIES) . '\';" class="sectionButton"' . ((!empty($_GET['search']) || ($current_category_id > 0)) ? '' : ' disabled') . '>' .
       '</form>';
?>

    </td>
  </tr>
</table>

<div id="infoBox_cDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_CATEGORIES; ?></th>
        <th><?php echo TABLE_HEADING_STATUS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qcategories = $osC_Database->query('select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified from :table_categories c, :table_categories_description cd where c.categories_id = cd.categories_id and cd.language_id = :language_id');

  if (!empty($_GET['search'])) {
    $Qcategories->appendQuery('and cd.categories_name like :categories_name');
    $Qcategories->bindValue(':categories_name', '%' . $_GET['search'] . '%');
  } else {
    $Qcategories->appendQuery('and c.parent_id = :parent_id');
    $Qcategories->bindInt(':parent_id', $current_category_id);
  }

  $Qcategories->appendQuery('order by c.sort_order, cd.categories_name');

  $Qcategories->bindTable(':table_categories', TABLE_CATEGORIES);
  $Qcategories->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
  $Qcategories->bindInt(':language_id', $osC_Language->getID());
  $Qcategories->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qcategories->execute();

  while ($Qcategories->next()) {
    if (!isset($cInfo) && (!isset($_GET['cID']) && !isset($_GET['pID']) || (isset($_GET['cID']) && ($_GET['cID'] == $Qcategories->valueInt('categories_id')))) && ($action != 'cNew')) {
      $cInfo_extra = array('childs_count' => tep_childs_in_category_count($Qcategories->valueInt('categories_id')),
                           'products_count' => tep_products_in_category_count($Qcategories->valueInt('categories_id')));

      $cInfo = new objectInfo(array_merge($Qcategories->toArray(), $cInfo_extra));
    }

    if (isset($cInfo) && ($Qcategories->valueInt('categories_id') == $cInfo->categories_id)) {
      echo '      <tr class="selected">' . "\n";
    } else {
      echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_CATEGORIES, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search'] . '&cID=' . $Qcategories->valueInt('categories_id')) . '\';">' . "\n";
    }
?>
        <td><?php echo osc_link_object(osc_href_link_admin(FILENAME_CATEGORIES, 'cPath=' . $osC_CategoryTree->buildBreadcrumb($Qcategories->valueInt('categories_id'))), osc_image('images/icons/folder.gif', ICON_FOLDER) . '&nbsp;<b>' . $Qcategories->value('categories_name') . '</b>'); ?></td>
        <td align="center"><?php echo osc_icon('checkbox_ticked.gif', null, null); ?></td>
        <td align="right">
<?php
    if (isset($cInfo) && ($Qcategories->valueInt('categories_id') == $cInfo->categories_id)) {
      echo '<a href="#" onclick="toggleInfoBox(\'cEdit\');">' . osc_icon('edit.png', IMAGE_EDIT) . '</a>&nbsp;' .
           '<a href="#" onclick="toggleInfoBox(\'cMove\');">' . osc_icon('move.png', IMAGE_MOVE) . '</a>&nbsp;' .
           '<a href="#" onclick="toggleInfoBox(\'cDelete\');">' . osc_icon('trash.png', IMAGE_DELETE) . '</a>';
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_CATEGORIES, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search'] . '&cID=' . $Qcategories->valueInt('categories_id') . '&action=cEdit'), osc_icon('edit.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_CATEGORIES, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search'] . '&cID=' . $Qcategories->valueInt('categories_id') . '&action=cMove'), osc_icon('move.png', IMAGE_MOVE)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_CATEGORIES, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search'] . '&cID=' . $Qcategories->valueInt('categories_id') . '&action=cDelete'), osc_icon('trash.png', IMAGE_DELETE));
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
      <td class="smallText"><?php echo $Qcategories->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_CATEGORIES); ?></td>
      <td class="smallText" align="right"><?php echo $Qcategories->displayBatchLinksPullDown('page', 'cPath=' . $cPath . '&search=' . $_GET['search']); ?></td>
    </tr>
  </table>

  <p align="right">
<?php
  if (!empty($cPath)) {
    echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_CATEGORIES, 'cPath=' . implode('_', array_slice($cPath_array, 0, -1)) . '&search=' . $_GET['search'] . '&cID=' . $current_category_id) . '\';" class="infoBoxButton"> ';
  }

  if (empty($_GET['search']) && ($action != 'cNew')) {
    echo '<input type="button" value="' . IMAGE_NEW_CATEGORY . '" onclick="toggleInfoBox(\'cNew\');" class="infoBoxButton"> ';
  }
?>
  </p>
</div>

<div id="infoBox_cNew" <?php if ($action != 'cNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_CATEGORY; ?></div>
  <div class="infoBoxContent">
    <form name="cNew" action="<?php echo osc_href_link_admin(FILENAME_CATEGORIES, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&action=save_category'); ?>" method="post" enctype="multipart/form-data">

    <p><?php echo TEXT_NEW_CATEGORY_INTRO; ?></p>
    <p>
<?php
  echo TEXT_CATEGORIES_NAME;

  foreach ($osC_Language->getAll() as $l) {
    echo '<br />' . osc_image('../includes/languages/' . $l['code'] . '/images/' . $l['image'], $l['name']) . '&nbsp;' . osc_draw_input_field('categories_name[' . $l['id'] . ']');
  }
?>
    </p>
    <p><?php echo TEXT_CATEGORIES_IMAGE . '<br />' . osc_draw_file_field('categories_image', true); ?></p>
    <p><?php echo TEXT_EDIT_SORT_ORDER . '<br />' . osc_draw_input_field('sort_order'); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'cDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($cInfo)) {
?>
<div id="infoBox_cMove" <?php if ($action != 'cMove') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('move.png', IMAGE_MOVE) . ' ' . $cInfo->categories_name; ?></div>
  <div class="infoBoxContent">
    <form name="cMove" action="<?php echo osc_href_link_admin(FILENAME_CATEGORIES, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search'] . '&cID=' . $cInfo->categories_id . '&action=move_category_confirm'); ?>" method="post">

    <p><?php echo sprintf(TEXT_MOVE_CATEGORIES_INTRO, $cInfo->categories_name); ?></p>
    <p><?php echo sprintf(TEXT_MOVE, $cInfo->categories_name) . '<br />' . osc_draw_pull_down_menu('move_to_category_id', $categories_array); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_MOVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'cDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_cDelete" <?php if ($action != 'cDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $cInfo->categories_name; ?></div>
  <div class="infoBoxContent">
    <p><?php echo TEXT_DELETE_CATEGORY_INTRO; ?></p>
    <p><?php echo '<b>' . $cInfo->categories_name . '</b>'; ?></p>
<?php
    if ($cInfo->childs_count > 0) {
      echo '    <p>' . sprintf(TEXT_DELETE_WARNING_CHILDS, $cInfo->childs_count) . '</p>';
    }

    if ($cInfo->products_count > 0) {
      echo '    <p>' . sprintf(TEXT_DELETE_WARNING_PRODUCTS, $cInfo->products_count) . '</p>';
    }
?>
    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_CATEGORIES, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search'] . '&cID=' . $cInfo->categories_id . '&action=delete_category_confirm') . '\'" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'cDefault\');" class="operationButton">'; ?></p>
  </div>
</div>

<div id="infoBox_cEdit" <?php if ($action != 'cEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $cInfo->categories_name; ?></div>
  <div class="infoBoxContent">
    <form name="cEdit" action="<?php echo osc_href_link_admin(FILENAME_CATEGORIES, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search'] . '&cID=' . $cInfo->categories_id . '&action=save_category'); ?>" method="post" enctype="multipart/form-data">

    <p><?php echo TEXT_EDIT_INTRO; ?></p>
    <p>
<?php
    echo TEXT_EDIT_CATEGORIES_NAME;

    $Qcd = $osC_Database->query('select language_id, categories_name from :table_categories_description where categories_id = :categories_id');
    $Qcd->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
    $Qcd->bindInt(':categories_id', $cInfo->categories_id);
    $Qcd->execute();

    $categories_name = array();
    while ($Qcd->next()) {
      $categories_name[$Qcd->valueInt('language_id')] = $Qcd->value('categories_name');
    }

    foreach ($osC_Language->getAll() as $l) {
      echo '<br />' . osc_image('../includes/languages/' . $l['code'] . '/images/' . $l['image'], $l['name']) . '&nbsp;' . osc_draw_input_field('categories_name[' . $l['id'] . ']', (isset($categories_name[$l['id']]) ? $categories_name[$l['id']] : null));
    }
?>
    </p>
    <p><?php echo osc_image('../images/' . $cInfo->categories_image, $cInfo->categories_name, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT) . '<br />' . DIR_WS_CATALOG . 'images/<br /><b>' . $cInfo->categories_image . '</b>'; ?></p>
    <p><?php echo TEXT_EDIT_CATEGORIES_IMAGE . '<br />' . osc_draw_file_field('categories_image', true); ?></p>
    <p><?php echo TEXT_EDIT_SORT_ORDER . '<br />' . osc_draw_input_field('sort_order', $cInfo->sort_order); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_UPDATE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'cDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>
