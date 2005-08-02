<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  if (empty($_POST)) {
    $Qreviews = $osC_Database->query('select r.reviews_id, r.products_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read, r.reviews_text, r.reviews_rating, p.products_image, pd.products_name from :table_reviews r left join :table_products_description pd on (r.products_id = pd.products_id and r.languages_id = pd.language_id) left join :table_products p on (r.products_id = p.products_id) where r.reviews_id = :reviews_id');
    $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
    $Qreviews->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qreviews->bindTable(':table_products', TABLE_PRODUCTS);
    $Qreviews->bindInt(':reviews_id', $_GET['rID']);
    $Qreviews->execute();

    $rInfo = new objectInfo($Qreviews->toArray());
  } else {
    $Qreview = $osC_Database->query('select r.customers_name, r.date_added, p.products_image, pd.products_name from :table_reviews r, :table_products p, :table_products_description pd where r.reviews_id = :reviews_id and r.products_id = p.products_id and p.products_id = pd.products_id and r.languages_id = pd.language_id');
    $Qreview->bindTable(':table_reviews', TABLE_REVIEWS);
    $Qreview->bindTable(':table_products', TABLE_PRODUCTS);
    $Qreview->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qreview->bindInt(':reviews_id', $_GET['rID']);
    $Qreview->execute();

    $rInfo = new objectInfo(array_merge($_POST, $Qreview->toArray()));
  }

  $rating_array = array();
  for ($i=1; $i<=5; $i++) {
    $rating_array[] = array('id' => $i, 'text' => '');
  }
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<?php echo tep_draw_form('review', FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=rPreview'); ?>

<p class="main"><?php echo tep_image('../images/' . $rInfo->products_image, $rInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'align="right" hspace="5" vspace="5"') . '<b>' . ENTRY_PRODUCT . '</b> ' . $rInfo->products_name . '<br><b>' . ENTRY_FROM . '</b> ' . $rInfo->customers_name . '<br><br><b>' . ENTRY_DATE . '</b> ' . tep_date_short($rInfo->date_added); ?></p>

<p class="main"><?php echo '<b>' . ENTRY_REVIEW . '</b><br>' . tep_draw_textarea_field('reviews_text', 'soft', '60', '15', $rInfo->reviews_text, 'style="width: 100%;"') . '<br><span class="smallText">' . ENTRY_REVIEW_TEXT . '</span>'; ?></p>

<p class="main"><?php echo '<b>' . ENTRY_RATING . '</b>&nbsp;' . TEXT_BAD . '&nbsp;' . osc_draw_radio_field('reviews_rating', $rating_array, $rInfo->reviews_rating) . '&nbsp;' . TEXT_GOOD; ?></p>

<p class="main" align="right"><?php echo '<input type="submit" value="' . IMAGE_PREVIEW . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="document.location.href=\'' . tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID']) . '\';" class="operationButton">'; ?></p>

</form>
