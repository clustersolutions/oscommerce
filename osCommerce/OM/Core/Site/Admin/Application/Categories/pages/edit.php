<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\ObjectInfo;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Admin\Application\Categories\Categories;

  $OSCOM_ObjectInfo = new ObjectInfo(Categories::get($_GET['id']));
?>

<script>
$(function() {
  $('#cEditForm input, #cEditForm select, #cEditForm textarea, #cEditForm fileupload').safetynet();
});
</script>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<form id="cEditForm" name="cEdit" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'Save&Process&cid=' . $OSCOM_ObjectInfo->getInt('parent_id') . '&id=' . $OSCOM_ObjectInfo->getInt('categories_id')); ?>" method="post" enctype="multipart/form-data">

<div id="formButtons" style="float: right;"><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . HTML::button(array('type' => 'button', 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'), 'params' => 'onclick="$.safetynet.suppressed(true); window.location.href=\'' . OSCOM::getLink(null, null, 'cid=' . $OSCOM_ObjectInfo->getInt('parent_id')) . '\';"')); ?></div>

<div style="clear: both;"></div>

<div class="infoBox">
  <h3><?php echo HTML::icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('categories_name'); ?></h3>

  <fieldset>
    <p><label for="parent_id"><?php echo OSCOM::getDef('field_parent_category'); ?></label><?php echo HTML::selectMenu('parent_id', array_merge(array(array('id' => '0', 'text' => OSCOM::getDef('top_category'))), $OSCOM_Application->getCategoryList()), $OSCOM_ObjectInfo->getInt('parent_id')); ?></p>
    <p><label><?php echo OSCOM::getDef('field_name'); ?></label></p>

<?php
  foreach ( $OSCOM_Language->getAll() as $l ) {
    echo '<p>' . $OSCOM_Language->showImage($l['code']) . '&nbsp;' . $l['name'] . '<br />' . HTML::inputField('categories_name[' . $l['id'] . ']', Categories::get($OSCOM_ObjectInfo->getInt('categories_id'), 'categories_name', $l['id'])) . '</p>';
  }

  if ( strlen($OSCOM_ObjectInfo->get('categories_image')) > 0 ) {
?>

    <p><?php echo HTML::link('public/categories/' . $OSCOM_ObjectInfo->get('categories_image'), HTML::image('public/categories/' . $OSCOM_ObjectInfo->get('categories_image'), $OSCOM_ObjectInfo->get('categories_name'), null, null, 'style="max-width: 64px; max-height: 64px;"'), 'target="_blank"') . '<br />public/categories/' . $OSCOM_ObjectInfo->getProtected('categories_image'); ?></p>

<?php
  }
?>

    <p><label for="categories_image"><?php echo OSCOM::getDef('field_image'); ?></label><?php echo HTML::fileField('categories_image'); ?></p>
  </fieldset>
</div>

</form>
