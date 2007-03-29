<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  if ( isset($_GET['pID']) ) {
    $osC_ObjectInfo = new osC_ObjectInfo(osC_Products_Admin::getData($_GET['pID']));

    $Qpd = $osC_Database->query('select products_name, products_description, products_model, products_keyword, products_tags, products_url, language_id from :table_products_description where products_id = :products_id');
    $Qpd->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qpd->bindInt(':products_id', $_GET['pID']);
    $Qpd->execute();

    $products_name = array();
    $products_description = array();
    $products_model = array();
    $products_keyword = array();
    $products_tags = array();
    $products_url = array();

    while ($Qpd->next()) {
      $products_name[$Qpd->valueInt('language_id')] = $Qpd->value('products_name');
      $products_description[$Qpd->valueInt('language_id')] = $Qpd->value('products_description');
      $products_model[$Qpd->valueInt('language_id')] = $Qpd->value('products_model');
      $products_keyword[$Qpd->valueInt('language_id')] = $Qpd->value('products_keyword');
      $products_tags[$Qpd->valueInt('language_id')] = $Qpd->value('products_tags');
      $products_url[$Qpd->valueInt('language_id')] = $Qpd->value('products_url');
    }
  }

  $Qmanufacturers = $osC_Database->query('select manufacturers_id, manufacturers_name from :table_manufacturers order by manufacturers_name');
  $Qmanufacturers->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
  $Qmanufacturers->execute();

  $manufacturers_array = array(array('id' => '',
                                     'text' => $osC_Language->get('none')));

  while ($Qmanufacturers->next()) {
    $manufacturers_array[] = array('id' => $Qmanufacturers->valueInt('manufacturers_id'),
                                   'text' => $Qmanufacturers->value('manufacturers_name'));
  }

  $Qtc = $osC_Database->query('select tax_class_id, tax_class_title from :table_tax_class order by tax_class_title');
  $Qtc->bindTable(':table_tax_class', TABLE_TAX_CLASS);
  $Qtc->execute();

  $tax_class_array = array(array('id' => '0',
                                 'text' => $osC_Language->get('none')));

  while ($Qtc->next()) {
    $tax_class_array[] = array('id' => $Qtc->valueInt('tax_class_id'),
                               'text' => $Qtc->value('tax_class_title'));
  }

  $Qwc = $osC_Database->query('select weight_class_id, weight_class_title from :table_weight_class where language_id = :language_id order by weight_class_title');
  $Qwc->bindTable(':table_weight_class', TABLE_WEIGHT_CLASS);
  $Qwc->bindInt(':language_id', $osC_Language->getID());
  $Qwc->execute();

  $weight_class_array = array();

  while ($Qwc->next()) {
    $weight_class_array[] = array('id' => $Qwc->valueInt('weight_class_id'),
                                  'text' => $Qwc->value('weight_class_title'));
  }
?>

<script language="javascript" type="text/javascript" src="../ext/prototype/prototype.js"></script>
<script language="javascript" type="text/javascript" src="../ext/scriptaculous/scriptaculous.js"></script>

<style type="text/css">@import url('external/jscalendar/calendar-win2k-1.css');</style>
<script type="text/javascript" src="external/jscalendar/calendar.js"></script>
<script type="text/javascript" src="external/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="external/jscalendar/calendar-setup.js"></script>

<style type="text/css"><!--
.attributeRemove {
  background-color: #FFC6C6;
}

.attributeAdd {
  background-color: #E8FFC6;
}
//--></style>

<script type="text/javascript"><!--
  var tax_rates = new Array();

<?php
  foreach ($tax_class_array as $tc_entry) {
    if ( $tc_entry['id'] > 0 ) {
      echo '  tax_rates["' . $tc_entry['id'] . '"] = ' . $osC_Tax->getTaxRate($tc_entry['id']) . ';' . "\n";
    }
  }
