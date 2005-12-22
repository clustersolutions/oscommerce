<?php
/*
  $Id: product_reviews.php 212 2005-10-04 09:55:32 +0200 (Di, 04 Okt 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<h1 style="float: right;"><?php echo $osC_Product->getPriceFormated(true); ?></h1>

<h1><?php echo $osC_Template->getPageTitle() . ($osC_Product->hasModel() ? '<br /><span class="smallText">' . $osC_Product->getModel() . '</span>' : ''); ?></h1>

<?php
  if ($messageStack->size('reviews') > 0) {
    echo $messageStack->output('reviews');
  }

  if ($osC_Product->hasImage()) {
?>

<div style="float: right; padding-left: 30px;">
  <script type="text/javascript"><!--
    document.write('<?php echo '<a href="javascript:popupWindow(\\\'' . tep_href_link(FILENAME_POPUP_IMAGE, 'pID=' . $osC_Product->getID()) , '\\\')">' . tep_image(DIR_WS_IMAGES . $osC_Product->getImage(), addslashes($osC_Product->getTitle()), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '<br />' . TEXT_CLICK_TO_ENLARGE . '</a>'; ?>');
  //--></script>
  <noscript>
<?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES . $osC_Product->getImage()) . '" target="_blank">' . tep_image(DIR_WS_IMAGES . $osC_Product->getImage(), $osC_Product->getTitle(), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '<br />' . TEXT_CLICK_TO_ENLARGE . '</a>'; ?>
  </noscript>
<?php echo '<p><a href="' . tep_href_link(basename($_SERVER['PHP_SELF']), tep_get_all_get_params(array('action')) . 'action=buy_now') . '">' . tep_image_button('button_in_cart.gif', IMAGE_BUTTON_IN_CART) . '</a></p>'; ?>
</div>

<?php
  }
?>

<p><?php echo TEXT_AVERAGE_RATING . ' ' . tep_image(DIR_WS_IMAGES . 'stars_' . $osC_Product->getData('reviews_average_rating') . '.gif', sprintf(TEXT_OF_5_STARS, $osC_Product->getData('reviews_average_rating'))); ?></p>

<?php
  $counter = 0;
  $Qreviews = osC_Reviews::getListing($osC_Product->getID());
  while ($Qreviews->next()) {
    $counter++;

    if ($counter > 1) {
?>

<hr style="height: 1px; width: 150px; text-align: left; margin-left: 0px" />

<?php
    }
?>

<p><?php echo tep_image(DIR_WS_IMAGES . 'stars_' . $Qreviews->valueInt('reviews_rating') . '.gif', sprintf(TEXT_OF_5_STARS, $Qreviews->valueInt('reviews_rating'))) . '&nbsp;' . sprintf(TEXT_REVIEW_BY, $Qreviews->valueProtected('customers_name')) . '; ' . tep_date_long($Qreviews->value('date_added')); ?></p>

<p><?php echo nl2br(tep_break_string($Qreviews->valueProtected('reviews_text'), 60, '-<br />')); ?></p>

<?php
  }
?>

<div class="listingPageLinks">
  <span style="float: right;"><?php echo $Qreviews->displayBatchLinksPullDown('page', 'reviews'); ?></span>

  <?php echo $Qreviews->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?>
</div>

<div class="submitFormButtons">
<?php
  if ($osC_Reviews->is_enabled === true) {
    echo '  <span style="float: right;"><a href="' . tep_href_link(FILENAME_PRODUCTS, 'reviews=new&amp;' . $osC_Product->getKeyword()) . '">' . tep_image_button('button_write_review.gif', IMAGE_BUTTON_WRITE_REVIEW) . '</a></span>';
  }

  echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, $osC_Product->getKeyword()) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>';
?>
</div>
