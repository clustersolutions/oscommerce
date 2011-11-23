<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Admin\Application\ProductAttributes\ProductAttributes;

  if ( !$new_product ) {
    $attributes = $OSCOM_ObjectInfo->get('attributes');
  }
?>

<div id="sectionMenu_data">
  <div class="infoBox">

<?php
  if ( $new_product ) {
    echo '<h3>' . HTML::icon('new.png') . ' ' . OSCOM::getDef('action_heading_new_product') . '</h3>';
  } else {
    echo '<h3>' . HTML::icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('products_name') . '</h3>';
  }
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>

<?php
  $data_width = ( !$new_product && ($OSCOM_ObjectInfo->getInt('has_children') === 1) ) ? '100%' : '50%';

  if ( $new_product || ($OSCOM_ObjectInfo->getInt('has_children') !== 1) ) {
?>

        <td width="<?php echo $data_width;?>" height="100%" valign="top">
          <h4><?php echo OSCOM::getDef('subsection_price'); ?></h4>

          <fieldset>
            <p><label for="products_price_tax_class"><?php echo OSCOM::getDef('field_tax_class'); ?></label><?php echo HTML::selectMenu('products_tax_class_id', $OSCOM_Application->getTaxClassesList(), (!$new_product ? $OSCOM_ObjectInfo->getInt('products_tax_class_id') : null), 'id="products_price_tax_class" onchange="updateGross(\'products_price\');"'); ?></p>
            <p><label for="products_price"><?php echo OSCOM::getDef('field_price_net'); ?></label><?php echo HTML::inputField('products_price', (!$new_product ? $OSCOM_ObjectInfo->get('products_price') : null), 'onkeyup="updateGross(\'products_price\')"'); ?></p>
            <p><label for="products_price_gross"><?php echo OSCOM::getDef('field_price_gross'); ?></label><?php echo HTML::inputField('products_price_gross', (!$new_product ? $OSCOM_ObjectInfo->get('products_price') : null), 'onkeyup="updateNet(\'products_price\')"'); ?></p>
          </fieldset>
        </td>

<?php
  }
?>

        <td width="<?php echo $data_width;?>" height="100%" valign="top">
          <h4><?php echo OSCOM::getDef('subsection_data'); ?></h4>

          <fieldset>
            <p id="productStatusField"><label for="products_status"><?php echo OSCOM::getDef('field_status'); ?></label><?php echo HTML::radioField('products_status', array(array('id' => '1', 'text' => OSCOM::getDef('status_enabled')), array('id' => '0', 'text' => OSCOM::getDef('status_disabled'))), (!$new_product ? $OSCOM_ObjectInfo->get('products_status') : '0')); ?></p>

<script>$('#productStatusField').buttonset();</script>

<?php
  if ( $new_product || ($OSCOM_ObjectInfo->getInt('has_children') !== 1) ) {
?>

            <p><label for="products_model"><?php echo OSCOM::getDef('field_model'); ?></label><?php echo HTML::inputField('products_model', (!$new_product ? $OSCOM_ObjectInfo->get('products_model') : null)); ?></p>
            <p><label for="products_quantity"><?php echo OSCOM::getDef('field_quantity'); ?></label><?php echo HTML::inputField('products_quantity', (!$new_product ? $OSCOM_ObjectInfo->get('products_quantity') : null)); ?></p>
            <p><label for="products_weight"><?php echo OSCOM::getDef('field_weight'); ?></label><?php echo HTML::inputField('products_weight', (!$new_product ? $OSCOM_ObjectInfo->get('products_weight') : null)) . HTML::selectMenu('products_weight_class', $OSCOM_Application->getWeightClassesList(), (!$new_product ? $OSCOM_ObjectInfo->get('products_weight_class') : SHIPPING_WEIGHT_UNIT)); ?></p>

<?php
  }
?>

          </fieldset>
        </td>
      </tr>
    </table>

<?php
  if ( !$new_product && ($OSCOM_ObjectInfo->getInt('has_children') === 1) ) {
    echo HTML::hiddenField('products_tax_class_id', 0) . HTML::hiddenField('products_price', 0) . HTML::hiddenField('products_model') . HTML::hiddenField('products_quantity', 0), HTML::hiddenField('products_weight', 0), HTML::hiddenField('products_weight_class', 0);
  }
?>

    <h4><?php echo OSCOM::getDef('subsection_attributes'); ?></h4>

    <fieldset>

<?php
  $installed = ProductAttributes::getInstalled();

  foreach ( $installed['entries'] as $pa ) {
    $pamo = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\ProductAttribute\\' . $pa['code'];
    $pam = new $pamo();

    echo '<p><label for="pa_' . $pa['code'] . '">' . $pa['title'] . '</label>' . $pam->getInputField(!$new_product && isset($attributes[$pa['id']]) ? $attributes[$pa['id']] : null) . '</p>';
  }
?>

    </fieldset>
  </div>
</div>

<script>

<?php
  $tr_array = array();

  foreach ( $OSCOM_Application->getTaxClassesList() as $tc_entry ) {
    if ( $tc_entry['id'] > 0 ) {
      $tr_array[$tc_entry['id']] = $OSCOM_Tax->getTaxRate($tc_entry['id']);
    }
  }

  echo 'var tax_rates = ' . json_encode($tr_array) . ';' . "\n";
?>

function doRound(x, places) {
  return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
}

function getTaxRate(field) {
  var value = $('#' + field + '_tax_class').val();

  if ( (value > 0) && (tax_rates[value] > 0) ) {
    return tax_rates[value];
  } else {
    return 0;
  }
}

function updateGross(field) {
  var taxRate = getTaxRate(field);
  var grossValue = $('#' + field).val();

  if (taxRate > 0) {
    grossValue = grossValue * ((taxRate / 100) + 1);
  }

  $('#' + field + '_gross').val(doRound(grossValue, 4));
}

function updateNet(field) {
  var taxRate = getTaxRate(field);
  var netValue = $('#' + field + '_gross').val();

  if (taxRate > 0) {
    netValue = netValue / ((taxRate / 100) + 1);
  }

  $('#' + field).val(doRound(netValue, 4));
}

$(function(){
  updateGross('products_price');
});
</script>
