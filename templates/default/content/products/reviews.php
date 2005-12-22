<?php
/*
  $Id: index.php 199 2005-09-22 17:56:13 +0200 (Do, 22 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . $osC_Template->getPageImage(), $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  $Qreviews = osC_Reviews::getListing();
  while ($Qreviews->next()) {
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, 'reviews=' . $Qreviews->valueInt('reviews_id') . '&amp;' . $Qreviews->value('products_keyword')) . '"><u><b>' . $Qreviews->value('products_name') . '</b></u></a> <span class="smallText">' . sprintf(TEXT_REVIEW_BY, $Qreviews->valueProtected('customers_name')) . '</span>'; ?></td>
    <td class="smallText" align="right"><?php echo sprintf(TEXT_REVIEW_DATE_ADDED, tep_date_long($Qreviews->value('date_added'))); ?></td>
  </tr>
</table>

<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
  <tr class="infoBoxContents">
    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
        <td width="<?php echo SMALL_IMAGE_WIDTH + 10; ?>" align="center" valign="top" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, 'reviews=' . $Qreviews->valueInt('reviews_id') . '&amp;' . $Qreviews->value('products_keyword')) . '">' . tep_image(DIR_WS_IMAGES . $Qreviews->value('products_image'), $Qreviews->value('products_name'), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>'; ?></td>
        <td valign="top" class="main"><?php echo tep_break_string($Qreviews->valueProtected('reviews_text'), 60, '-<br />') . ((strlen($Qreviews->valueProtected('reviews_text')) >= 100) ? '..' : '') . '<br /><br /><i>' . sprintf(TEXT_REVIEW_RATING, tep_image(DIR_WS_IMAGES . 'stars_' . $Qreviews->valueInt('reviews_rating') . '.gif', sprintf(TEXT_OF_5_STARS, $Qreviews->valueInt('reviews_rating'))), sprintf(TEXT_OF_5_STARS, $Qreviews->valueInt('reviews_rating'))) . '</i>'; ?></td>
        <td width="10" align="right"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
      </tr>
    </table></td>
  </tr>
</table>

<?php
  }
?>

<div class="listingPageLinks">
  <span style="float: right;"><?php echo $Qreviews->displayBatchLinksPullDown('page', 'reviews'); ?></span>

  <?php echo $Qreviews->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?>
</div>
