<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  if (isset($_GET['pID'])) {
    $Qp = $osC_Database->query('select products_id, products_quantity, products_price, products_weight, products_weight_class, products_date_added, products_last_modified, date_format(products_date_available, "%Y-%m-%d") as products_date_available, products_status, products_tax_class_id, manufacturers_id from :table_products where products_id = :products_id');
    $Qp->bindTable(':table_products', TABLE_PRODUCTS);
    $Qp->bindInt(':products_id', $_GET['pID']);
    $Qp->execute();

    $Qpd = $osC_Database->query('select products_name, products_description, products_model, products_keyword, products_tags, products_url, language_id from :table_products_description where products_id = :products_id');
    $Qpd->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qpd->bindInt(':products_id', $_GET['pID']);
    $Qpd->execute();

    $pd_extra = array();
    while ($Qpd->next()) {
      $pd_extra['products_name'][$Qpd->valueInt('language_id')] = $Qpd->value('products_name');
      $pd_extra['products_description'][$Qpd->valueInt('language_id')] = $Qpd->value('products_description');
      $pd_extra['products_model'][$Qpd->valueInt('language_id')] = $Qpd->value('products_model');
      $pd_extra['products_keyword'][$Qpd->valueInt('language_id')] = $Qpd->value('products_keyword');
      $pd_extra['products_tags'][$Qpd->valueInt('language_id')] = $Qpd->value('products_tags');
      $pd_extra['products_url'][$Qpd->valueInt('language_id')] = $Qpd->value('products_url');
    }

    $pInfo = new objectInfo(array_merge($Qp->toArray(), $pd_extra));
  }

  $Qmanufacturers = $osC_Database->query('select manufacturers_id, manufacturers_name from :table_manufacturers order by manufacturers_name');
  $Qmanufacturers->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
  $Qmanufacturers->execute();

  $manufacturers_array = array(array('id' => '', 'text' => TEXT_NONE));
  while ($Qmanufacturers->next()) {
    $manufacturers_array[] = array('id' => $Qmanufacturers->valueInt('manufacturers_id'),
                                   'text' => $Qmanufacturers->value('manufacturers_name'));
  }

  $Qtc = $osC_Database->query('select tax_class_id, tax_class_title from :table_tax_class order by tax_class_title');
  $Qtc->bindTable(':table_tax_class', TABLE_TAX_CLASS);
  $Qtc->execute();

  $tax_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
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

<style type="text/css">
.attributeRemove {
  background-color: #FFC6C6;
}

.attributeAdd {
  background-color: #E8FFC6;
}
</style>

<script type="text/javascript"><!--
  var tax_rates = new Array();
