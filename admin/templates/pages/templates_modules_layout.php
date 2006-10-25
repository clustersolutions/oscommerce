<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/templates/' . $_GET['filter'] . '.php');
  require('includes/classes/directory_listing.php');

  $boxes_array = array();

  $Qboxes = $osC_Database->query('select id, title from :table_templates_boxes where modules_group = :modules_group order by title');
  $Qboxes->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
  $Qboxes->bindValue(':modules_group', $_GET['set']);
  $Qboxes->execute();

  while ($Qboxes->next()) {
    $boxes_array[] = array('id' => $Qboxes->valueInt('id'), 'text' => $Qboxes->value('title'));
  }

  $filter_id = 0;
  $templates_array = array();

  $Qtemplates = $osC_Database->query('select id, title, code from :table_templates order by title');
  $Qtemplates->bindTable(':table_templates', TABLE_TEMPLATES);
  $Qtemplates->execute();

  while ($Qtemplates->next()) {
    if ($Qtemplates->value('code') == $_GET['filter']) {
      $filter_id = $Qtemplates->valueInt('id');
    }

    $templates_array[] = array('id' => $Qtemplates->value('code'), 'text' => $Qtemplates->value('title'));
  }

  $pages_array = array(array('id' => $filter_id . '/*', 'text' => '*'));

  $d_boxes = new osC_DirectoryListing('../templates/' . $_GET['filter'] . '/content');
  $d_boxes->setRecursive(true);
  $d_boxes->setAddDirectoryToFilename(true);
  $d_boxes->setCheckExtension('php');
  $d_boxes->setExcludeEntries('.svn');

  foreach ($d_boxes->getFiles(false) as $box) {
    if ($box['is_directory'] === true) {
      $entry = array('id' => $filter_id . '/' . $box['name'] . '/*', 'text' => $box['name'] . '/*');
    } else {
      $page_filename = substr($box['name'], 0, strrpos($box['name'], '.'));
      $entry = array('id' => $filter_id . '/' . $page_filename, 'text' => $page_filename);
    }

    if ( ($_GET['filter'] != DEFAULT_TEMPLATE) && ($d_boxes->getSize() > 0) ) {
      $entry['group'] = '-- ' . $_GET['filter'] . ' --';
    }

    $pages_array[] = $entry;
  }

  if ($_GET['filter'] != DEFAULT_TEMPLATE) {
    $d_boxes = new osC_DirectoryListing('../templates/' . DEFAULT_TEMPLATE . '/content');
    $d_boxes->setRecursive(true);
    $d_boxes->setAddDirectoryToFilename(true);
    $d_boxes->setCheckExtension('php');
    $d_boxes->setExcludeEntries('.svn');

    foreach ($d_boxes->getFiles(false) as $box) {
      if ($box['is_directory'] === true) {
        $entry = array('id' => $filter_id . '/' . $box['name'] . '/*', 'text' => $box['name'] . '/*');
      } else {
        $page_filename = substr($box['name'], 0, strrpos($box['name'], '.'));
        $entry = array('id' => $filter_id . '/' . $page_filename, 'text' => $page_filename);
      }

      $check_entry = $entry;
      $check_entry['group'] = '-- ' . $_GET['filter'] . ' --';

      if (!in_array($check_entry, $pages_array)) {
        $entry['group'] = '-- ' . DEFAULT_TEMPLATE . ' --';

        $pages_array[] = $entry;
      }
    }
  }

  $groups_array = array();

  $class = 'osC_Template_' . $_GET['filter'];
  $filter_template = new $class();

  foreach ($filter_template->getGroups($_GET['set']) as $group) {
    $groups_array[] = array('id' => $group, 'text' => $group);
  }

  $Qgroups = $osC_Database->query('select distinct b2p.boxes_group from :table_templates_boxes_to_pages b2p, :table_templates_boxes b where b2p.templates_id = :templates_id and b2p.templates_boxes_id = b.id and b.modules_group = :modules_group and b2p.boxes_group not in (:boxes_group) order by b2p.boxes_group');
  $Qgroups->bindTable(':table_templates_boxes_to_pages', TABLE_TEMPLATES_BOXES_TO_PAGES);
  $Qgroups->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
  $Qgroups->bindInt(':templates_id', $filter_id);
  $Qgroups->bindValue(':modules_group', $_GET['set']);
  $Qgroups->bindRaw(':boxes_group', '"' . implode('", "', $filter_template->getGroups($_GET['set'])) . '"');
  $Qgroups->execute();

  while ($Qgroups->next()) {
    $groups_array[] = array('id' => $Qgroups->value('boxes_group'), 'text' => $Qgroups->value('boxes_group'));
  }

  if (empty($groups_array) === false) {
    array_unshift($groups_array, array('id' => null, 'text' => TEXT_PLEASE_SELECT));
  }
?>

<div>
  <div style="float: right;">
    <form name="template" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT); ?>" method="get"><?php echo osc_draw_hidden_field($osC_Template->getModule(), null) . osc_draw_hidden_field('set', $_GET['set']); ?>
      <?php echo osc_draw_pull_down_menu('filter', $templates_array, $filter_id); ?><input type="submit" value="GO">
    </form>
  </div>

  <h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set']), $osC_Template->getPageTitle()); ?></h1>
