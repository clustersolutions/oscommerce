<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
?>

<div id="sectionMenu_general">
  <div class="infoBox">

<?php
  if ( $new_product ) {
    echo '<h3>' . HTML::icon('new.png') . ' ' . OSCOM::getDef('action_heading_new_product') . '</h3>';
  } else {
    echo '<h3>' . HTML::icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('products_name') . '</h3>';
  }
?>

    <div id="languageTabs">
      <ul>

<?php
  foreach ( $OSCOM_Language->getAll() as $l ) {
    echo '<li>' . HTML::link('#languageTabs_' . $l['code'], $OSCOM_Language->showImage($l['code']) . '&nbsp;' . $l['name']) . '</li>';
  }
?>

      </ul>

<?php
  foreach ( $OSCOM_Language->getAll() as $l ) {
?>

      <div id="languageTabs_<?php echo $l['code']; ?>">
        <fieldset>
          <p><label for="<?php echo 'products_name[' . $l['id'] . ']'; ?>"><?php echo OSCOM::getDef('field_name'); ?></label><?php echo HTML::inputField('products_name[' . $l['id'] . ']', (!$new_product && isset($products_name[$l['id']]) ? $products_name[$l['id']] : null)); ?></p>
          <p><label for="<?php echo 'products_description[' . $l['id'] . ']'; ?>"><?php echo OSCOM::getDef('field_description'); ?></label><?php echo HTML::textareaField('products_description[' . $l['id'] . ']', (!$new_product && isset($products_description[$l['id']]) ? $products_description[$l['id']] : null)); ?></p>
          <p><label for="<?php echo 'products_keyword[' . $l['id'] . ']'; ?>"><?php echo OSCOM::getDef('field_keyword'); ?></label><?php echo HTML::inputField('products_keyword[' . $l['id'] . ']', (!$new_product && isset($products_keyword[$l['id']]) ? $products_keyword[$l['id']] : null)); ?></p>
          <p><label for="<?php echo 'products_tags[' . $l['id'] . ']'; ?>"><?php echo OSCOM::getDef('field_tags'); ?></label><?php echo HTML::inputField('products_tags[' . $l['id'] . ']', (!$new_product && isset($products_tags[$l['id']]) ? $products_tags[$l['id']] : null)); ?></p>
          <p><label for="<?php echo 'products_url[' . $l['id'] . ']'; ?>"><?php echo OSCOM::getDef('field_url'); ?></label><?php echo HTML::inputField('products_url[' . $l['id'] . ']', (!$new_product && isset($products_url[$l['id']]) ? $products_url[$l['id']] : null)); ?></p>
        </fieldset>
      </div>

<?php
  }
?>

    </div>
  </div>
</div>

<script>
$(function(){
  $("#languageTabs").tabs( { selected: 0 } );
});
</script>