<?php
  foreach ($tax_class_array as $tc_entry) {
    if ($tc_entry['id'] > 0) {
      echo '  tax_rates["' . $tc_entry['id'] . '"] = ' . tep_get_tax_rate_value($tc_entry['id']) . ';' . "\n";
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

  function handleHttpResponseRemoveImage(http) {
    var result = http.responseText.split(':osCRPC:', 2);

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
    var result = http.responseText.split(':osCRPC:', 2);

    if (result[0] == '1') {
      getImagesOriginals();
    }
  }

  function setDefaultImage(id) {
    var image = id.split('_');

    new Ajax.Request("rpc.php?action=setDefaultImage&image=" + image[1], {onSuccess: handleHttpResponseSetDefaultImage});
  }

  function handleHttpResponseReorderImages(http) {
    var result = http.responseText.split(':osCRPC:', 2);

    if (result[0] == '1') {
      getImagesOthers();
    }
  }

  function handleHttpResponseGetImages(http) {
    var result = http.responseText.split(':osCRPC:', 2);

    if (result[0] == '1') {
      var str_array = result[1].split('[x]');

      for (i = 0; i < str_array.length; ++i) {
        var str_ele = str_array[i].split('[-]');

        var style = 'width: <?php echo $osC_Image->getWidth($osC_Image->getCode(DEFAULT_IMAGE_GROUP_ID)) + 20; ?>px; padding: 10px; float: left; text-align: center;';

        if (str_ele[1] == '1') { // original (products_images_groups_id)
          var onmouseover = 'this.style.backgroundColor=\'#EFEBDE\'; this.style.backgroundImage=\'url(<?php echo tep_href_link('templates/' . $template . '/images/icons/16x16/drag.png', '', 'SSL'); ?>)\'; this.style.backgroundRepeat=\'no-repeat\'; this.style.backgroundPosition=\'0 0\';';

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
        newdiv += '<a href="' + str_ele[4] + '" target="_blank"><img src="<?php echo DIR_WS_HTTP_CATALOG . 'images/products/' . $osC_Image->getCode(DEFAULT_IMAGE_GROUP_ID) . '/'; ?>' + str_ele[2] + '" border="0" height="<?php echo $osC_Image->getHeight($osC_Image->getCode(DEFAULT_IMAGE_GROUP_ID)); ?>" alt="' + str_ele[2] + '" title="' + str_ele[2] + '" style="max-width: <?php echo $osC_Image->getWidth($osC_Image->getCode(DEFAULT_IMAGE_GROUP_ID)) + 20; ?>px;" /></a><br />' + str_ele[3] + '<br />' + str_ele[5] + ' bytes<br />';

        if (str_ele[1] == '1') {
          if (str_ele[6] == '1') {
            newdiv += '<?php echo tep_image('templates/' . $template . '/images/icons/16x16/default.png', IMAGE_DEFAULT, '16', '16'); ?>&nbsp;';
          } else {
            newdiv += '<a href="#" onclick="setDefaultImage(\'image_' + str_ele[0] + '\');"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/default_grey.png', IMAGE_DEFAULT, '16', '16'); ?></a>&nbsp;';
          }

          newdiv += '<a href="#" onclick="removeImage(\'image_' + str_ele[0] + '\');"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16'); ?></a>';
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
    document.getElementById('imagesOriginal').innerHTML = '<div id="showProgressOriginal" style="float: left; padding-left: 10px;"><?php echo tep_image('templates/default/images/icons/16x16/progress_ani.gif', '', '16', '16') . '&nbsp;Loading images from server...'; ?></div>';

    if (makeCall != false) {
      new Ajax.Request("rpc.php?action=getImages&pID=<?php echo urlencode($_GET['pID']); ?>&filter=originals", {onSuccess: handleHttpResponseGetImages});
    }
  }

  function getImagesOthers(makeCall) {
    document.getElementById('imagesOther').innerHTML = '<div id="showProgressOther" style="float: left; padding-left: 10px;"><?php echo tep_image('templates/default/images/icons/16x16/progress_ani.gif', '', '16', '16') . '&nbsp;Loading images from server...'; ?></div>';

    if (makeCall != false) {
      new Ajax.Request("rpc.php?action=getImages&pID=<?php echo urlencode($_GET['pID']); ?>&filter=others", {onSuccess: handleHttpResponseGetImages});
    }
  }
//--></script>

<style type="text/css"><!--
#overlay img {
  border: none;
}

#overlay {
  background-image: url(<?php echo tep_href_link('templates/' . $template . '/images/overlay.png', '', 'SSL'); ?>);
}

* html #overlay {
  background-color: #000;
  back\ground-color: transparent;
  background-image: url(<?php echo tep_href_link('templates/' . $template . '/images/overlay.png', '', 'SSL'); ?>);
  filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="<?php echo tep_href_link('templates/' . $template . '/images/overlay.png', '', 'SSL'); ?>", sizingMethod="scale");
  }
//--></style>

<link type="text/css" rel="stylesheet" href="external/tabpane/css/luna/tab.css" />
<script type="text/javascript" src="external/tabpane/js/tabpane.js"></script>

<div id="overlay" style="display: none; position: absolute; top: 0; left: 0; z-index: 90; width: 100%;"></div>

<div id="actionLayer" style="display: none; position: absolute; z-index: 100; width: 400px; height: 200px;">
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' Delete Image'; ?></div>
  <div class="infoBoxContent">
    <p>Are you sure you want to delete this product image?</p>

    <p align="center"><?php echo '<button onclick="removeImageConfirmation(\'\')" class="operationButton">' . IMAGE_DELETE . '</button> <button onclick="cancelRemoveImage(\'\')" class="operationButton">' . IMAGE_CANCEL . '</button>'; ?></p>
  </div>
</div>

<h1><?php echo (isset($pInfo->products_name[$osC_Language->getID()])) ? $pInfo->products_name[$osC_Language->getID()] : TEXT_NEW_PRODUCT; ?></h1>

<div class="tab-pane" id="mainTabPane">
  <script type="text/javascript"><!--
    var mainTabPane = new WebFXTabPane( document.getElementById( "mainTabPane" ) );
  //--></script>

<?php
  echo tep_draw_form('product', FILENAME_PRODUCTS, 'cPath=' . $cPath . '&search=' . $_GET['search'] . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '') . '&action=save_product', 'post', 'enctype="multipart/form-data"');
