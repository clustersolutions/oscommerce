<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  if ($osC_Services->isStarted('reviews')) {

?>
<!-- reviews //-->
          <tr>
            <td>
<?php
    $info_box_contents = array();
    $info_box_contents[] = array('text' => BOX_HEADING_REVIEWS);

    new infoBoxHeading($info_box_contents, false, false, tep_href_link(FILENAME_REVIEWS));

    $info_box_contents = array();

    $random_query = 'select r.reviews_id, r.reviews_rating, p.products_id, p.products_image, pd.products_name from :table_reviews r, :table_products p, :table_products_description pd where r.products_id = p.products_id and p.products_status = 1 and r.languages_id = :language_id and p.products_id = pd.products_id and pd.language_id = :language_id and r.reviews_status = 1';
    if (isset($_GET['products_id']) && is_numeric($_GET['products_id'])) {
      $random_query .= ' and p.products_id = :products_id';
    }
    $random_query .= ' order by r.reviews_id desc limit :max_random_select_reviews';

    $Qreviews = $osC_Database->query($random_query);
    $Qreviews->bindRaw(':table_reviews', TABLE_REVIEWS);
    $Qreviews->bindRaw(':table_products', TABLE_PRODUCTS);
    $Qreviews->bindRaw(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qreviews->bindInt(':language_id', $osC_Session->value('languages_id'));
    $Qreviews->bindInt(':language_id', $osC_Session->value('languages_id'));
    $Qreviews->bindRaw(':max_random_select_reviews', MAX_RANDOM_SELECT_REVIEWS);

    if (isset($_GET['products_id']) && is_numeric($_GET['products_id'])) {
      $Qreviews->bindInt(':products_id', $_GET['products_id']);
    }

    if ($Qreviews->executeRandomMulti()) {
// display random review box
      $Qreview_text = $osC_Database->query('select substring(reviews_text, 1, 60) as reviews_text from :table_reviews where reviews_id = :reviews_id and languages_id = :languages_id');
      $Qreview_text->bindRaw(':table_reviews', TABLE_REVIEWS);
      $Qreview_text->bindInt(':reviews_id', $Qreviews->valueInt('reviews_id'));
      $Qreview_text->bindInt(':languages_id', $osC_Session->value('languages_id'));
      $Qreview_text->execute();

      $review_text = tep_break_string($Qreview_text->valueProtected('reviews_text'), 15, '-<br>');

      $info_box_contents[] = array('text' => '<div align="center"><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $Qreviews->valueInt('products_id') . '&reviews_id=' . $Qreviews->valueInt('reviews_id')) . '">' . tep_image(DIR_WS_IMAGES . $Qreviews->value('products_image'), $Qreviews->value('products_name'), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></div><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $Qreviews->valueInt('products_id') . '&reviews_id=' . $Qreviews->valueInt('reviews_id')) . '">' . $review_text . ' ..</a><br><div align="center">' . tep_image(DIR_WS_IMAGES . 'stars_' . $Qreviews->valueInt('reviews_rating') . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $Qreviews->valueInt('reviews_rating'))) . '</div>');

      $Qreview_text->freeResult();
      $Qreviews->freeResult();
    } elseif (isset($_GET['products_id']) && is_numeric($_GET['products_id'])) {
// display 'write a review' box
      $info_box_contents[] = array('text' => '<table border="0" cellspacing="0" cellpadding="2"><tr><td class="infoBoxContents"><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'products_id=' . $_GET['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'box_write_review.gif', IMAGE_BUTTON_WRITE_REVIEW) . '</a></td><td class="infoBoxContents"><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'products_id=' . $_GET['products_id']) . '">' . BOX_REVIEWS_WRITE_REVIEW .'</a></td></tr></table>');
    } else {
// display 'no reviews' box
      $info_box_contents[] = array('text' => BOX_REVIEWS_NO_REVIEWS);
    }

    new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- reviews_eof //-->
<?php
  }
?>
