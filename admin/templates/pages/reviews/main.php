<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }

  $Qreviews = $osC_Database->query('select r.reviews_id, r.products_id, r.date_added, r.last_modified, r.reviews_rating, r.reviews_status, r.languages_id, pd.products_name, l.name as languages_name, l.code as languages_code from :table_reviews r left join :table_products_description pd on (r.products_id = pd.products_id and r.languages_id = pd.language_id), :table_languages l where r.languages_id = l.languages_id order by r.date_added desc');
  $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
  $Qreviews->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qreviews->bindTable(':table_languages', TABLE_LANGUAGES);
  $Qreviews->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qreviews->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $Qreviews->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_ENTRIES); ?></td>
    <td align="right"><?php echo $Qreviews->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo TABLE_HEADING_PRODUCTS; ?></th>
      <th><?php echo TABLE_HEADING_LANGUAGE; ?></th>
      <th><?php echo TABLE_HEADING_RATING; ?></th>
      <th><?php echo TABLE_HEADING_DATE_ADDED; ?></th>
      <th width="150"><?php echo TABLE_HEADING_ACTION; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="5"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . IMAGE_DELETE . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ($Qreviews->next()) {
    if ( defined('SERVICE_REVIEW_ENABLE_MODERATION') && ( SERVICE_REVIEW_ENABLE_MODERATION != -1 ) ) {
      echo '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" ' . (($Qreviews->valueInt('reviews_status') !== 1) ? 'class="deactivatedRow"' : '') . '>';
    } else {
      echo '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">';
    }
?>

      <td onclick="document.getElementById('batch<?php echo $Qreviews->valueInt('reviews_id'); ?>').checked = !document.getElementById('batch<?php echo $Qreviews->valueInt('reviews_id'); ?>').checked;"><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&rID=' . $Qreviews->valueInt('reviews_id') . '&action=preview'), osc_image('images/icons/preview.gif', ICON_PREVIEW) . '&nbsp;' . $Qreviews->value('products_name')); ?></td>
      <td align="center"><?php echo osc_image('../includes/languages/' . $Qreviews->value('languages_code') . '/images/icon.gif', $Qreviews->value('languages_name')); ?></td>
      <td align="center"><?php echo osc_image('../images/stars_' . $Qreviews->valueInt('reviews_rating') . '.gif', sprintf(TEXT_OF_5_STARS, $Qreviews->valueInt('reviews_rating'))); ?></td>
      <td><?php echo osC_DateTime::getShort($Qreviews->value('date_added')); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&rID=' . $Qreviews->valueInt('reviews_id') . '&action=save'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&rID=' . $Qreviews->valueInt('reviews_id') . '&action=delete'), osc_icon('trash.png', IMAGE_DELETE));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qreviews->valueInt('reviews_id'), null, 'id="batch' . $Qreviews->valueInt('reviews_id') . '"'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . TEXT_LEGEND . '</b> ' . osc_icon('configure.png', IMAGE_EDIT) . '&nbsp;' . IMAGE_EDIT . '&nbsp;&nbsp;' . osc_icon('trash.png', IMAGE_DELETE) . '&nbsp;' . IMAGE_DELETE; ?></td>
    <td align="right"><?php echo $Qreviews->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>
