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

<div id="sectionMenu_variants">
  <div class="infoBox">

<?php
  if ( $new_product ) {
    echo '<h3>' . HTML::icon('new.png') . ' ' . OSCOM::getDef('action_heading_new_product') . '</h3>';
  } else {
    echo '<h3>' . HTML::icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('products_name') . '</h3>';
  }
?>

    <div id="variantListing">
      <p style="float: right;"><?php echo HTML::button(array('type' => 'button', 'icon' => 'plus', 'title' => OSCOM::getDef('button_add'), 'params' => 'onclick="openNewVariantForm();"')); ?></p>

      <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="productVariantsDataTable">
        <thead>
          <tr>
            <th><?php echo OSCOM::getDef('table_heading_variants'); ?></th>
            <th><?php echo OSCOM::getDef('table_heading_price'); ?></th>
            <th><?php echo OSCOM::getDef('table_heading_quantity'); ?></th>
            <th width="150"><?php echo OSCOM::getDef('table_heading_action'); ?></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th colspan="4">&nbsp;</th>
          </tr>
        </tfoot>
        <tbody>

<?php
  $v = array('db' => array());

  if ( !$new_product ) {
    $v = array('db' => $OSCOM_Application->getProductVariants($OSCOM_ObjectInfo->getInt('products_id')));

    foreach ( $v['db'] as $key => $pv ) {
      $variants_string = '';

      foreach ( $pv['combos'] as $pvc ) {
        $variants_string .= $pvc['group_title'] . ': ' . $pvc['value_title'] . ', ';
      }

      $variants_string = substr($variants_string, 0, -2);

      echo '          <tr id="dbVariant' . $key . '">' .
           '            <td>' . $variants_string . '</td>' .
           '            <td>' . $OSCOM_Currencies->format($pv['price']) . '</td>' .
           '            <td>' . $pv['quantity'] . '</td>' .
           '            <td align="right"><span class="variantActions"><span class="defaultVariantActions"><a href="#" onclick="openEditVariantForm(' . $key . '); return false;">' . HTML::icon('edit.png') . '</a> <a href="#" onclick="deleteVariant(\'' . $key . '\'); return false;">' . HTML::icon('trash.png') . '</a></span></span></td>' .
           '          </tr>';
    }
  }
?>

        </tbody>
      </table>

      <div style="padding: 2px;">
        <span id="dataTableLegend"><?php echo '<b>' . OSCOM::getDef('table_action_legend') . '</b> ' . HTML::icon('edit.png') . '&nbsp;' . OSCOM::getDef('icon_edit') . '&nbsp;&nbsp;' . HTML::icon('trash.png') . '&nbsp;' . OSCOM::getDef('icon_trash'); ?></span>
      </div>

<?php
  echo HTML::hiddenField('assigned_variants', json_encode($v), 'id="assigned_variants"') . HTML::hiddenField('deleted_variants', null, 'id="deleted_variants"');
?>

<script>
$(function() {
  prettifyDataTable();
});
</script>

    </div>

    <div id="variantForm" style="display: none;">
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td width="30%" valign="top">
            <h4><?php echo OSCOM::getDef('subsection_variants'); ?></h4>

            <fieldset>
              <ul id="availableVariantsList" style="list-style-type: none; padding-left: 10px;">

<?php
  $vg = $OSCOM_Application->getVariantGroups();

  foreach ( $vg as $vgroup_id => $vgroup ) {
    echo '                <li style="padding-bottom: 10px;">' . HTML::checkboxField('vg' . $vgroup_id, $vgroup_id, null, 'disabled="disabled" onclick="vgClearChildren(' . $vgroup_id . ');"') . ' <strong>' . $vgroup['title'] . '</strong>' .
         '                  <ul style="list-style-type: none;">';

    foreach ( $vgroup['values'] as $vvalue_id => $vvalue ) {
      echo '                    <li>';

      if ( $vgroup['allow_multiple_values'] === true ) {
        echo HTML::checkboxField('vv' . $vvalue_id, $vvalue_id, null, 'onclick="vgEnable(' . $vgroup_id . ');"');
      } else {
        echo HTML::radioField('vv' . $vgroup_id, $vvalue_id, null, 'id="vv' . $vvalue_id . '" onclick="vgEnable(' . $vgroup_id . ');"');
      }

      echo ' ' . $vvalue['title'] . '</li>';
    }

    echo '                  </ul>' .
         '                </li>';
  }