?>

  function doRound(x, places) {
    return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
  }

  function getTaxRate() {
    var selected_value = document.forms["product"].products_tax_class_id.selectedIndex;
    var parameterVal = document.forms["product"].products_tax_class_id[selected_value].value;

    if ( (parameterVal > 0) && (tax_rates[parameterVal] > 0) ) {
      return tax_rates[parameterVal];
    } else {
      return 0;
    }
  }

  function updateGross(field) {
    var taxRate = getTaxRate();
    var grossValue = document.getElementById(field).value;

    if (taxRate > 0) {
      grossValue = grossValue * ((taxRate / 100) + 1);
    }

    document.getElementById(field + "_gross").value = doRound(grossValue, 4);
  }

  function updateNet(field) {
    var taxRate = getTaxRate();
    var netValue = document.getElementById(field + "_gross").value;

    if (taxRate > 0) {
      netValue = netValue / ((taxRate / 100) + 1);
    }

    document.getElementById(field).value = doRound(netValue, 4);
  }

  var counter = 0;

  function moreFields() {
    var existingFields = document.product.getElementsByTagName('input');
    var attributeExists = false;

    for (i=0; i<existingFields.length; i++) {
      if (existingFields[i].name == 'attribute_price[' + document.product.attributes.options[document.product.attributes.options.selectedIndex].parentNode.id + '][' + document.product.attributes.options[document.product.attributes.options.selectedIndex].value + ']') {
        attributeExists = true;
        break;
      }
    }

    if (attributeExists == false) {
      counter++;
      var newFields = document.getElementById('readroot').cloneNode(true);
      newFields.id = '';
      newFields.style.display = 'block';

      var spanFields = newFields.getElementsByTagName('span');
      var inputFields = newFields.getElementsByTagName('input');
      var selectFields = newFields.getElementsByTagName('select');

      spanFields[0].innerHTML = document.product.attributes.options[document.product.attributes.options.selectedIndex].parentNode.label;
      spanFields[1].innerHTML = document.product.attributes.options[document.product.attributes.options.selectedIndex].text;

      for (y=0; y<inputFields.length; y++) {
        if (inputFields[y].type != 'button') {
          inputFields[y].name = inputFields[y].name.substr(4) + '[' + document.product.attributes.options[document.product.attributes.options.selectedIndex].parentNode.id + '][' + document.product.attributes.options[document.product.attributes.options.selectedIndex].value + ']';
          inputFields[y].disabled = false;
        }
      }

      for (y=0; y<selectFields.length; y++) {
        selectFields[y].name = selectFields[y].name.substr(4) + '[' + document.product.attributes.options[document.product.attributes.options.selectedIndex].parentNode.id + '][' + document.product.attributes.options[document.product.attributes.options.selectedIndex].value + ']';
        selectFields[y].disabled = false;
      }

      var insertHere = document.getElementById('writeroot');
      insertHere.parentNode.insertBefore(newFields,insertHere);
    }
  }

  function toggleAttributeStatus(attributeID) {
    var row = document.getElementById(attributeID);
    var rowButton = document.getElementById(attributeID + '-button');
    var inputFields = row.getElementsByTagName('input');
    var selectFields = row.getElementsByTagName('select');

    if (rowButton.value == '-') {
      for (rF=0; rF<inputFields.length; rF++) {
        if (inputFields[rF].type != 'button') {
          inputFields[rF].disabled = true;
        }
      }

      for (rF=0; rF<selectFields.length; rF++) {
        selectFields[rF].disabled = true;
      }

      row.className = 'attributeRemove';
      rowButton.value = '+';
    } else {
      for (rF=0; rF<inputFields.length; rF++) {
        if (inputFields[rF].type != 'button') {
          inputFields[rF].disabled = false;
        }
      }

      for (rF=0; rF<selectFields.length; rF++) {
        selectFields[rF].disabled = false;
      }

      row.className = '';
      rowButton.value = '-';
    }
  }

