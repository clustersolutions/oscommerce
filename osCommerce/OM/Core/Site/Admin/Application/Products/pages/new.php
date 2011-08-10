<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  $new_product = true;
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div id="sectionMenuContainer" style="float: left; padding-bottom: 10px;">
  <span class="ui-widget-header ui-corner-all" style="padding: 10px 4px;">
    <span id="sectionMenu"><?php echo HTML::radioField('sections', array(array('id' => 'general', 'text' => OSCOM::getDef('section_general')), array('id' => 'data', 'text' => OSCOM::getDef('section_data')), array('id' => 'images', 'text' => OSCOM::getDef('section_images')), array('id' => 'variants', 'text' => OSCOM::getDef('section_variants')), array('id' => 'categories', 'text' => OSCOM::getDef('section_categories'))), (isset($_GET['tabIndex']) ? $_GET['tabIndex'] : null), null, ''); ?></span>
  </span>
</div>

<form id="pEditForm" name="pEdit" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'Save&Process&cid=' . $OSCOM_Application->getCurrentCategoryID()); ?>" method="post">

<div id="formButtons" style="float: right;"><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . HTML::button(array('type' => 'button', 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'), 'params' => 'onclick="$.safetynet.suppressed(true); window.location.href=\'' . OSCOM::getLink(null, null, 'cid=' . $OSCOM_Application->getCurrentCategoryID()) . '\';"')); ?></div>

<div style="clear: both;"></div>

<?php
// HPDL Modularize, zack zack!
  include('section_general.php');
//  include('section_password.php');
//  include('section_addressBook.php');
//  include('section_newsletters.php');
//  include('section_map.php');
//  include('section_social.php');
?>

</form>

<script>
$(function() {
  $('#sectionMenu').buttonsetTabs();

  $('#pEditForm input, #pEditForm select, #pEditForm textarea, #pEditForm fileupload').safetynet();
});
</script>
