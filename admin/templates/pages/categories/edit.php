<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Categories_Admin::getData($_GET['cID']));

  $categories_array = array(array('id' => '0',
                                  'text' => $osC_Language->get('top_category')));

  foreach ($osC_CategoryTree->getTree() as $value) {
    $categories_array[] = array('id' => $value['id'],
                                'text' => $value['title']);
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->get('categories_name'); ?></div>
<div class="infoBoxContent">
  <form name="cEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . '&cID=' . $osC_ObjectInfo->get('categories_id') . '&action=save'); ?>" method="post" enctype="multipart/form-data">

  <p><?php echo $osC_Language->get('introduction_edit_category'); ?></p>

  <p>

<?php
  echo $osC_Language->get('field_name');

  $Qcd = $osC_Database->query('select language_id, categories_name from :table_categories_description where categories_id = :categories_id');
  $Qcd->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
  $Qcd->bindInt(':categories_id', $osC_ObjectInfo->get('categories_id'));
  $Qcd->execute();

  $categories_name = array();
  while ($Qcd->next()) {
    $categories_name[$Qcd->valueInt('language_id')] = $Qcd->value('categories_name');
  }

  foreach ($osC_Language->getAll() as $l) {
    echo '<br />' . $osC_Language->showImage($l['code']) . '&nbsp;' . osc_draw_input_field('categories_name[' . $l['id'] . ']', (isset($categories_name[$l['id']]) ? $categories_name[$l['id']] : null));
  }
?>

  </p>
  <p><?php echo osc_image('../' . DIR_WS_IMAGES . 'categories/' . $osC_ObjectInfo->get('categories_image'), $osC_ObjectInfo->get('categories_name'), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT) . '<br />' . DIR_WS_CATALOG . 'images/categories/<br /><b>' . $osC_ObjectInfo->get('categories_image') . '</b>'; ?></p>
  <p><?php echo $osC_Language->get('field_image') . '<br />' . osc_draw_file_field('categories_image', true); ?></p>
  <p><?php echo $osC_Language->get('field_sort_order') . '<br />' . osc_draw_input_field('sort_order', $osC_ObjectInfo->get('sort_order')); ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