<?php
  if ( isset($_GET['pID']) ) {
?>

  function handleHttpResponseRemoveImage(http) {
    var result = /\[\[([^|]*?)(?:\|([^|]*?)){0,1}\]\]/.exec(http.responseText);
    result.shift();

    if (result[0] == '1') {
      document.getElementById('image_' + result[1]).style.display = 'none';

      if (document.getElementById('image_' + result[1]).parentNode.id == 'imagesOriginal') {
        getImagesOthers();
      }
    }
  }

  function removeImage(id) {
    var objOverlay = document.getElementById('overlay');
    var objActionLayer = document.getElementById('actionLayer');

    var arrayPageSize = getPageSize();
    var arrayPageScroll = getPageScroll();

    objOverlay.style.height = (arrayPageSize[1] + 'px');
    objOverlay.style.display = 'block';

    objActionLayer.style.top = (arrayPageScroll[1] + ((arrayPageSize[3] - 35 - parseInt(objActionLayer.style.height)) / 2) + 'px');
    objActionLayer.style.left = (((arrayPageSize[0] - 20 - parseInt(objActionLayer.style.width)) / 2) + 'px');

    var s = new String(objActionLayer.innerHTML);
    s = s.replace(/removeImageConfirmation\(\'[a-zA-Z0-9_]*\'\)/, 'removeImageConfirmation(\'' + id + '\')');
    s = s.replace(/cancelRemoveImage\(\'[a-zA-Z0-9_]*\'\)/, 'cancelRemoveImage(\'' + id + '\')');

    objActionLayer.innerHTML = s;

    objActionLayer.style.display = 'block';
  }

  function removeImageConfirmation(id) {
    var image = id.split('_');

    document.getElementById('actionLayer').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';

    new Ajax.Request("rpc.php?action=deleteProductImage&image=" + image[1], {onSuccess: handleHttpResponseRemoveImage});
  }

  function cancelRemoveImage(id) {
    document.getElementById('actionLayer').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
    document.getElementById(id).style.backgroundColor = '#ffffff';
  }

  function handleHttpResponseSetDefaultImage(http) {
    var result = /\[\[([^|]*?)(?:\|([^|]*?)){0,1}\]\]/.exec(http.responseText);
    result.shift();

    if (result[0] == '1') {
      getImagesOriginals();
    }
  }

  function setDefaultImage(id) {
    var image = id.split('_');

    new Ajax.Request("rpc.php?action=setDefaultImage&image=" + image[1], {onSuccess: handleHttpResponseSetDefaultImage});
  }

  function handleHttpResponseReorderImages(http) {
    var result = /\[\[([^|]*?)(?:\|([^|]*?)){0,1}\]\]/.exec(http.responseText);
    result.shift();

    if (result[0] == '1') {
      getImagesOthers();
    }
  }

  function handleHttpResponseGetImages(http) {
    var result = /\[\[([^|]*?)(?:\|([^|]*?)){0,1}\]\]/.exec(http.responseText);
    result.shift();

    if (result[0] == '1') {
      var str_array = result[1].split('[x]');

      for (i = 0; i < str_array.length; ++i) {
        var str_ele = str_array[i].split('[-]');

        var style = 'width: <?php echo $osC_Image->getWidth('mini') + 20; ?>px; padding: 10px; float: left; text-align: center;';

        if (str_ele[1] == '1') { // original (products_images_groups_id)
          var onmouseover = 'this.style.backgroundColor=\'#EFEBDE\'; this.style.backgroundImage=\'url(<?php echo osc_href_link_admin('templates/' . $osC_Template->getCode() . '/images/icons/16x16/drag.png'); ?>)\'; this.style.backgroundRepeat=\'no-repeat\'; this.style.backgroundPosition=\'0 0\';';

          if (str_ele[6] == '1') { // default_flag
            style += ' background-color: #E5EFE5;';

            var onmouseout = 'this.style.backgroundColor=\'#E5EFE5\'; this.style.backgroundImage=\'none\';';
          } else {
            var onmouseout = 'this.style.backgroundColor=\'#FFFFFF\'; this.style.backgroundImage=\'none\';';
          }
        } else {
          var onmouseover = 'this.style.backgroundColor=\'#EFEBDE\';';
          var onmouseout = 'this.style.backgroundColor=\'#FFFFFF\';';
        }

        var newdiv = '<span id="image_' + str_ele[0] + '" style="' + style + '" onmouseover="' + onmouseover + '" onmouseout="' + onmouseout + '">';
        newdiv += '<a href="' + str_ele[4] + '" target="_blank"><img src="<?php echo DIR_WS_HTTP_CATALOG . 'images/products/mini/'; ?>' + str_ele[2] + '" border="0" height="<?php echo $osC_Image->getHeight('mini'); ?>" alt="' + str_ele[2] + '" title="' + str_ele[2] + '" style="max-width: <?php echo $osC_Image->getWidth('mini') + 20; ?>px;" /></a><br />' + str_ele[3] + '<br />' + str_ele[5] + ' bytes<br />';

        if (str_ele[1] == '1') {
          if (str_ele[6] == '1') {
            newdiv += '<?php echo osc_icon('default.png'); ?>&nbsp;';
          } else {
            newdiv += '<a href="#" onclick="setDefaultImage(\'image_' + str_ele[0] + '\');"><?php echo osc_icon('default_grey.png'); ?></a>&nbsp;';
          }

          newdiv += '<a href="#" onclick="removeImage(\'image_' + str_ele[0] + '\');"><?php echo osc_icon('trash.png'); ?></a>';
        }

        newdiv += '</span>';

        if (str_ele[1] == '1') {
          document.getElementById('imagesOriginal').innerHTML += newdiv;
        } else {
          document.getElementById('imagesOther').innerHTML += newdiv;
        }
      }

      Sortable.create('imagesOriginal', {tag: 'span', overlap: 'horizontal', constraint: false, onUpdate: function() {
        new Ajax.Request("rpc.php?action=reorderImages&pID=<?php echo urlencode($_GET['pID']); ?>&" + Sortable.serialize('imagesOriginal'), {onSuccess: handleHttpResponseReorderImages});
      }});
    }

    if (document.getElementById('showProgressOriginal').style.display != 'none') {
      document.getElementById('showProgressOriginal').style.display = 'none';
    }

    if (document.getElementById('showProgressOther').style.display != 'none') {
      document.getElementById('showProgressOther').style.display = 'none';
    }
  }

  function getImages() {
    getImagesOriginals(false);
    getImagesOthers(false);

    new Ajax.Request("rpc.php?action=getImages&pID=<?php echo urlencode($_GET['pID']); ?>", {onSuccess: handleHttpResponseGetImages});
  }

  function getImagesOriginals(makeCall) {
    document.getElementById('imagesOriginal').innerHTML = '<div id="showProgressOriginal" style="float: left; padding-left: 10px;"><?php echo osc_icon('progress_ani.gif') . '&nbsp;' . $osC_Language->get('images_loading_from_server'); ?></div>';

    if (makeCall != false) {
      new Ajax.Request("rpc.php?action=getImages&pID=<?php echo urlencode($_GET['pID']); ?>&filter=originals", {onSuccess: handleHttpResponseGetImages});
    }
  }

  function getImagesOthers(makeCall) {
    document.getElementById('imagesOther').innerHTML = '<div id="showProgressOther" style="float: left; padding-left: 10px;"><?php echo osc_icon('progress_ani.gif') . '&nbsp;' . $osC_Language->get('images_loading_from_server'); ?></div>';

    if (makeCall != false) {
      new Ajax.Request("rpc.php?action=getImages&pID=<?php echo urlencode($_GET['pID']); ?>&filter=others", {onSuccess: handleHttpResponseGetImages});
    }
  }

<?php
  }
?>

  function handleHttpResponseGetLocalImages(http) {
    var result = /\[\[([^|]*?)(?:\|([^|]*?)){0,1}\]\]/.exec(http.responseText);
    result.shift();

    if (result[0] == '1') {
      var i = 0;

      var selectList = document.getElementById('localImagesSelection');

      for (i = selectList.options.length; i >= 0; i--) {
        selectList.options[i] = null;
      }

      if (result[1].length > 0) {
        var entries = result[1].split('#');

        for (i = 0; i < entries.length; i++) {
          selectList.options[i] = new Option(entries[i]);
          selectList.options[i].selected = false;
        }
      }
    }

    document.getElementById('showProgressGetLocalImages').style.display = 'none';
  }

  function getLocalImages() {
    document.getElementById('showProgressGetLocalImages').style.display = 'inline';

    new Ajax.Request("rpc.php?action=getLocalImages", {onSuccess: handleHttpResponseGetLocalImages});
  }

  function setFileUploadField() {
    document.getElementById('fileUploadField').innerHTML = '<?php echo osc_draw_file_field('products_image', true); ?>';
  }

  function switchImageFilesView(layer) {
    if (layer == 'local') {
      var layer1 = document.getElementById('remoteFiles');
      var layer1link = document.getElementById('remoteFilesLink');
      var layer2 = document.getElementById('localFiles');
      var layer2link = document.getElementById('localFilesLink');
    } else {
      var layer1 = document.getElementById('localFiles');
      var layer1link = document.getElementById('localFilesLink');
      var layer2 = document.getElementById('remoteFiles');
      var layer2link = document.getElementById('remoteFilesLink');
    }

    if ( (layer != 'local') || ((layer == 'local') && (layer1.style.display != 'none')) ) {
      layer1.style.display='none';
      layer2.style.display='inline';
      layer1link.style.backgroundColor='';
      layer2link.style.backgroundColor='#E5EFE5';
    } else {
      getLocalImages();
    }
  }
//--></script>

<style type="text/css"><!--
#overlay img {
  border: none;
}

#overlay {
  background-image: url(<?php echo osc_href_link_admin('templates/' . $osC_Template->getCode() . '/images/overlay.png'); ?>);
}

* html #overlay {
  background-color: #000;
  back\ground-color: transparent;
  background-image: url(<?php echo osc_href_link_admin('templates/' . $osC_Template->getCode() . '/images/overlay.png'); ?>);
  filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="<?php echo osc_href_link_admin('templates/' . $osC_Template->getCode() . '/images/overlay.png'); ?>", sizingMethod="scale");
  }
