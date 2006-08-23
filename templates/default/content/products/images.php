<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $large_image = $osC_Image->show($osC_Product->getImage(), $osC_Product->getTitle(), 'id="productImageLarge"', 'large');
?>

<style type="text/css">
<!--
#pageContent {
  width: 100%;
  margin: 0;
  padding: 0;
}

div#pageBlockLeft {
  width: 100%;
  margin: 0;
}
//-->
</style>

<script language="javascript" type="text/javascript">
<!--
function loadImage(imageUrl) {
  new Effect.Fade('productImageLarge', {duration: 0.3, afterFinish: function() {
    document.getElementById('productImageLarge').src = imageUrl;
    new Effect.Appear('productImageLarge', {duration: 0.3});
  }});
}
//-->
</script>

<div style="padding: 10px;" class="moduleBox">

<?php
  if ($osC_Product->numberOfImages() > 1) {
?>

  <div id="productImageThumbnails" class="content" style="overflow: auto; width: <?php echo ($osC_Image->getWidth('thumbnails') * 2) + 25; ?>px;">

<?php
    foreach ($osC_Product->getImages() as $images) {
      if ( isset($_GET['image']) && ($_GET['image'] == $images['id']) ) {
        $large_image = $osC_Image->show($images['image'], $osC_Product->getTitle(), 'id="productImageLarge"', 'large');
      }

      echo '<span style="width: ' . $osC_Image->getWidth($osC_Image->getCode(DEFAULT_IMAGE_GROUP_ID)) . 'px; padding: 2px; float: left; text-align: center;"><a href="' . tep_href_link(FILENAME_PRODUCTS, 'images&' . $osC_Product->getKeyword() . '&image=' . $images['id']) . '" onclick="loadImage(\'' . $osC_Image->getAddress($images['image'], 'large') . '\'); return false;">' . $osC_Image->show($images['image'], $osC_Product->getTitle(), 'height="' . $osC_Image->getHeight($osC_Image->getCode(DEFAULT_IMAGE_GROUP_ID)) . '" style="max-width: ' . $osC_Image->getWidth($osC_Image->getCode(DEFAULT_IMAGE_GROUP_ID)) . 'px;"') . '</a></span>';
    }
?>

  </div>

<?php
  }
?>

  <div id="productImageLargeBlock" style="position: absolute; left: <?php echo ($osC_Product->numberOfImages() > 1) ? ($osC_Image->getWidth($osC_Image->getCode(DEFAULT_IMAGE_GROUP_ID)) * 2) + 60 : 10; ?>px; top: 10px; text-align: center; width: <?php echo $osC_Image->getWidth('large'); ?>px;">

<?php
  echo $large_image;
?>

  </div>
</div>