?>

  <div class="tab-page" id="tabDescription">
    <h2 class="tab"><?php echo TAB_GENERAL; ?></h2>

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
        <h2 class="tab"><?php echo tep_image('../includes/languages/' . $l['code'] . '/images/' . $l['image'], $l['name']) . '&nbsp;' . $l['name']; ?></h2>

        <script type="text/javascript"><!--
          descriptionTabPane.addTabPage( document.getElementById( "tabDescriptionLanguages_<?php echo $l['code']; ?>" ) );
        //--></script>

        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText"><?php echo TEXT_PRODUCTS_NAME; ?></td>
            <td class="smallText"><?php echo osc_draw_input_field('products_name[' . $l['id'] . ']', (isset($pInfo) && is_array($pInfo->products_name) && isset($pInfo->products_name[$l['id']]) ? $pInfo->products_name[$l['id']] : '')); ?></td>
          </tr>
          <tr>
            <td class="smallText" valign="top"><?php echo TEXT_PRODUCTS_DESCRIPTION; ?></td>
            <td class="smallText"><?php echo tep_draw_textarea_field('products_description[' . $l['id'] . ']', 'soft', '70', '15', (isset($pInfo) && is_array($pInfo->products_description) && isset($pInfo->products_description[$l['id']]) ? $pInfo->products_description[$l['id']] : ''), 'id="fckpd_' . $l['code'] . '" style="width: 100%;"'); ?></td>
          </tr>
          <tr>
            <td class="smallText"><?php echo TEXT_PRODUCTS_MODEL; ?></td>
            <td class="smallText"><?php echo osc_draw_input_field('products_model[' . $l['id'] . ']', (isset($pInfo) && is_array($pInfo->products_model) && isset($pInfo->products_model[$l['id']]) ? $pInfo->products_model[$l['id']] : '')); ?></td>
          </tr>
          <tr>
            <td class="smallText"><?php echo TEXT_PRODUCTS_KEYWORD; ?></td>
            <td class="smallText"><?php echo osc_draw_input_field('products_keyword[' . $l['id'] . ']', (isset($pInfo) && is_array($pInfo->products_keyword) && isset($pInfo->products_keyword[$l['id']]) ? $pInfo->products_keyword[$l['id']] : '')); ?></td>
          </tr>
          <tr>
            <td class="smallText"><?php echo TEXT_PRODUCTS_TAGS; ?></td>
            <td class="smallText"><?php echo osc_draw_input_field('products_tags[' . $l['id'] . ']', (isset($pInfo) && is_array($pInfo->products_tags) && isset($pInfo->products_tags[$l['id']]) ? $pInfo->products_tags[$l['id']] : '')); ?></td>
          </tr>
          <tr>
            <td class="smallText"><?php echo TEXT_PRODUCTS_URL; ?></td>
            <td class="smallText"><?php echo osc_draw_input_field('products_url[' . $l['id'] . ']', (isset($pInfo) && is_array($pInfo->products_url) && isset($pInfo->products_url[$l['id']]) ? $pInfo->products_url[$l['id']] : '')); ?></td>
          </tr>
        </table>
      </div>

<?php
  }