//--></style>

<link type="text/css" rel="stylesheet" href="external/tabpane/css/luna/tab.css" />
<script type="text/javascript" src="external/tabpane/js/tabpane.js"></script>

<div id="overlay" style="display: none; position: absolute; top: 0; left: 0; z-index: 90; width: 100%;"></div>

<div id="actionLayer" style="display: none; position: absolute; z-index: 100; width: 400px; height: 200px;">
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_delete_image'); ?></div>
  <div class="infoBoxContent">
    <p><?php echo $osC_Language->get('introduction_delete_image'); ?></p>

    <p align="center"><?php echo '<button onclick="removeImageConfirmation(\'\')" class="operationButton">' . $osC_Language->get('button_delete') . '</button> <button onclick="cancelRemoveImage(\'\')" class="operationButton">' . $osC_Language->get('button_cancel') . '</button>'; ?></p>
  </div>
</div>

<h1><?php echo (isset($osC_ObjectInfo) && isset($products_name[$osC_Language->getID()])) ? $products_name[$osC_Language->getID()] : $osC_Language->get('heading_title_new_product'); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="tab-pane" id="mainTabPane">
  <script type="text/javascript"><!--
    var mainTabPane = new WebFXTabPane( document.getElementById( "mainTabPane" ) );
  //--></script>

  <form name="product" action="#" method="post" enctype="multipart/form-data">

  <div class="tab-page" id="tabDescription">
    <h2 class="tab"><?php echo $osC_Language->get('section_general'); ?></h2>

    <script type="text/javascript"><!--
      mainTabPane.addTabPage( document.getElementById( "tabDescription" ) );
    //--></script>

    <div class="tab-pane" id="descriptionTabPane">
      <script type="text/javascript"><!--
        var descriptionTabPane = new WebFXTabPane( document.getElementById( "descriptionTabPane" ) );
      //--></script>