</div>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div id="infoBox_lDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_BOXES; ?></th>
        <th><?php echo TABLE_HEADING_PAGES; ?></th>
        <th><?php echo TABLE_HEADING_PAGE_SPECIFIC; ?></th>
        <th><?php echo TABLE_HEADING_GROUP; ?></th>
        <th><?php echo TABLE_HEADING_SORT_ORDER; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>

<?php
  $Qlayout = $osC_Database->query('select b2p.*, b.title as box_title from :table_templates_boxes_to_pages b2p, :table_templates_boxes b where b2p.templates_id = :templates_id and b2p.templates_boxes_id = b.id and b.modules_group = :modules_group order by b2p.page_specific desc, b2p.boxes_group, b2p.sort_order, b.title');
  $Qlayout->bindTable(':table_templates_boxes_to_pages', TABLE_TEMPLATES_BOXES_TO_PAGES);
  $Qlayout->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
  $Qlayout->bindInt(':templates_id', $filter_id);
  $Qlayout->bindValue(':modules_group', $_GET['set']);
  $Qlayout->execute();

  while ($Qlayout->next()) {
    if (!isset($lInfo) && (!isset($_GET['lID']) || (isset($_GET['lID']) && ($_GET['lID'] == $Qlayout->valueInt('id'))))) {
      $lInfo = new objectInfo($Qlayout->toArray());
    }
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td><?php echo $Qlayout->value('box_title'); ?></td>
        <td><?php echo $Qlayout->value('content_page'); ?></td>
        <td align="center"><?php echo osc_icon(($Qlayout->valueInt('page_specific') === 1 ? 'checkbox_ticked.gif' : 'checkbox.gif'), null, null); ?></td>
        <td align="right"><?php echo $Qlayout->value('boxes_group'); ?></td>
        <td align="right"><?php echo $Qlayout->valueInt('sort_order'); ?></td>
        <td align="right">

<?php
    if (isset($lInfo) && ($Qlayout->valueInt('id') == $lInfo->id)) {
      echo osc_link_object('#', osc_icon('configure.png', IMAGE_EDIT), 'onclick="toggleInfoBox(\'lEdit\');"') . '&nbsp;' .
           osc_link_object('#', osc_icon('trash.png', IMAGE_DELETE), 'onclick="toggleInfoBox(\'lDelete\');"');
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter'] . '&lID=' . $Qlayout->valueInt('id') . '&action=lEdit'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter'] . '&lID=' . $Qlayout->valueInt('id') . '&action=lDelete'), osc_icon('trash.png', IMAGE_DELETE));
    }
?>

        </td>
      </tr>

<?php
  }
?>

    </tbody>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'lNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_lNew" <?php if ($_GET['action'] != 'lNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_BOX_LAYOUT; ?></div>
  <div class="infoBoxContent">
    <form name="lNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter'] . '&action=save'); ?>" method="post">

    <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_BOXES . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('box', $boxes_array, null, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_PAGES . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('content_page', $pages_array, null, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%">&nbsp;</td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('page_specific', '1') . '&nbsp;<b>' . TEXT_INFO_PAGE_SPECIFIC . '</b>'; ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_GROUP . '</b>'; ?></td>
        <td class="smallText" width="60%">

<?php
  if (empty($groups_array) === false) {
    echo osc_draw_pull_down_menu('group', $groups_array, null, 'style="width: 30%;"') . '&nbsp;&nbsp;<b>' . TEXT_INFO_GROUP_NEW . '</b>&nbsp;';
  }

  echo osc_draw_input_field('group_new', null, 'style="width: ' . (empty($groups_array) ? '100%' : '40%') . ';"');
?>

        </td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_SORT_ORDER . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('sort_order', null, 'style="width: 100%;"'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'lDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($lInfo)) {
?>

<div id="infoBox_lEdit" <?php if ($_GET['action'] != 'lEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $lInfo->box_title; ?></div>
  <div class="infoBoxContent">
    <form name="lEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter'] . '&lID=' . $lInfo->id . '&action=save'); ?>" method="post">

    <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_PAGES . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('content_page', $pages_array, $lInfo->templates_id . '/' . $lInfo->content_page, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%">&nbsp;</td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('page_specific', '1', $lInfo->page_specific) . '&nbsp;<b>' . TEXT_INFO_PAGE_SPECIFIC . '</b>'; ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_GROUP . '</b>'; ?></td>
        <td class="smallText" width="60%">

<?php
  if (empty($groups_array) === false) {
    echo osc_draw_pull_down_menu('group', $groups_array, $lInfo->boxes_group, 'style="width: 30%;"') . '&nbsp;&nbsp;<b>' . TEXT_INFO_GROUP_NEW . '</b>&nbsp;';
  }

  echo osc_draw_input_field('group_new', null, 'style="width: ' . (empty($groups_array) ? '100%' : '40%') . ';"');
?>

        </td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_SORT_ORDER . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('sort_order', $lInfo->sort_order, 'style="width: 100%;"'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'lDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_lDelete" <?php if ($_GET['action'] != 'lDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $lInfo->box_title; ?></div>
  <div class="infoBoxContent">
    <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>

    <p align="center"><?php echo '<input type="button" value="' . IMAGE_BOX_REMOVE . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter'] . '&lID=' . $lInfo->id . '&action=remove') . '\';"> <input type="button" value="' . IMAGE_CANCEL . '" class="operationButton" onclick="toggleInfoBox(\'lDefault\');">'; ?></p>
  </div>
</div>

<?php
  }
?>
