<?php
/*
  $Id: reviews.php,v 1.4 2004/11/07 20:38:51 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  if (!class_exists('osC_Summary')) {
    include('includes/classes/summary.php');
  }

  if (!defined('MODULE_SUMMARY_REVIEWS_TITLE')) {
    include('includes/languages/' . $osC_Session->value('language') . '/modules/summary/reviews.php');
  }

  class osC_Summary_reviews extends osC_Summary {

/* Class constructor */

    function osC_Summary_reviews() {
      $this->_title = MODULE_SUMMARY_REVIEWS_TITLE;
      $this->_title_link = tep_href_link(FILENAME_REVIEWS);

      $this->_setData();
    }

/* Private methods */

    function _setData() {
      global $osC_Database, $template;

      $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                     '  <thead>' .
                     '    <tr>' .
                     '      <th>' . MODULE_SUMMARY_REVIEWS_HEADING_PRODUCTS . '</th>' .
                     '      <th>' . MODULE_SUMMARY_REVIEWS_HEADING_LANGUAGE . '</th>' .
                     '      <th>' . MODULE_SUMMARY_REVIEWS_HEADING_RATING . '</th>' .
                     '      <th>' . MODULE_SUMMARY_REVIEWS_HEADING_DATE . '</th>' .
                     '    </tr>' .
                     '  </thead>' .
                     '  <tbody>';

      $Qreviews = $osC_Database->query('select r.reviews_id, r.products_id, greatest(r.date_added, r.last_modified) as date_last_modified, r.reviews_rating, pd.products_name, l.name as languages_name, l.directory as languages_directory, l.image as languages_image from :table_reviews r left join :table_products_description pd on (r.products_id = pd.products_id and r.languages_id = pd.language_id), :table_languages l where r.languages_id = l.languages_id order by date_last_modified desc limit 6');
      $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
      $Qreviews->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qreviews->bindTable(':table_languages', TABLE_LANGUAGES);
      $Qreviews->execute();

      while ($Qreviews->next()) {
        $this->_data .= '    <tr onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);">' .
                        '      <td><a href="' . tep_href_link(FILENAME_REVIEWS, 'rID=' . $Qreviews->valueInt('reviews_id') . '&action=rEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/write.png', ICON_PREVIEW, '16', '16') . '&nbsp;' . $Qreviews->value('products_name') . '</a></td>' .
                        '      <td align="center">' . tep_image('../includes/languages/' . $Qreviews->value('languages_directory') . '/images/' . $Qreviews->value('languages_image'), $Qreviews->value('languages_name')) . '</td>' .
                        '      <td align="center">' . tep_image('../images/stars_' . $Qreviews->valueInt('reviews_rating') . '.gif', $Qreviews->valueInt('reviews_rating') . '/5') . '</td>' .
                        '      <td>' . $Qreviews->value('date_last_modified') . '</td>' .
                        '    </tr>';
      }

      $Qreviews->freeResult();

      $this->_data .= '  </tbody>' .
                      '</table>';
    }
  }
?>