<?php
  foreach ($osC_Language->getAll() as $l) {
?>

      <div class="tab-page" id="tabDescriptionLanguages_<?php echo $l['code']; ?>">
        <h2 class="tab"><?php echo $osC_Language->showImage($l['code']) . '&nbsp;' . $l['name']; ?></h2>

        <script type="text/javascript"><!--
          descriptionTabPane.addTabPage( document.getElementById( "tabDescriptionLanguages_<?php echo $l['code']; ?>" ) );
        //--></script>

        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td><?php echo $osC_Language->get('field_name'); ?></td>
            <td><?php echo osc_draw_input_field('products_name[' . $l['id'] . ']', (isset($osC_ObjectInfo) && isset($products_name[$l['id']]) ? $products_name[$l['id']] : null)); ?></td>
          </tr>
          <tr>
            <td valign="top"><?php echo $osC_Language->get('field_description'); ?></td>
            <td><?php echo osc_draw_textarea_field('products_description[' . $l['id'] . ']', (isset($osC_ObjectInfo) && isset($products_description[$l['id']]) ? $products_description[$l['id']] : null), 70, 15, 'style="width: 100%;"'); ?></td>
          </tr>
          <tr>
            <td><?php echo $osC_Language->get('field_model'); ?></td>
            <td><?php echo osc_draw_input_field('products_model[' . $l['id'] . ']', (isset($osC_ObjectInfo) && isset($products_model[$l['id']]) ? $products_model[$l['id']] : null)); ?></td>
          </tr>
          <tr>
            <td><?php echo $osC_Language->get('field_keyword'); ?></td>
            <td><?php echo osc_draw_input_field('products_keyword[' . $l['id'] . ']', (isset($osC_ObjectInfo) && isset($products_keyword[$l['id']]) ? $products_keyword[$l['id']] : null)); ?></td>
          </tr>
          <tr>
            <td><?php echo $osC_Language->get('field_tags'); ?></td>
            <td><?php echo osc_draw_input_field('products_tags[' . $l['id'] . ']', (isset($osC_ObjectInfo) && isset($products_tags[$l['id']]) ? $products_tags[$l['id']] : null)); ?></td>
          </tr>
          <tr>
            <td><?php echo $osC_Language->get('field_url'); ?></td>
            <td><?php echo osc_draw_input_field('products_url[' . $l['id'] . ']', (isset($osC_ObjectInfo) && isset($products_url[$l['id']]) ? $products_url[$l['id']] : null)); ?></td>
          </tr>
        </table>
      </div>

<?php
  }
