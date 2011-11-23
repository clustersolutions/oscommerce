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

<div id="sectionMenu_images">
  <div class="infoBox">

<?php
  if ( $new_product ) {
    echo '<h3>' . HTML::icon('new.png') . ' ' . OSCOM::getDef('action_heading_new_product') . '</h3>';
  } else {
    echo '<h3>' . HTML::icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('products_name') . '</h3>';
  }
?>

    <h4><?php echo OSCOM::getDef('subsection_new_image'); ?></h4>

    <fieldset>
      <div style="float: right;">
        <a href="#" id="remoteFilesLink" onclick="switchImageFilesView('remote');" style="background-color: #E5EFE5;"><?php echo OSCOM::getDef('image_remote_upload'); ?></a> | <a href="#" id="localFilesLink" onclick="switchImageFilesView('local');"><?php echo OSCOM::getDef('image_local_files'); ?></a>
      </div>

      <div id="remoteFiles">
        <span id="fileUploadField"></span>

<?php
  if ( !$new_product ) {
    echo '<input type="button" id="uploadFile" value="' . OSCOM::getDef('button_send_to_server') . '" class="operationButton" /><div id="showProgress" style="display: none; padding-left: 10px;">' . HTML::icon('progress_ani.gif') . '&nbsp;' . OSCOM::getDef('image_upload_progress') . '</div>';
  } else {
    echo HTML::fileField('products_image');
  }
?>

      </div>

<?php
  if ( !$new_product ) {
?>

<script>
$('#uploadFile').upload( {
  name: 'products_image',
  method: 'post',
  enctype: 'multipart/form-data',
  action: '<?php //echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&action=fileUpload'); ?>',
  onSubmit: function() {
    $('#showProgress').css('display', 'inline');
  },
  onComplete: function(data) {
    $('#showProgress').css('display', 'none');
    getImages();
  }
});
</script>

<?php
  }
?>

      <div id="localFiles" style="display: none;">
        <p><?php echo OSCOM::getDef('introduction_select_local_images'); ?></p>

        <select id="localImagesSelection" name="localimages[]" size="5" multiple="multiple" style="width: 100%;"></select>

        <div id="showProgressGetLocalImages" style="display: none; float: right; padding-right: 10px;"><?php echo HTML::icon('progress_ani.gif') . '&nbsp;' . OSCOM::getDef('image_retrieving_local_files'); ?></div>

        <p><?php echo realpath('../images/products/_upload'); ?></p>

<?php
  if ( !$new_product ) {
    echo '<input type="button" value="Assign To Product" class="operationButton" onclick="assignLocalImages();" /><div id="showProgressAssigningLocalImages" style="display: none; padding-left: 10px;">' . HTML::icon('progress_ani.gif') . '&nbsp;' . OSCOM::getDef('image_multiple_upload_progress') . '</div>';
  }
?>

      </div>
    </fieldset>

<script>
$(function(){
  getLocalImages();
});
</script>

<?php
  if ( !$new_product ) {
?>

    <fieldset style="height: 100%;">
      <legend><?php echo OSCOM::getDef('subsection_original_images'); ?></legend>

      <div id="imagesOriginal" style="overflow: auto;"></div>
    </fieldset>

    <fieldset style="height: 100%;">
      <legend><?php echo OSCOM::getDef('subsection_images'); ?></legend>

      <div id="imagesOther" style="overflow: auto;"></div>
    </fieldset>

<script>
$(function(){
  getImages();
});
</script>

<?php
  }
?>

  </div>
</div>

<div id="deleteImageDialog" title="<?php echo OSCOM::getDef('action_heading_delete_image'); ?>"><p><?php echo OSCOM::getDef('introduction_delete_image'); ?></p></div>

<script>
$(function() {
  $('#deleteImageDialog').dialog({
    autoOpen: false,
    width: 600,
    modal: true
  });
});

function switchImageFilesView(layer) {
  if (layer == 'local') {
    var layer1 = $('#remoteFiles');
    var layer1link = $('#remoteFilesLink');
    var layer2 = $('#localFiles');
    var layer2link = $('#localFilesLink');
  } else {
    var layer1 = $('#localFiles');
    var layer1link = $('#localFilesLink');
    var layer2 = $('#remoteFiles');
    var layer2link = $('#remoteFilesLink');
  }

  if ( (layer != 'local') || ((layer == 'local') && (layer1.css('display') != 'none')) ) {
    layer1.css('display', 'none');
    layer2.css('display', 'inline');
    layer1link.css('backgroundColor', '');
    layer2link.css('backgroundColor', '#E5EFE5');
  } else {
    getLocalImages();
  }
}

function getLocalImages() {
  $('#showProgressGetLocalImages').css('display', 'inline');

  $.getJSON('<?php echo OSCOM::getRPCLink(null, null, 'GetAvailableImages'); ?>',
    function (data) {
      var i = 0;
      var selectList = document.getElementById('localImagesSelection');

      for ( i=selectList.options.length; i>=0; i-- ) {
        selectList.options[i] = null;
      }

      for ( i=0; i<data.images.length; i++ ) {
        selectList.options[i] = new Option(data.images[i]);
        selectList.options[i].selected = false;
      }

      $('#showProgressGetLocalImages').css('display', 'none');
    }
  );
}