?>

    </div>
  </div>

  <div class="tab-page" id="tabData">
    <h2 class="tab"><?php echo TAB_DATA; ?></h2>

    <script type="text/javascript"><!--
      mainTabPane.addTabPage( document.getElementById( "tabData" ) );
    //--></script>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="50%" height="100%" valign="top">
          <fieldset style="height: 100%;">
            <legend>Price</legend>

            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="smallText"><?php echo TEXT_PRODUCTS_TAX_CLASS; ?></td>
                <td class="smallText"><?php echo osc_draw_pull_down_menu('products_tax_class_id', $tax_class_array, (isset($pInfo) ? $pInfo->products_tax_class_id : ''), 'onchange="updateGross(\'products_price\');"'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><?php echo TEXT_PRODUCTS_PRICE_NET; ?></td>
                <td class="smallText"><?php echo osc_draw_input_field('products_price', (isset($pInfo) ? $pInfo->products_price : ''), 'id="products_price" onkeyup="updateGross(\'products_price\')"'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><?php echo TEXT_PRODUCTS_PRICE_GROSS; ?></td>
                <td class="smallText"><?php echo osc_draw_input_field('products_price_gross', (isset($pInfo) ? $pInfo->products_price : ''), 'id="products_price_gross" onkeyup="updateNet(\'products_price\')"'); ?></td>
              </tr>
            </table>

            <script type="text/javascript"><!--
              updateGross('products_price');
            //--></script>
          </fieldset>
        </td>
        <td class="smallText" width="50%" height="100%" valign="top">
          <fieldset style="height: 100%;">
            <legend>Data</legend>

            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="smallText"><?php echo TEXT_PRODUCTS_MANUFACTURER; ?></td>
                <td class="smallText"><?php echo osc_draw_pull_down_menu('manufacturers_id', $manufacturers_array, (isset($pInfo) ? $pInfo->manufacturers_id : '')); ?></td>
              </tr>
              <tr>
                <td class="smallText"><?php echo TEXT_PRODUCTS_QUANTITY; ?></td>
                <td class="smallText"><?php echo osc_draw_input_field('products_quantity', (isset($pInfo) ? $pInfo->products_quantity : '')); ?></td>
              </tr>
              <tr>
                <td class="smallText"><?php echo TEXT_PRODUCTS_WEIGHT; ?></td>
                <td class="smallText"><?php echo osc_draw_input_field('products_weight', (isset($pInfo) ? $pInfo->products_weight : '')). '&nbsp;' . osc_draw_pull_down_menu('products_weight_class', $weight_class_array, (isset($pInfo) ? $pInfo->products_weight_class : SHIPPING_WEIGHT_UNIT)); ?></td>
              </tr>
            </table>
          </fieldset>
        </td>
      </tr>
      <tr>
        <td class="smallText" width="50%" height="100%" valign="top">
          <fieldset style="height: 100%;">
            <legend><?php echo TEXT_PRODUCTS_STATUS; ?></legend>

            <?php echo osc_draw_radio_field('products_status', array(array('id' => '1', 'text' => TEXT_PRODUCT_AVAILABLE), array('id' => '0', 'text' => TEXT_PRODUCT_NOT_AVAILABLE)), (isset($pInfo) ? $pInfo->products_status : '0'), '', false, '<br />'); ?>
          </fieldset>
        </td>
        <td class="smallText" width="50%" height="100%" valign="top">
          <fieldset style="height: 100%;">
            <legend>Information</legend>

            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="smallText"><?php echo TEXT_PRODUCTS_DATE_AVAILABLE; ?></td>
                <td class="smallText"><?php echo osc_draw_input_field('products_date_available', (isset($pInfo) ? $pInfo->products_date_available : ''), 'id="calendarValue"'); ?><input type="button" value="..." id="calendarTrigger" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "calendarValue", ifFormat: "%Y-%m-%d", button: "calendarTrigger" } );</script><small>(YYYY-MM-DD)</small></td>
              </tr>
            </table>
          </fieldset>
        </td>
      </tr>
    </table>
  </div>

  <div class="tab-page" id="tabImages">
    <h2 class="tab"><?php echo TAB_IMAGES; ?></h2>

    <script type="text/javascript"><!--
      mainTabPane.addTabPage( document.getElementById( "tabImages" ) );
    //--></script>

<?php
  if (isset($pInfo)) {
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="100%" height="100%" valign="top">
          <fieldset style="height: 100%;">
            <legend>Upload New Image</legend>

            <iframe id="fileUpload" src="<?php echo tep_href_link(FILENAME_PRODUCTS, 'action=fileUploadForm' . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '')); ?>" frameborder="0" scrolling="0" style="width: 100%; height: 50px;"></iframe>
          </fieldset>

          <fieldset style="height: 100%;">
            <legend>Original Images</legend>

            <div id="imagesOriginal" style="overflow: auto;"></div>
          </fieldset>

          <fieldset style="height: 100%;">
            <legend>Images</legend>

            <div id="imagesOther" style="overflow: auto;"></div>
          </fieldset>

<script type="text/javascript"><!--
  getImages();
//--></script>

        </td>
      </tr>
    </table>

<?php
  } else {
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" height="100%" valign="top">
          <fieldset style="height: 100%;">
            <legend>Upload New Original Image</legend>

            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="smallText"><?php echo TEXT_PRODUCTS_IMAGE; ?></td>
                <td class="smallText"><?php echo osc_draw_file_field('products_image'); ?></td>
              </tr>
            </table>
          </fieldset>
        </td>
      </tr>
    </table>

<?php
  }