?>

    </div>
  </div>

  <div class="tab-page" id="tabData">
    <h2 class="tab"><?php echo $osC_Language->get('section_data'); ?></h2>

    <script type="text/javascript"><!--
      mainTabPane.addTabPage( document.getElementById( "tabData" ) );
    //--></script>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="50%" height="100%" valign="top">
          <fieldset style="height: 100%;">
            <legend><?php echo $osC_Language->get('subsection_price'); ?></legend>

            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td><?php echo $osC_Language->get('field_tax_class'); ?></td>
                <td><?php echo osc_draw_pull_down_menu('products_tax_class_id', $tax_class_array, (isset($osC_ObjectInfo) ? $osC_ObjectInfo->get('products_tax_class_id') : null), 'onchange="updateGross(\'products_price\');"'); ?></td>
              </tr>
              <tr>
                <td><?php echo $osC_Language->get('field_price_net'); ?></td>
                <td><?php echo osc_draw_input_field('products_price', (isset($osC_ObjectInfo) ? $osC_ObjectInfo->get('products_price') : null), 'onkeyup="updateGross(\'products_price\')"'); ?></td>
              </tr>
              <tr>
                <td><?php echo $osC_Language->get('field_price_gross'); ?></td>
                <td><?php echo osc_draw_input_field('products_price_gross', (isset($osC_ObjectInfo) ? $osC_ObjectInfo->get('products_price') : null), 'onkeyup="updateNet(\'products_price\')"'); ?></td>
              </tr>
            </table>

            <script type="text/javascript"><!--
              updateGross('products_price');
            //--></script>
          </fieldset>
        </td>
        <td width="50%" height="100%" valign="top">
          <fieldset style="height: 100%;">
            <legend><?php echo $osC_Language->get('subsection_data'); ?></legend>

            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td><?php echo $osC_Language->get('field_manufacturer'); ?></td>
                <td><?php echo osc_draw_pull_down_menu('manufacturers_id', $manufacturers_array, (isset($osC_ObjectInfo) ? $osC_ObjectInfo->get('manufacturers_id') : null)); ?></td>
              </tr>
              <tr>
                <td><?php echo $osC_Language->get('field_quantity'); ?></td>
                <td><?php echo osc_draw_input_field('products_quantity', (isset($osC_ObjectInfo) ? $osC_ObjectInfo->get('products_quantity') : null)); ?></td>
              </tr>
              <tr>
                <td><?php echo $osC_Language->get('field_weight'); ?></td>
                <td><?php echo osc_draw_input_field('products_weight', (isset($osC_ObjectInfo) ? $osC_ObjectInfo->get('products_weight') : null)). '&nbsp;' . osc_draw_pull_down_menu('products_weight_class', $weight_class_array, (isset($osC_ObjectInfo) ? $osC_ObjectInfo->get('products_weight_class') : SHIPPING_WEIGHT_UNIT)); ?></td>
              </tr>
            </table>
          </fieldset>
        </td>
      </tr>
      <tr>
        <td width="50%" height="100%" valign="top">
          <fieldset style="height: 100%;">
            <legend><?php echo $osC_Language->get('subsection_status'); ?></legend>

            <?php echo osc_draw_radio_field('products_status', array(array('id' => '1', 'text' => $osC_Language->get('status_enabled')), array('id' => '0', 'text' => $osC_Language->get('status_disabled'))), (isset($osC_ObjectInfo) ? $osC_ObjectInfo->get('products_status') : '0'), null, '<br />'); ?>
          </fieldset>
        </td>
        <td width="50%" height="100%" valign="top">
          <fieldset style="height: 100%;">
            <legend><?php echo $osC_Language->get('subsection_information'); ?></legend>

            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td><?php echo $osC_Language->get('field_date_available'); ?></td>
                <td><?php echo osc_draw_input_field('products_date_available', (isset($osC_ObjectInfo) ? $osC_ObjectInfo->get('products_date_available') : null)); ?><input type="button" value="..." id="calendarTrigger" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "products_date_available", ifFormat: "%Y-%m-%d", button: "calendarTrigger" } );</script><small>(YYYY-MM-DD)</small></td>
              </tr>
            </table>
          </fieldset>
        </td>
      </tr>
    </table>
  </div>

  <div class="tab-page" id="tabImages">
    <h2 class="tab"><?php echo $osC_Language->get('section_images'); ?></h2>

    <script type="text/javascript"><!--
      mainTabPane.addTabPage( document.getElementById( "tabImages" ) );
    //--></script>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%" height="100%" valign="top">
          <fieldset style="height: 100%;">
            <legend><?php echo $osC_Language->get('subsection_new_image'); ?></legend>

            <div style="float: right;">
              <a href="#" id="remoteFilesLink" onclick="switchImageFilesView('remote');" style="background-color: #E5EFE5;"><?php echo $osC_Language->get('image_remote_upload'); ?></a> | <a href="#" id="localFilesLink" onclick="switchImageFilesView('local');"><?php echo $osC_Language->get('image_local_files'); ?></a>
            </div>

            <div id="remoteFiles">
              <span id="fileUploadField"></span>

<?php
    if ( isset($osC_ObjectInfo) ) {
      echo '<input type="submit" value="' . $osC_Language->get('button_send_to_server') . '" class="operationButton" onclick="document.product.target=\'fileUploadFrame\'; document.product.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=fileUpload' . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '')) . '\'; document.getElementById(\'showProgress\').style.display=\'inline\';" /><div id="showProgress" style="display: none; padding-left: 10px;">' . osc_icon('progress_ani.gif') . '&nbsp;' . $osC_Language->get('image_upload_progress') . '</div>';
    }
?>
            </div>

<script type="text/javascript"><!--
  setFileUploadField();
