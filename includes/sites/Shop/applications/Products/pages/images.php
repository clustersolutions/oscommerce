<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\OSCOM;

  $large_image = $OSCOM_Image->show($OSCOM_Product->getImage(), $OSCOM_Product->getTitle(), 'id="productImageLarge"', 'large');
?>

<style type="text/css">
BODY {
  min-width: 0;
}
</style>

<script language="javascript" type="text/javascript">
function loadImage(imageUrl) {
  $("#productImageLarge").fadeOut('fast', function() {
    $("#productImageLarge").attr('src', imageUrl);
    $("#productImageLarge").fadeIn("slow");
  });
}
</script>

<div class="moduleBox">

<?php
  if ( $OSCOM_Product->numberOfImages() > 1 ) {
?>

  <div id="productImageThumbnails" class="content" style="position: absolute; top: 10px; overflow: auto; width: <?php echo ($OSCOM_Image->getWidth('thumbnails') * 2) + 15; ?>px;">

<?php
    foreach ( $OSCOM_Product->getImages() as $images ) {
      if ( isset($_GET['image']) && ($_GET['image'] == $images['id']) ) {
        $large_image = $OSCOM_Image->show($images['image'], $OSCOM_Product->getTitle(), 'id="productImageLarge"', 'large');
      }

      echo '<span style="width: ' . $OSCOM_Image->getWidth($OSCOM_Image->getCode(DEFAULT_IMAGE_GROUP_ID)) . 'px; padding: 2px; float: left; text-align: center;">' . osc_link_object(OSCOM::getLink(null, null, 'Images&' . $OSCOM_Product->getKeyword() . '&image=' . $images['id']),  $OSCOM_Image->show($images['image'], $OSCOM_Product->getTitle(), 'height="' . $OSCOM_Image->getHeight($OSCOM_Image->getCode(DEFAULT_IMAGE_GROUP_ID)) . '" style="max-width: ' . $OSCOM_Image->getWidth($OSCOM_Image->getCode(DEFAULT_IMAGE_GROUP_ID)) . 'px;"'), 'onclick="loadImage(\'' . $OSCOM_Image->getAddress($images['image'], 'large') . '\'); return false;"') . '</span>';
    }
?>

  </div>

<?php
  }
?>

  <div id="productImageLargeBlock" style="position: absolute; left: <?php echo ($OSCOM_Product->numberOfImages() > 1) ? ($OSCOM_Image->getWidth($OSCOM_Image->getCode(DEFAULT_IMAGE_GROUP_ID)) * 2) + 60 : 10; ?>px; top: 10px; text-align: center; width: <?php echo $OSCOM_Image->getWidth('large'); ?>px;">

<?php
  echo $large_image;
?>

  </div>
</div>