?>

  </div>

  <div class="tab-page" id="tabAttributes">
    <h2 class="tab"><?php echo TAB_ATTRIBUTES; ?></h2>

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
        <td align="center" width="10%" class="smallText">
          <input type="button" value=">>" onclick="moreFields()" class="infoBoxButton">
        </td>
        <td width="60%" valign="top" class="smallText">
          <fieldset>
            <legend><?php echo FIELDSET_ASSIGNED_ATTRIBUTES; ?></legend>

            <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
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
           '                <td class="smallText" colspan="3"><b>' . $Qattributes->value('products_options_name') . '</b></td>' . "\n" .
           '              </tr>' . "\n";

      $current_attribute_group = $Qattributes->value('products_options_name');
    }

    echo '              <tr id="attribute-' . $Qattributes->valueInt('products_options_id') . '_' . $Qattributes->valueInt('products_options_values_id') . '">' . "\n" .
         '                <td class="smallText" width="50%">' . $Qattributes->value('products_options_values_name') . '</td>' . "\n" .
         '                <td class="smallText">' . osc_draw_pull_down_menu('attribute_prefix[' . $Qattributes->valueInt('products_options_id') . '][' . $Qattributes->valueInt('products_options_values_id') . ']', array(array('id' => '+', 'text' => '+'), array('id' => '-', 'text' => '-')), $Qattributes->value('price_prefix')) . '&nbsp;' . osc_draw_input_field('attribute_price[' . $Qattributes->valueInt('products_options_id') . '][' . $Qattributes->valueInt('products_options_values_id') . ']', $Qattributes->value('options_values_price')) . '</td>' . "\n" .
         '                <td class="smallText" align="right"><input type="button" value="-" id="attribute-' . $Qattributes->valueInt('products_options_id') . '_' . $Qattributes->valueInt('products_options_values_id') . '-button" onclick="toggleAttributeStatus(\'attribute-' . $Qattributes->valueInt('products_options_id') . '_' . $Qattributes->valueInt('products_options_values_id') . '\');" class="infoBoxButton"></td>' . "\n" .
         '              </tr>' . "\n";
  }
?>
            </table>

            <span id="writeroot"></span>

            <div id="readroot" style="display: none">
              <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                  <td class="smallText" colspan="3"><b><span id="attribteGroupName">&nbsp;</span></b></td>
                </tr>
                <tr class="attributeAdd">
                  <td class="smallText" width="50%"><span id="attributeKey">&nbsp;</span></td>
                  <td class="smallText"><?php echo osc_draw_pull_down_menu('new_attribute_prefix', array(array('id' => '+', 'text' => '+'), array('id' => '-', 'text' => '-')), '+', 'disabled') . '&nbsp;' . osc_draw_input_field('new_attribute_price', '', 'disabled'); ?></td>
                  <td class="smallText" align="right"><input type="button" value="-" onclick="this.parentNode.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode.parentNode);" class="infoBoxButton"></td>
                </tr>
              </table>
            </div>
          </fieldset>
        </td>
      </tr>
    </table>
  </div>

  <div class="tab-page" id="tabCategories">
    <h2 class="tab"><?php echo TAB_CATEGORIES; ?></h2>

<script type="text/javascript">mainTabPane.addTabPage( document.getElementById( "tabCategories" ) );</script>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
          <thead>
            <tr>
              <th>Categories</th>
              <th>Selected</th>
            </tr>
          </thead>
          <tbody>
<?php
  $product_categories_array = array();

  $Qcategories = $osC_Database->query('select categories_id from :table_products_to_categories where products_id = :products_id');
  $Qcategories->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
  $Qcategories->bindInt(':products_id', $_GET['pID']);
  $Qcategories->execute();

  while ($Qcategories->next()) {
    $product_categories_array[] = $Qcategories->valueInt('categories_id');
  }

  $assignedCategoryTree = new osC_CategoryTree();
  $assignedCategoryTree->setBreadcrumbUsage(false);
  $assignedCategoryTree->setSpacerString('&nbsp;', 5);

  foreach ($assignedCategoryTree->getTree() as $value) {
    echo '          <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' . "\n" .
         '            <td class="smallText"><a href="#" onclick="document.product.categories_' . $value['id'] . '.checked=!document.product.categories_' . $value['id'] . '.checked;">' . $value['title'] . '</a></td>' . "\n" .
         '            <td class="smallText" align="right">' . osc_draw_checkbox_field('categories[]', $value['id'], in_array($value['id'], $product_categories_array), 'id="categories_' . $value['id'] . '"') . '</td>' . "\n" .
         '          </tr>' . "\n";
  }
?>
        </table></td>
      </tr>
    </table>
  </div>

  <p align="right"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . tep_href_link(FILENAME_PRODUCTS, 'cPath=' . $cPath . '&search=' . $_GET['search'] . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '')) . '\';" class="operationButton">'; ?></p>

  </form>
</div>