//--></script>

            <div id="localFiles" style="display: none;">
              <p><?php echo $osC_Language->get('introduction_select_local_images'); ?></p>

              <select id="localImagesSelection" name="localimages[]" size="5" multiple="multiple" style="width: 100%;"></select>

              <div id="showProgressGetLocalImages" style="display: none; float: right; padding-right: 10px;"><?php echo osc_icon('progress_ani.gif') . '&nbsp;' . $osC_Language->get('image_retrieving_local_files'); ?></div>

              <p><?php echo realpath('../images/products/_upload'); ?></p>

<?php
    if ( isset($osC_ObjectInfo) ) {
      echo '<input type="submit" value="Assign To Product" class="operationButton" onclick="document.product.target=\'fileUploadFrame\'; document.product.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=assignLocalImages' . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '')) . '\'; document.getElementById(\'showProgressAssigningLocalImages\').style.display=\'inline\';" /><div id="showProgressAssigningLocalImages" style="display: none; padding-left: 10px;">' . osc_icon('progress_ani.gif') . '&nbsp;' . $osC_Language->get('image_multiple_upload_progress') . '</div>';
    }
?>

            </div>

            <iframe id="fileUploadFrame" name="fileUploadFrame" style="height: 0px; width: 0px; border: 0px"></iframe>
          </fieldset>

<script type="text/javascript"><!--
  getLocalImages();
//--></script>

<?php
    if ( isset($osC_ObjectInfo) ) {
?>

          <fieldset style="height: 100%;">
            <legend><?php echo $osC_Language->get('subsection_original_images'); ?></legend>

            <div id="imagesOriginal" style="overflow: auto;"></div>
          </fieldset>

          <fieldset style="height: 100%;">
            <legend><?php echo $osC_Language->get('subsection_images'); ?></legend>

            <div id="imagesOther" style="overflow: auto;"></div>
          </fieldset>

<script type="text/javascript"><!--
  getImages();
//--></script>

<?php
    }
?>

        </td>
      </tr>
    </table>
  </div>

  <div class="tab-page" id="tabAttributes">
    <h2 class="tab"><?php echo $osC_Language->get('section_attributes'); ?></h2>

<script type="text/javascript">mainTabPane.addTabPage( document.getElementById( "tabAttributes" ) );</script>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="30%" valign="top"><select name="attributes" size="20" style="width: 100%;">

<?php
  $Qoptions = $osC_Database->query('select products_options_id, products_options_name from :table_products_options where language_id = :language_id order by products_options_name');
  $Qoptions->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
  $Qoptions->bindInt(':language_id', $osC_Language->getID());
  $Qoptions->execute();

  while ($Qoptions->next()) {
    echo '          <optgroup label="' . $Qoptions->value('products_options_name') . '" id="' . $Qoptions->value('products_options_id') . '">' . "\n";

    $Qvalues = $osC_Database->query('select pov.products_options_values_id, pov.products_options_values_name from :table_products_options_values pov, :table_products_options_values_to_products_options pov2po where pov2po.products_options_id = :products_options_id and pov2po.products_options_values_id = pov.products_options_values_id and pov.language_id = :language_id order by pov.products_options_values_name');
    $Qvalues->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
    $Qvalues->bindTable(':table_products_options_values_to_products_options', TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);
    $Qvalues->bindInt(':products_options_id', $Qoptions->valueInt('products_options_id'));
    $Qvalues->bindInt(':language_id', $osC_Language->getID());
    $Qvalues->execute();

    while ($Qvalues->next()) {
      echo '            <option value="' . $Qvalues->valueInt('products_options_values_id') . '">' . $Qvalues->value('products_options_values_name') . '</option>' . "\n";
    }

    echo '          </optgroup>' . "\n";
  }
?>
        </select></td>
        <td align="center" width="10%">
          <input type="button" value=">>" onclick="moreFields()" class="infoBoxButton">
        </td>
        <td width="60%" valign="top"">
          <fieldset>
            <legend><?php echo $osC_Language->get('subsection_assigned_attributes'); ?></legend>

