<?php
/*
  $Id: products_edit.php,v 1.7 2004/11/20 02:08:20 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  if (isset($_GET['pID']) && empty($_POST)) {
    $Qp = $osC_Database->query('select products_id, products_quantity, products_model, products_image, products_price, products_weight, products_weight_class, products_date_added, products_last_modified, date_format(products_date_available, "%Y-%m-%d") as products_date_available, products_status, products_tax_class_id, manufacturers_id from :table_products where products_id = :products_id');
    $Qp->bindTable(':table_products', TABLE_PRODUCTS);
    $Qp->bindInt(':products_id', $_GET['pID']);
    $Qp->execute();

    $Qpd = $osC_Database->query('select products_name, products_description, products_url, language_id from :table_products_description where products_id = :products_id');
    $Qpd->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qpd->bindInt(':products_id', $_GET['pID']);
    $Qpd->execute();

    $pd_extra = array();
    while ($Qpd->next()) {
      $pd_extra['products_name'][$Qpd->valueInt('language_id')] = $Qpd->value('products_name');
      $pd_extra['products_description'][$Qpd->valueInt('language_id')] = $Qpd->value('products_description');
      $pd_extra['products_url'][$Qpd->valueInt('language_id')] = $Qpd->value('products_url');
    }

    $pInfo = new objectInfo(array_merge($Qp->toArray(), $pd_extra));
  } elseif (!empty($_POST)) {
    $pInfo = new objectInfo($_POST);
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
  $Qwc->bindInt(':language_id', $osC_Session->value('languages_id'));
  $Qwc->execute();

  $weight_class_array = array();
  while ($Qwc->next()) {
    $weight_class_array[] = array('id' => $Qwc->valueInt('weight_class_id'),
                                  'text' => $Qwc->value('weight_class_title'));
  }

  require('includes/classes/directory_listing.php');
  $osC_Dir_Images = new osC_DirectoryListing('../images');
  $osC_Dir_Images->setExcludeEntries('CVS');
  $osC_Dir_Images->setIncludeFiles(false);
  $osC_Dir_Images->setRecursive(true);
  $osC_Dir_Images->setAddDirectoryToFilename(true);
  $files = $osC_Dir_Images->getFiles();

  $image_directories = array(array('id' => '', 'text' => 'images/'));
  foreach ($files as $file) {
    $image_directories[] = array('id' => $file['name'], 'text' => 'images/' . $file['name']);
  }
?>

<script type="text/javascript" src="external/FCKeditor/2.0b1/fckeditor.js"></script>
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

<script language="javascript"><!--
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
    var selected_value = document.forms["new_product"].products_tax_class_id.selectedIndex;
    var parameterVal = document.forms["new_product"].products_tax_class_id[selected_value].value;

    if ( (parameterVal > 0) && (tax_rates[parameterVal] > 0) ) {
      return tax_rates[parameterVal];
    } else {
      return 0;
    }
  }

  function updateGross() {
    var taxRate = getTaxRate();
    var grossValue = document.forms["new_product"].products_price.value;

    if (taxRate > 0) {
      grossValue = grossValue * ((taxRate / 100) + 1);
    }

    document.forms["new_product"].products_price_gross.value = doRound(grossValue, 4);
  }

  function updateNet() {
    var taxRate = getTaxRate();
    var netValue = document.forms["new_product"].products_price_gross.value;

    if (taxRate > 0) {
      netValue = netValue / ((taxRate / 100) + 1);
    }

    document.forms["new_product"].products_price.value = doRound(netValue, 4);
  }

  var counter = 0;

  function moreFields() {
    var existingFields = document.new_product.getElementsByTagName('input');
    var attributeExists = false;

    for (i=0; i<existingFields.length; i++) {
      if (existingFields[i].name == 'attribute_price[' + document.new_product.attributes.options[document.new_product.attributes.options.selectedIndex].parentNode.id + '][' + document.new_product.attributes.options[document.new_product.attributes.options.selectedIndex].value + ']') {
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

      spanFields[0].innerHTML = document.new_product.attributes.options[document.new_product.attributes.options.selectedIndex].parentNode.label;
      spanFields[1].innerHTML = document.new_product.attributes.options[document.new_product.attributes.options.selectedIndex].text;

      for (y=0; y<inputFields.length; y++) {
        if (inputFields[y].type != 'button') {
          inputFields[y].name = inputFields[y].name.substr(4) + '[' + document.new_product.attributes.options[document.new_product.attributes.options.selectedIndex].parentNode.id + '][' + document.new_product.attributes.options[document.new_product.attributes.options.selectedIndex].value + ']';
          inputFields[y].disabled = false;
        }
      }

      for (y=0; y<selectFields.length; y++) {
        selectFields[y].name = selectFields[y].name.substr(4) + '[' + document.new_product.attributes.options[document.new_product.attributes.options.selectedIndex].parentNode.id + '][' + document.new_product.attributes.options[document.new_product.attributes.options.selectedIndex].value + ']';
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

  function reloadImage() {
    var image = document.new_product.products_image.value;
    var preview = document.getElementById('previewImage');

    preview.src = '../images/' + image;
  }
//--></script>

<link type="text/css" rel="stylesheet" href="external/tabpane/css/luna/tab.css" />
<script type="text/javascript" src="external/tabpane/js/tabpane.js"></script>

<h1><?php echo sprintf(TEXT_NEW_PRODUCT, tep_output_generated_category_path($current_category_id)); ?></h1>

<div class="tab-pane" id="mainTabPane">
  <script type="text/javascript"><!--
    var mainTabPane = new WebFXTabPane( document.getElementById( "mainTabPane" ) );
  //--></script>

<?php
  echo tep_draw_form('new_product', FILENAME_PRODUCTS, 'cPath=' . $cPath . '&search=' . $_GET['search'] . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '') . '&action=new_product_preview', 'post', 'enctype="multipart/form-data"');
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
  foreach ($osC_Language->getAll() as $language) {
?>

      <div class="tab-page" id="tabDescriptionLanguages_<?php echo $language['code']; ?>">
        <h2 class="tab"><?php echo tep_image('../includes/languages/' . $language['directory'] . '/images/' . $language['image'], $language['name']) . '&nbsp;' . $language['name']; ?></h2>

        <script type="text/javascript"><!--
          descriptionTabPane.addTabPage( document.getElementById( "tabDescriptionLanguages_<?php echo $language['code']; ?>" ) );
        //--></script>

        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText"><?php echo TEXT_PRODUCTS_NAME; ?></td>
            <td class="smallText"><?php echo osc_draw_input_field('products_name[' . $language['id'] . ']', (isset($pInfo) && is_array($pInfo->products_name) && isset($pInfo->products_name[$language['id']]) ? $pInfo->products_name[$language['id']] : '')); ?></td>
          </tr>
          <tr>
            <td class="smallText" valign="top"><?php echo TEXT_PRODUCTS_DESCRIPTION; ?></td>
            <td class="smallText"><?php echo tep_draw_textarea_field('products_description[' . $language['id'] . ']', 'soft', '70', '15', (isset($pInfo) && is_array($pInfo->products_description) && isset($pInfo->products_description[$language['id']]) ? $pInfo->products_description[$language['id']] : ''), 'id="fckpd_' . $language['code'] . '" style="width: 100%;"'); ?></td>
          </tr>
          <tr>
            <td class="smallText"><?php echo TEXT_PRODUCTS_URL; ?></td>
            <td class="smallText"><?php echo osc_draw_input_field('products_url[' . $language['id'] . ']', (isset($pInfo) && is_array($pInfo->products_url) && isset($pInfo->products_url[$language['id']]) ? $pInfo->products_url[$language['id']] : '')); ?></td>
          </tr>
        </table>

        <script type="text/javascript"><!--
          var fckpd_<?php echo $language['code']; ?> = new FCKeditor('fckpd_<?php echo $language['code']; ?>');
          fckpd_<?php echo $language['code']; ?>.BasePath = "<?php echo DIR_WS_CATALOG . 'admin/external/FCKeditor/2.0b1/'; ?>";
          fckpd_<?php echo $language['code']; ?>.Height = "400";
          fckpd_<?php echo $language['code']; ?>.ReplaceTextarea();
        //--></script>
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
                <td class="smallText"><?php echo osc_draw_pull_down_menu('products_tax_class_id', $tax_class_array, (isset($pInfo) ? $pInfo->products_tax_class_id : ''), 'onchange="updateGross()"'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><?php echo TEXT_PRODUCTS_PRICE_NET; ?></td>
                <td class="smallText"><?php echo osc_draw_input_field('products_price', (isset($pInfo) ? $pInfo->products_price : ''), 'onKeyUp="updateGross()"'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><?php echo TEXT_PRODUCTS_PRICE_GROSS; ?></td>
                <td class="smallText"><?php echo osc_draw_input_field('products_price_gross', (isset($pInfo) ? $pInfo->products_price : ''), 'OnKeyUp="updateNet()"'); ?></td>
              </tr>
            </table>

            <script language="javascript"><!--
              updateGross();
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
                <td class="smallText"><?php echo TEXT_PRODUCTS_MODEL; ?></td>
                <td class="smallText"><?php echo osc_draw_input_field('products_model', (isset($pInfo) ? $pInfo->products_model : '')); ?></td>
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

            <?php echo osc_draw_radio_field('products_status', array(array('id' => '1', 'text' => TEXT_PRODUCT_AVAILABLE), array('id' => '0', 'text' => TEXT_PRODUCT_NOT_AVAILABLE)), (isset($pInfo) ? $pInfo->products_status : '0'), '', false, '<br>'); ?>
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

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="50%" height="100%" valign="top">
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="smallText" width="50%" height="100%" valign="top">
                <fieldset style="height: 100%;">
                  <legend>Image Location</legend>

                  <p><?php echo DIR_WS_CATALOG . 'images/' . osc_draw_input_field('products_image', (isset($pInfo) ? $pInfo->products_image : '')) . '&nbsp;<input type="button" value="Preview" onClick="reloadImage();" class="infoBoxButton">'; ?></p>
                </fieldset>
              </td>
            </tr>
            <tr>
              <td class="smallText" width="50%" height="100%" valign="top">
                <fieldset style="height: 100%;">
                  <legend>Upload New Image</legend>

                  <table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr>
                      <td class="smallText"><?php echo TEXT_PRODUCTS_IMAGE; ?></td>
                      <td class="smallText"><?php echo osc_draw_file_field('products_image_new'); ?></td>
                    </tr>
                    <tr>
                      <td class="smallText"><?php echo 'Destination'; ?></td>
                      <td class="smallText"><?php echo osc_draw_pull_down_menu('products_image_location', $image_directories, dirname($pInfo->products_image)); ?></td>
                    </tr>
                  </table>
                </fieldset>
              </td>
            </tr>
          </table>
        </td>
        <td class="smallText" width="50%" height="100%" valign="top">
          <fieldset style="height: 100%;">
            <legend>Preview</legend>

            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td align="center"><?php echo tep_image('../images/' . $pInfo->products_image, '', '', '', 'id="previewImage"'); ?></td>
              </tr>
            </table>
          </fieldset>
        </td>
      </tr>
    </table>
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
  $Qoptions->bindInt(':language_id', $osC_Session->value('languages_id'));
  $Qoptions->execute();

  while ($Qoptions->next()) {
    echo '          <optgroup label="' . $Qoptions->value('products_options_name') . '" id="' . $Qoptions->value('products_options_id') . '">' . "\n";

    $Qvalues = $osC_Database->query('select pov.products_options_values_id, pov.products_options_values_name from :table_products_options_values pov, :table_products_options_values_to_products_options pov2po where pov2po.products_options_id = :products_options_id and pov2po.products_options_values_id = pov.products_options_values_id and pov.language_id = :language_id order by pov.products_options_values_name');
    $Qvalues->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
    $Qvalues->bindTable(':table_products_options_values_to_products_options', TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);
    $Qvalues->bindInt(':products_options_id', $Qoptions->valueInt('products_options_id'));
    $Qvalues->bindInt(':language_id', $osC_Session->value('languages_id'));
    $Qvalues->execute();

    while ($Qvalues->next()) {
      echo '            <option value="' . $Qvalues->valueInt('products_options_values_id') . '">' . $Qvalues->value('products_options_values_name') . '</option>' . "\n";
    }

    echo '          </optgroup>' . "\n";
  }
?>
        </select></td>
        <td align="center" width="10%" class="smallText">
          <input type="button" value=">>" onClick="moreFields()" class="infoBoxButton">
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
  $Qattributes->bindInt(':language_id', $osC_Session->value('languages_id'));
  $Qattributes->bindInt(':language_id', $osC_Session->value('languages_id'));
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
         '                <td class="smallText" align="right"><input type="button" value="-" id="attribute-' . $Qattributes->valueInt('products_options_id') . '_' . $Qattributes->valueInt('products_options_values_id') . '-button" onClick="toggleAttributeStatus(\'attribute-' . $Qattributes->valueInt('products_options_id') . '_' . $Qattributes->valueInt('products_options_values_id') . '\');" class="infoBoxButton"></td>' . "\n" .
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
                  <td class="smallText" align="right"><input type="button" value="-" onClick="this.parentNode.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode.parentNode);" class="infoBoxButton"></td>
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
    echo '          <tr onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);">' . "\n" .
         '            <td class="smallText"><a href="#" onClick="document.new_product.categories_' . $value['id'] . '.checked=!document.new_product.categories_' . $value['id'] . '.checked;">' . $value['title'] . '</a></td>' . "\n" .
         '            <td class="smallText" align="right">' . osc_draw_checkbox_field('categories[]', $value['id'], in_array($value['id'], $product_categories_array), 'id="categories_' . $value['id'] . '"') . '</td>' . "\n" .
         '          </tr>' . "\n";
  }
?>
        </table></td>
      </tr>
    </table>
  </div>

  <p align="right"><?php echo osc_draw_hidden_field('products_date_added', (isset($pInfo) && isset($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d'))) . '<input type="submit" value="' . IMAGE_PREVIEW . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="document.location.href=\'' . tep_href_link(FILENAME_PRODUCTS, 'cPath=' . $cPath . '&search=' . $_GET['search'] . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '')) . '\';" class="operationButton">'; ?></p>

  </form>
</div>