?>

              </ul>
            </fieldset>
          </td>
          <td width="70%" valign="top">
            <h4><?php echo OSCOM::getDef('subsection_price'); ?></h4>

            <fieldset>
              <p><label for="variants_price_tax_class"><?php echo OSCOM::getDef('field_tax_class'); ?></label><?php echo HTML::selectMenu('variants_price_tax_class', $OSCOM_Application->getTaxClassesList(), null, 'onchange="updateGross(\'variants_price\');"'); ?></p>
              <p><label for="variants_price"><?php echo OSCOM::getDef('field_price_net'); ?></label><?php echo HTML::inputField('variants_price', null, 'onkeyup="updateGross(\'variants_price\')"'); ?></p>
              <p><label for="variants_price_gross"><?php echo OSCOM::getDef('field_price_gross'); ?></label><?php echo HTML::inputField('variants_price_gross', null, 'onkeyup="updateNet(\'variants_price\')"'); ?></p>
            </fieldset>

            <h4><?php echo OSCOM::getDef('subsection_data'); ?></h4>

            <fieldset>
              <p id="vstatus"><label for="variants_status"><?php echo OSCOM::getDef('field_status'); ?></label><?php echo HTML::radioField('variants_status', array(array('id' => '1', 'text' => OSCOM::getDef('status_enabled')), array('id' => '0', 'text' => OSCOM::getDef('status_disabled')))); ?></p>
              <p><label for="variants_model"><?php echo OSCOM::getDef('field_model'); ?></label><?php echo HTML::inputField('variants_model', null); ?></p>
              <p><label for="variants_quantity"><?php echo OSCOM::getDef('field_quantity'); ?></label><?php echo HTML::inputField('variants_quantity', null); ?></p>
              <p><label for="variants_weight"><?php echo OSCOM::getDef('field_weight'); ?></label><?php echo HTML::inputField('variants_weight', null, 'size="6"'). HTML::selectMenu('variants_weight_class', $OSCOM_Application->getWeightClassesList(), SHIPPING_WEIGHT_UNIT); ?></p>
              <p><label for="variants_default"><?php echo OSCOM::getDef('field_default'); ?></label><?php echo HTML::checkboxField('variants_default'); ?></p>
            </fieldset>