<?php
  if (isset($_GET['pID'])) {
    echo '<table border="0" width="100%" cellspacing="0" cellpadding="2">';

    $Qattributes = $osC_Database->query('select po.products_options_id, po.products_options_name, pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from :table_products_attributes pa, :table_products_options po, :table_products_options_values pov where pa.products_id = :products_id and pa.options_id = po.products_options_id and po.language_id = :language_id and pa.options_values_id = pov.products_options_values_id and pov.language_id = :language_id order by po.products_options_name, pov.products_options_values_name');
    $Qattributes->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
    $Qattributes->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
    $Qattributes->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
    $Qattributes->bindInt(':products_id', $_GET['pID']);
    $Qattributes->bindInt(':language_id', $osC_Language->getID());
    $Qattributes->bindInt(':language_id', $osC_Language->getID());
    $Qattributes->execute();

    $current_attribute_group = '';

    while ($Qattributes->next()) {
      if ($Qattributes->value('products_options_name') != $current_attribute_group) {
        echo '              <tr>' . "\n" .
             '                <td colspan="3"><b>' . $Qattributes->value('products_options_name') . '</b></td>' . "\n" .
             '              </tr>' . "\n";

        $current_attribute_group = $Qattributes->value('products_options_name');
      }

      echo '              <tr id="attribute-' . $Qattributes->valueInt('products_options_id') . '_' . $Qattributes->valueInt('products_options_values_id') . '">' . "\n" .
           '                <td width="50%">' . $Qattributes->value('products_options_values_name') . '</td>' . "\n" .
           '                <td>' . osc_draw_pull_down_menu('attribute_prefix[' . $Qattributes->valueInt('products_options_id') . '][' . $Qattributes->valueInt('products_options_values_id') . ']', array(array('id' => '+', 'text' => '+'), array('id' => '-', 'text' => '-')), $Qattributes->value('price_prefix')) . '&nbsp;' . osc_draw_input_field('attribute_price[' . $Qattributes->valueInt('products_options_id') . '][' . $Qattributes->valueInt('products_options_values_id') . ']', $Qattributes->value('options_values_price')) . '</td>' . "\n" .
           '                <td align="right"><input type="button" value="-" id="attribute-' . $Qattributes->valueInt('products_options_id') . '_' . $Qattributes->valueInt('products_options_values_id') . '-button" onclick="toggleAttributeStatus(\'attribute-' . $Qattributes->valueInt('products_options_id') . '_' . $Qattributes->valueInt('products_options_values_id') . '\');" class="infoBoxButton"></td>' . "\n" .
           '              </tr>' . "\n";
    }

    echo '</table>';
  }
?>

            <span id="writeroot"></span>

            <div id="readroot" style="display: none">
              <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                  <td colspan="3"><b><span id="attribteGroupName">&nbsp;</span></b></td>
                </tr>
                <tr class="attributeAdd">
                  <td width="50%"><span id="attributeKey">&nbsp;</span></td>
                  <td><?php echo osc_draw_pull_down_menu('new_attribute_prefix', array(array('id' => '+', 'text' => '+'), array('id' => '-', 'text' => '-')), '+', 'disabled="disabled"') . '&nbsp;' . osc_draw_input_field('new_attribute_price', null, 'disabled="disabled"'); ?></td>
                  <td align="right"><input type="button" value="-" onclick="this.parentNode.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode.parentNode);" class="infoBoxButton"></td>
                </tr>
              </table>
            </div>
          </fieldset>
        </td>
      </tr>
    </table>
  </div>

  <div class="tab-page" id="tabCategories">
    <h2 class="tab"><?php echo $osC_Language->get('section_categories'); ?></h2>

<script type="text/javascript">mainTabPane.addTabPage( document.getElementById( "tabCategories" ) );</script>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
          <thead>
            <tr>
              <th width="20">&nbsp;</th>
              <th><?php echo $osC_Language->get('table_heading_categories'); ?></th>
            </tr>
          </thead>
          <tbody>
<?php
  $product_categories_array = array();

  if (isset($_GET['pID'])) {
    $Qcategories = $osC_Database->query('select categories_id from :table_products_to_categories where products_id = :products_id');
    $Qcategories->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
    $Qcategories->bindInt(':products_id', $_GET['pID']);
    $Qcategories->execute();

    while ($Qcategories->next()) {
      $product_categories_array[] = $Qcategories->valueInt('categories_id');
    }
  }

  $assignedCategoryTree = new osC_CategoryTree();
  $assignedCategoryTree->setBreadcrumbUsage(false);
  $assignedCategoryTree->setSpacerString('&nbsp;', 5);

  foreach ($assignedCategoryTree->getTree() as $value) {
    echo '          <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' . "\n" .
         '            <td>' . osc_draw_checkbox_field('categories[]', $value['id'], in_array($value['id'], $product_categories_array), 'id="categories_' . $value['id'] . '"') . '</td>' . "\n" .
         '            <td><a href="#" onclick="document.product.categories_' . $value['id'] . '.checked=!document.product.categories_' . $value['id'] . '.checked;">' . $value['title'] . '</a></td>' . "\n" .
         '          </tr>' . "\n";
  }
?>
        </table></td>
      </tr>
    </table>
  </div>

  <p align="right"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" onclick="' . (isset($osC_ObjectInfo) ? 'setFileUploadField(); ' : '') . 'document.product.target=\'_self\'; document.product.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '') . '&action=save') . '\';" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '')) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
