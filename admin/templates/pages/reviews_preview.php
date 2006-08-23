<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  if (empty($_POST)) {
    $Qreviews = $osC_Database->query('select r.reviews_id, r.products_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read, r.reviews_text, r.reviews_rating, pd.products_name from :table_reviews r left join :table_products_description pd on (r.products_id = pd.products_id and r.languages_id = pd.language_id) left join :table_products p on (r.products_id = p.products_id) where r.reviews_id = :reviews_id');
    $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
    $Qreviews->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qreviews->bindTable(':table_products', TABLE_PRODUCTS);
    $Qreviews->bindInt(':reviews_id', $_GET['rID']);
    $Qreviews->execute();

    $rInfo = new objectInfo($Qreviews->toArray());
  } else {
    $Qreview = $osC_Database->query('select r.customers_name, r.date_added, pd.products_name from :table_reviews r, :table_products p, :table_products_description pd where r.reviews_id = :reviews_id and r.products_id = p.products_id and p.products_id = pd.products_id and r.languages_id = pd.language_id');
    $Qreview->bindTable(':table_reviews', TABLE_REVIEWS);
    $Qreview->bindTable(':table_products', TABLE_PRODUCTS);
    $Qreview->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qreview->bindInt(':reviews_id', $_GET['rID']);
    $Qreview->execute();

    $rInfo = new objectInfo(array_merge($_POST, $Qreview->toArray()));
  }
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<p class="main"><?php echo '<b>' . ENTRY_PRODUCT . '</b> ' . $rInfo->products_name . '<br /><b>' . ENTRY_FROM . '</b> ' . $rInfo->customers_name . '<br /><br /><b>' . ENTRY_DATE . '</b> ' . osC_DateTime::getShort($rInfo->date_added); ?></p>

<p class="main"><?php echo '<b>' . ENTRY_REVIEW . '</b><br />' . nl2br(osc_output_string_protected($rInfo->reviews_text)); ?></p>

<p class="main"><?php echo '<b>' . ENTRY_RATING . '</b>&nbsp;' . osc_image('../images/stars_' . $rInfo->reviews_rating . '.gif', sprintf(TEXT_OF_5_STARS, $rInfo->reviews_rating)) . '&nbsp;<small>[' . sprintf(TEXT_OF_5_STARS, $rInfo->reviews_rating) . ']</small>'; ?></p>

<?php
  if (empty($_POST)) {
    echo '<p align="right"><input type="button" value="' . IMAGE_BACK . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID']) . '\';"></p>';
    if ( (defined('SERVICE_REVIEW_ENABLE_MODERATION')) && (SERVICE_REVIEW_ENABLE_MODERATION != -1) ) {
      echo '<p align="right"><input type="button" value="' . IMAGE_APPROVE . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=rApprove') . '\';"> <input type="button" value="' . IMAGE_REJECT . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=rReject') . '\';"></p>';
    }
  } else {
    echo '<form name="update" action="' . osc_href_link_admin(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=update') . '" method="post" enctype="multipart/form-data">';

    foreach ($_POST as $key => $value) {
      echo osc_draw_hidden_field($key, $value);
    }

    echo '<p align="right"><input type="submit" value="' . IMAGE_BACK . '" name="review_edit" class="operationButton"> <input type="submit" value="' . IMAGE_UPDATE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $_GET['rID']) . '\';"></p>';

    echo '</form>';
  }
?>