<?php
  if ( !$new_product ) {
?>

function removeImage(id) {
  $('#deleteImageDialog').dialog('option', 'buttons', {
    "Cancel": function() {
      $(this).dialog("close");
    },
    "Ok": function() {
      var image = id.split('_');

      $.getJSON('<?php //echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&action=deleteProductImage'); ?>' + '&image=' + image[1],
        function (data) {
          getImages();
        }
      );

      $(this).dialog("close");
    }
  } );

  $('#deleteImageDialog').dialog('open');
}

function setDefaultImage(id) {
  var image = id.split('_');

  $.getJSON('<?php //echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&action=setDefaultImage'); ?>' + '&image=' + image[1],
    function (data) {
      getImagesOriginals();
    }
  );
}

function showImages(data) {
  for ( i=0; i<data.entries.length; i++ ) {
    var entry = data.entries[i];

    var style = 'width: <?php echo $OSCOM_Image->getWidth('mini') + 20; ?>px; padding: 10px; float: left; text-align: center;';

    if ( entry[1] == '1' ) { // original (products_images_groups_id)
      var onmouseover = 'this.style.backgroundColor=\'#EFEBDE\'; this.style.backgroundImage=\'url(<?php echo HTML::icon('drag.png'); ?>)\'; this.style.backgroundRepeat=\'no-repeat\'; this.style.backgroundPosition=\'0 0\';';

      if ( entry[6] == '1' ) { // default_flag
        style += ' background-color: #E5EFE5;';

        var onmouseout = 'this.style.backgroundColor=\'#E5EFE5\'; this.style.backgroundImage=\'none\';';
      } else {
        var onmouseout = 'this.style.backgroundColor=\'#FFFFFF\'; this.style.backgroundImage=\'none\';';
      }
    } else {
      var onmouseover = 'this.style.backgroundColor=\'#EFEBDE\';';
      var onmouseout = 'this.style.backgroundColor=\'#FFFFFF\';';
    }

    var newdiv = '<span id="image_' + entry[0] + '" style="' + style + '" onmouseover="' + onmouseover + '" onmouseout="' + onmouseout + '">';
    newdiv += '<a href="' + entry[4] + '" target="_blank"><img src="<?php //echo DIR_WS_HTTP_CATALOG . 'images/products/mini/'; ?>' + entry[2] + '" border="0" height="<?php echo $OSCOM_Image->getHeight('mini'); ?>" alt="' + entry[2] + '" title="' + entry[2] + '" style="max-width: <?php echo $OSCOM_Image->getWidth('mini') + 20; ?>px;" /></a><br />' + entry[3] + '<br />' + entry[5] + ' bytes<br />';

    if ( entry[1] == '1' ) {
      if ( entry[6] == '1' ) {
        newdiv += '<?php echo HTML::icon('default.png'); ?>&nbsp;';
      } else {
        newdiv += '<a href="#" onclick="setDefaultImage(\'image_' + entry[0] + '\');"><?php echo HTML::icon('default_grey.png'); ?></a>&nbsp;';
      }

      newdiv += '<a href="#" onclick="removeImage(\'image_' + entry[0] + '\');"><?php echo HTML::icon('trash.png'); ?></a>';
    }

    newdiv += '</span>';

    if ( entry[1] == '1' ) {
      $('#imagesOriginal').append(newdiv);
    } else {
      $('#imagesOther').append(newdiv);
    }
  }

  $('#imagesOriginal').sortable( {
    update: function(event, ui) {
      $.getJSON('<?php //echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&action=reorderImages'); ?>' + '&' + $(this).sortable('serialize'),
        function (data) {
          getImagesOthers();
        }
      );
    }
  } );

  if ( $('#showProgressOriginal').css('display') != 'none') {
    $('#showProgressOriginal').css('display', 'none');
  }

  if ( $('#showProgressOther').css('display') != 'none') {
    $('#showProgressOther').css('display', 'none');
  }
}

function getImages() {
  getImagesOriginals(false);
  getImagesOthers(false);

  $.getJSON('<?php //echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&action=getImages'); ?>',
    function (data) {
      showImages(data);
    }
  );
}

function getImagesOriginals(makeCall) {
  $('#imagesOriginal').html('<div id="showProgressOriginal" style="float: left; padding-left: 10px;"><?php echo HTML::icon('progress_ani.gif') . '&nbsp;' . OSCOM::getDef('images_loading_from_server'); ?></div>');

  if ( makeCall != false ) {
    $.getJSON('<?php //echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&action=getImages&filter=originals'); ?>',
      function (data) {
        showImages(data);
      }
    );
  }
}

function getImagesOthers(makeCall) {
  $('#imagesOther').html('<div id="showProgressOther" style="float: left; padding-left: 10px;"><?php echo HTML::icon('progress_ani.gif') . '&nbsp;' . OSCOM::getDef('images_loading_from_server'); ?></div>');

  if ( makeCall != false ) {
    $.getJSON('<?php //echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&action=getImages&filter=others'); ?>',
      function (data) {
        showImages(data);
      }
    );
  }
}

function assignLocalImages() {
  $('#showProgressAssigningLocalImages').css('display', 'inline');

  var selectedFiles = '';

  $('#localImagesSelection :selected').each(function(i, selected) {
    selectedFiles += 'files[]=' + $(selected).text() + '&';
  });

  $.getJSON('<?php //echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&action=assignLocalImages'); ?>' + '&' + selectedFiles,
    function (data) {
      $('#showProgressAssigningLocalImages').css('display', 'none');
      getLocalImages();
      getImages();
    }
  );
}

<?php
  }
?>

</script>
