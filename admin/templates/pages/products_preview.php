<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  $Qp = $osC_Database->query('select p.products_id, p.products_quantity, p.products_price, p.products_weight, p.products_weight_class, p.products_date_added, p.products_last_modified, date_format(p.products_date_available, "%Y-%m-%d") as products_date_available, p.products_status, p.products_tax_class_id, p.manufacturers_id, i.image from :table_products p left join :table_products_images i on (p.products_id = i.products_id and default_flag = :default_flag) where p.products_id = :products_id');
  $Qp->bindTable(':table_products', TABLE_PRODUCTS);
  $Qp->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
  $Qp->bindInt(':products_id', $_GET['pID']);
  $Qp->bindInt(':default_flag', 1);
  $Qp->execute();

  $Qpd = $osC_Database->query('select products_name, products_description, products_model, products_url, language_id from :table_products_description where products_id = :products_id');
  $Qpd->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qpd->bindInt(':products_id', $_GET['pID']);
  $Qpd->execute();

  $pd_extra = array();
  while ($Qpd->next()) {
    $pd_extra['products_name'][$Qpd->valueInt('language_id')] = $Qpd->value('products_name');
    $pd_extra['products_description'][$Qpd->valueInt('language_id')] = $Qpd->value('products_description');
    $pd_extra['products_model'][$Qpd->valueInt('language_id')] = $Qpd->value('products_model');
    $pd_extra['products_url'][$Qpd->valueInt('language_id')] = $Qpd->value('products_url');
  }

  $pInfo = new objectInfo(array_merge($Qp->toArray(), $pd_extra));
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr bgcolor="#fff3e7">
    <td>
<?php
  foreach ($osC_Language->getAll() as $l) {
    echo '<span id="lang_' . $l['code'] . '"' . (($l['code'] == $osC_Language->getCode()) ? ' class="highlight"' : '') . '><a href="javascript:toggleDivBlocks(\'pName_\', \'pName_' . $l['code'] . '\'); toggleClass(\'lang_\', \'lang_' . $l['code'] . '\', \'highlight\', \'span\');">' . tep_image('../includes/languages/' . $l['code'] . '/images/' . $l['image'], $l['name']) . '</a></span>&nbsp;&nbsp;';
  }
?>
    </td>
  </tr>
</table>

<?php
  foreach ($osC_Language->getAll() as $l) {
?>

<div id="pName_<?php echo $l['code']; ?>" <?php echo (($l['code'] != $osC_Language->getCode()) ? ' style="display: none;"' : ''); ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td><h1><?php echo $pInfo->products_name[$l['id']] . (!empty($pInfo->products_model[$l['id']]) ? '<br /><span class="smallText">' . $pInfo->products_model[$l['id']] . '</span>': ''); ?></h1></td>
      <td align="right"><h1><?php echo $osC_Currencies->format($pInfo->products_price); ?></h1></td>
    </tr>
  </table>

  <p class="main"><?php echo $osC_Image->show($pInfo->image, $pInfo->products_name[$l['id']], 'align="right" hspace="5" vspace="5"', 'product_info') . $pInfo->products_description[$l['id']]; ?></p>

<?php
    if (!empty($pInfo->products_url[$l['id']])) {
      echo '<p class="main">' . sprintf(TEXT_PRODUCT_MORE_INFORMATION, $pInfo->products_url[$l['id']]) . '</p>';
    }
?>

<?php
    if ($pInfo->products_date_available > date('Y-m-d')) {
      echo '<p class="smallText" align="center">' . sprintf(TEXT_PRODUCT_DATE_AVAILABLE, tep_date_long($pInfo->products_date_available)) . '</p>';
    } else {
      echo '<p class="smallText" align="center">' . sprintf(TEXT_PRODUCT_DATE_ADDED, tep_date_long($pInfo->products_date_added)) . '</p>';
    }
?>

</div>

<?php
  }

  echo '<p align="right"><input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . tep_href_link(FILENAME_PRODUCTS, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '\';" class="operationButton"></p>';
?>

</form>