<script>
$(function() {
  $('#vstatus').buttonset();
});
</script>

          </td>
        </tr>
      </table>

      <p id="variantSubmitButtonsNew"><?php echo HTML::button(array('type' => 'button', 'params' => 'onclick="processVariantForm();"', 'priority' => 'primary', 'icon' => 'plus', 'title' => OSCOM::getDef('button_add'))) . ' ' . HTML::button(array('type' => 'button', 'params' => 'onclick="closeVariantForm();"', 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>
      <p id="variantSubmitButtonsEdit"><?php echo HTML::button(array('type' => 'button', 'params' => 'data-vButtonType="henrysBucket"', 'priority' => 'primary', 'icon' => 'arrowrefresh-1-n', 'title' => OSCOM::getDef('button_update'))) . ' ' . HTML::button(array('type' => 'button', 'params' => 'onclick="closeVariantForm();"', 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>
    </div>
  </div>
</div>

<div id="dialogDeleteVariant" title="<?php echo HTML::output(OSCOM::getDef('dialog_delete_variant_title')); ?>">
  <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span><?php echo OSCOM::getDef('dialog_delete_variant_desc'); ?></p>
</div>

<div id="dialogDeleteNewVariant" title="<?php echo HTML::output(OSCOM::getDef('dialog_delete_new_variant_title')); ?>">
  <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span><?php echo OSCOM::getDef('dialog_delete_new_variant_desc'); ?></p>
</div>

<div id="dialogDeleteDefaultVariant" title="<?php echo HTML::output(OSCOM::getDef('dialog_delete_default_variant_title')); ?>">
  <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span><?php echo OSCOM::getDef('dialog_delete_default_variant_desc'); ?></p>
</div>

<script>
var edit_icon = '<?php echo HTML::icon('edit.png'); ?>';
var trash_icon = '<?php echo HTML::icon('trash.png'); ?>';
var undo_icon = '<?php echo HTML::icon('undo.png'); ?>';

var defaultVariantMarker = ' <span id="defaultVariantMarker">(default)</span>';

var defaultWeightClass = <?php echo SHIPPING_WEIGHT_UNIT; ?>;

var variant_combos = <?php echo json_encode($vg); ?>;
var v = $.parseJSON($('#assigned_variants').val());
var delete_variant_combos = { };

var variant_default_orig, variant_default_new;

$.each(v['db'], function (index, value) {
  if ( value['default'] === true ) {
    variant_default_orig = index;

    return false; // break each() loop
  }
});

if ( typeof variant_default_orig != 'undefined' ) {
  $('#dbVariant' + variant_default_orig + ' td:first').append(defaultVariantMarker);
}

var newVariantCounter = 0;

function prettifyDataTable() {
  $('#productVariantsDataTable tbody tr').removeClass('alt'); // reset all rows
  $('#productVariantsDataTable tbody tr:odd').addClass('alt');

  $('#productVariantsDataTable tbody tr').each(function() {
    $(this).hover( function() { $(this).addClass('mouseOver'); }, function() { $(this).removeClass('mouseOver'); });
  });
}

function openNewVariantForm() {
  $('#sectionMenuContainer').animate({'opacity': '0.30'}, 'fast').children().attr('disabled', true);
  $('#formButtons').animate({'opacity': '0.30'}, 'fast').children().attr('disabled', true);

  $('#variantListing').hide();

  if ( (typeof variant_default_orig == 'undefined') && (typeof variant_default_new == 'undefined') ) {
    $('#variants_default').click();
    $('#variants_default').attr('disabled', true);
  }

  $('#variantSubmitButtonsNew').show();
  $('#variantSubmitButtonsEdit').hide();

  $('#variantForm').show();
}

function openEditVariantForm(id, isNew) {
  if ( typeof isNew == 'undefined' ) {
    isNew = false;
  }

  var key = ( isNew === true ) ? 'new' : 'db';

  $('#sectionMenuContainer').animate({'opacity': '0.30'}, 'fast').children().attr('disabled', true);
  $('#formButtons').animate({'opacity': '0.30'}, 'fast').children().attr('disabled', true);

  $('#variantListing').hide();

  $.each(v[key][id]['combos'], function (name, value) {
    $('#vv' + value['value_id']).click();
  });

  $('#variants_price_tax_class').val(v[key][id]['tax_class_id']);
  $('#variants_price').val(v[key][id]['price']);

  updateGross('variants_price');

  $('#variants_model').val(v[key][id]['model']);
  $('#variants_quantity').val(v[key][id]['quantity']);
  $('#variants_weight').val(v[key][id]['weight']);
  $('#variants_weight_class').val(v[key][id]['weight_class_id']);

  if ( v[key][id]['default'] === true ) {
    $('#variants_default').click();
    $('#variants_default').attr('disabled', true);
  }

  $('input[name="variants_status"]').filter('[value="' + v[key][id]['status'] + '"]').attr('checked', true);
  $('#vstatus').buttonset('refresh');

// Replace onclick handler to pass the product variant id as a function parameter
  $('#variantSubmitButtonsEdit button[data-vButtonType="henrysBucket"]').off('click').click(function() {
    processVariantForm(isNew, id);
  });

  $('#variantSubmitButtonsNew').hide();
  $('#variantSubmitButtonsEdit').show();

  $('#variantForm').show();
}

function closeVariantForm() {
  $('#variantForm').hide();

// reset fields
  $('#availableVariantsList').find(':checkbox:checked, :radio:checked').attr('checked', false);
  $('#availableVariantsList').find(':checkbox[id^=vg]').attr('disabled', true);
  $('#variants_price_tax_class,#variants_price, #variants_price_gross,#variants_model,#variants_quantity,#variants_weight,#variants_weight_class').val('');
  $('#variants_price_tax_class').removeAttr('selected');
  $('#variants_weight_class option[value="' + defaultWeightClass + '"]').attr('selected', true);
  $('input[name="variants_status"]').removeAttr('checked');
  $('#vstatus').buttonset('refresh');
  $('#variants_default').removeAttr('checked disabled');

  $('#variantListing').show();

  $('#formButtons').animate({'opacity': '1'}, 'fast').children().removeAttr('disabled');
  $('#sectionMenuContainer').animate({'opacity': '1'}, 'fast').children().removeAttr('disabled');
}

function processVariantForm(isNew, id) {
  if ( typeof isNew == 'undefined' ) {
    isNew = true;
  }

  var key, index;

  if ( isNew === true ) {
    key = 'new';
    index = ( typeof id == 'undefined' ) ? newVariantCounter : id;

    if ( typeof v['new'] == 'undefined' ) {
      v['new'] = { };
    }
  } else {
    key = 'db';
    index = id;
  }

  v[key][index] = { 'combos': [ ],
                    'default': ($('input[name="variants_default"]:checked').length > 0),
                    'tax_class_id': parseInt($('#variants_price_tax_class').val()),
                    'price': $('#variants_price').val(),
                    'model': $('#variants_model').val(),
                    'quantity': parseInt($('#variants_quantity').val()),
                    'weight': $('#variants_weight').val(),
                    'weight_class_id': parseInt($('#variants_weight_class').val()),
                    'status': parseInt($('input[name="variants_status"]:checked').val()) };

  var vname = '';

  $('#availableVariantsList').find(':checkbox[id^=vg]:checked').each(function() {
    var vcg = this;

    vname += variant_combos[$(this).val()]['title'] + ': ';

    $(this).parent().find(':checkbox[id^=vv]:checked, :radio[id^=vv]:checked').each(function() {
      vname += variant_combos[$(vcg).val()]['values'][$(this).val()]['title'] + ', ';

      v[key][index]['combos'].push( { 'group_id': parseInt($(vcg).val()), 'value_id': parseInt($(this).val()) } );
    });
  });

  vname = vname.substr(0, vname.length-2);

  if ( isNew === true ) {
    if ( $('#newVariant' + index).length > 0 ) {
      $('#productVariantsDataTable #newVariant' + index).html('<td>' + vname + '</td><td id="nvP' + index + '">' + $('#variants_price').val() + '</td><td>' + parseInt($('#variants_quantity').val()) + '</td><td align="right"><a href="#" onclick="openEditVariantForm(' + index + ', true); return false;">' + edit_icon + '</a> <a href="#" onclick="deleteNewVariant(' + index + '); return false;">' + trash_icon + '</a></td>');
    } else {
      $('#productVariantsDataTable > tbody:last').append('<tr id="newVariant' + index + '"><td>' + vname + '</td><td id="nvP' + index + '">' + $('#variants_price').val() + '</td><td>' + parseInt($('#variants_quantity').val()) + '</td><td align="right"><a href="#" onclick="openEditVariantForm(' + index + ', true); return false;">' + edit_icon + '</a> <a href="#" onclick="deleteNewVariant(' + index + '); return false;">' + trash_icon + '</a></td></tr>');
    }
  } else {
    $('#productVariantsDataTable #dbVariant' + index).html('<td>' + vname + '</td><td id="dbvP' + index + '">' + $('#variants_price').val() + '</td><td>' + parseInt($('#variants_quantity').val()) + '</td><td align="right"><span class="variantActions"><span class="defaultVariantActions"><a href="#" onclick="openEditVariantForm(' + index + '); return false;">' + edit_icon + '</a> <a href="#" onclick="deleteVariant(' + index + '); return false;">' + trash_icon + '</a></span></span></td>');
  }

  if ( v[key][index]['default'] === true ) {
    if ( typeof variant_default_new != 'undefined' ) {
      v[variant_default_new['key']][variant_default_new['index']]['default'] = false;
    } else if ( typeof variant_default_orig != 'undefined' ) {
      v['db'][variant_default_orig]['default'] = false;
    }

    variant_default_new = { 'key': key,
                            'index': index };

    if ( $('#defaultVariantMarker').length > 0 ) {
      $('#defaultVariantMarker').remove();
    }

    if ( isNew === true ) {
      $('#newVariant' + variant_default_new['index'] + ' td:first').append(defaultVariantMarker);
    } else {
      $('#dbVariant' + variant_default_new['index'] + ' td:first').append(defaultVariantMarker);
    }
  }

  $.getJSON('<?php echo OSCOM::getRPCLink(null, null, 'FormatCurrency'); ?>', { value: $('#variants_price').val() }, function (response) {
    if ( response.rpcStatus == 1 ) {
      if ( isNew === true ) {
        $('#nvP' + index).html(response.value);
      } else {
        $('#dbvP' + index).html(response.value);
      }
    }
  });

  prettifyDataTable();

  if ( (isNew === true) && (typeof id == 'undefined') ) {
    newVariantCounter++;
  }

  $('#assigned_variants').val($.toJSON(v));

// Manually register a change in the jQuery safetynet plugin
  $.safetynet.raiseChange('variantEntry_' + key + '_' + index);

  closeVariantForm();
}

function deleteVariant(id) {
  var vcount = Object.keys(v['db']).length;

  if ( typeof v['new'] != 'undefined' ) {
    vcount += Object.keys(v['new']).length;
  }

  if ( (v['db'][id]['default'] === true) && (vcount > 1) ) {
    $('#dialogDeleteDefaultVariant').dialog('open');
  } else {
    $('#dialogDeleteVariant').data('id', id).dialog('open');
  }
}

function deleteNewVariant(id) {
  if ( (v['new'][id]['default'] === true) && Object.keys(v['new']).length > 1 && ((variant_default_orig in delete_variant_combos) ||  (typeof variant_default_orig == 'undefined')) ) {
    $('#dialogDeleteDefaultVariant').dialog('open');
  } else {
    $('#dialogDeleteNewVariant').data('id', id).dialog('open');
  }
}

function vgClearChildren(gid) {
  if ( $('#vg' + gid).is(':checked') == false ) {
    $('#vg' + gid).attr('disabled', true);
    $('#vg' + gid).parent().find(':checkbox, :radio').attr('checked', false);
  }
}

function vgEnable(gid) {
  if ( $('#vg' + gid).attr('disabled') ) {
    $('#vg' + gid).attr({disabled: false, checked: true});
  } else {
    if ( ($('#vg' + gid).parent().find(':checkbox:checked, :radio:checked').size() - 1) < 1 ) {
      $('#vg' + gid).attr({disabled: true, checked: false});
    }
  }
}

function undoDeleteVariant(id) {
  v['db'][id] = delete_variant_combos[id];
  delete delete_variant_combos[id];

  $('#assigned_variants').val($.toJSON(v));

  $('#dbVariant' + id + ' .variantActions .undoVariantActions').remove();
  $('#dbVariant' + id + ' .variantActions .defaultVariantActions').show();
  $('#dbVariant' + id).removeClass('deactivatedRow');

// Manually clear a change in the jQuery safetynet plugin
  $.safetynet.clearChange('variantDelete_' + id);
}

$(function() {
  $('#dialogDeleteVariant').dialog({
    autoOpen: false,
    resizable: false,
    modal: true,
    buttons: {
      '<?php echo addslashes(OSCOM::getDef('button_delete')); ?>': function() {
        $(this).dialog('close');

        delete_variant_combos[parseInt($(this).data('id'))] = v['db'][parseInt($(this).data('id'))];

        if ( delete v['db'][parseInt($(this).data('id'))] === true ) {
          $('#assigned_variants').val($.toJSON(v));
          $('#deleted_variants').val($.toJSON(delete_variant_combos));

          $('#dbVariant' + parseInt($(this).data('id'))).addClass('deactivatedRow');
          $('#dbVariant' + parseInt($(this).data('id')) + ' .variantActions .defaultVariantActions').hide();
          $('#dbVariant' + parseInt($(this).data('id')) + ' .variantActions').append('<span class="undoVariantActions"><a href="#" onclick="undoDeleteVariant(' + parseInt($(this).data('id')) + '); return false;">' + undo_icon + '</a></span>');

// Manually register a change in the jQuery safetynet plugin
          $.safetynet.raiseChange('variantDelete_' + parseInt($(this).data('id')));
        } else {
          delete delete_variant_combos[parseInt($(this).data('id'))];
        }
      },
      '<?php echo addslashes(OSCOM::getDef('button_cancel')); ?>': function() {
        $(this).dialog('close');
      }
    }
  });

  $('#dialogDeleteNewVariant').dialog({
    autoOpen: false,
    resizable: false,
    modal: true,
    buttons: {
      '<?php echo addslashes(OSCOM::getDef('button_delete')); ?>': function() {
        $(this).dialog('close');

        if ( delete v['new'][parseInt($(this).data('id'))] === true ) {
          $('#assigned_variants').val($.toJSON(v));

          $('#newVariant' + parseInt($(this).data('id'))).remove();

// Manually clear a change in the jQuery safetynet plugin
          $.safetynet.clearChange('variantEntry_new_' + parseInt($(this).data('id')));

          if ( (variant_default_new['key'] == 'new') && (variant_default_new['index'] == parseInt($(this).data('id'))) ) {
            variant_default_new = undefined;

            if ( typeof variant_default_orig != 'undefined' ) {
              v['db'][variant_default_orig]['default'] = true;

              $('#dbVariant' + variant_default_orig + ' td:first').append(defaultVariantMarker);
            }
          }

          prettifyDataTable();
        }
      },
      '<?php echo addslashes(OSCOM::getDef('button_cancel')); ?>': function() {
        $(this).dialog('close');
      }
    }
  });

  $('#dialogDeleteDefaultVariant').dialog({
    autoOpen: false,
    resizable: false,
    modal: true,
    buttons: {
      '<?php echo addslashes(OSCOM::getDef('button_ok')); ?>': function() {
        $(this).dialog('close');
      }
    }
  });
});
</script>
