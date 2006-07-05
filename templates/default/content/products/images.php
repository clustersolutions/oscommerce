<?php
/*
  $Id: index.php 199 2005-09-22 17:56:13 +0200 (Do, 22 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
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
  <div id="productImageThumbnails" class="content" style="overflow: auto; width: <?php echo ($osC_Image->getWidth('thumbnails') * 2) + 25; ?>px;">

<?php
  $Qimages = $osC_Database->query('select id, image, default_flag from :table_products_images where products_id = :products_id order by sort_order');
  $Qimages->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
  $Qimages->bindInt(':products_id', $osC_Product->getID());
  $Qimages->execute();

  $large_image = '';

  while ($Qimages->next()) {
    if ( empty($large_image) || (isset($_GET['image']) && ($_GET['image'] == $Qimages->valueInt('id'))) || (!isset($_GET['image']) && ($Qimages->valueInt('default_flag') === 1)) ) {
      $large_image = $osC_Image->show($Qimages->value('image'), $osC_Product->getTitle(), 'id="productImageLarge"', 'large');
    }

    echo '<span style="width: ' . $osC_Image->getWidth($osC_Image->getCode(DEFAULT_IMAGE_GROUP_ID)) . 'px; padding: 2px; float: left; text-align: center;"><a href="' . tep_href_link(FILENAME_PRODUCTS, 'images&' . $osC_Product->getKeyword() . '&image=' . $Qimages->valueInt('id')) . '" onclick="loadImage(\'' . $osC_Image->getAddress($Qimages->value('image'), 'large') . '\'); return false;">' . $osC_Image->show($Qimages->value('image'), $osC_Product->getTitle(), 'height="' . $osC_Image->getHeight($osC_Image->getCode(DEFAULT_IMAGE_GROUP_ID)) . '" style="max-width: ' . $osC_Image->getWidth($osC_Image->getCode(DEFAULT_IMAGE_GROUP_ID)) . 'px;"') . '</a></span>';
  }
?>

  </div>

  <div id="productImageLargeBlock" style="position: absolute; left: <?php echo ($osC_Image->getWidth($osC_Image->getCode(DEFAULT_IMAGE_GROUP_ID)) * 2) + 60; ?>px; top: 10px; text-align: center; width: <?php echo $osC_Image->getWidth('large'); ?>px;">

<?php
  echo $large_image;
?>

  </div>
</div>