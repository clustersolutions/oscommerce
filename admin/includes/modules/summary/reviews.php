<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  if (!class_exists('osC_Summary')) {
    include('includes/classes/summary.php');
  }

  if (!defined('MODULE_SUMMARY_REVIEWS_TITLE')) {
    $osC_Language->loadConstants('modules/summary/reviews.php');
  }

  class osC_Summary_reviews extends osC_Summary {

/* Class constructor */

    function osC_Summary_reviews() {
      $this->_title = MODULE_SUMMARY_REVIEWS_TITLE;
      $this->_title_link = osc_href_link_admin(FILENAME_DEFAULT, 'reviews');

      $this->_setData();
    }

/* Private methods */

    function _setData() {
      global $osC_Database;

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

      $Qreviews = $osC_Database->query('select r.reviews_id, r.products_id, greatest(r.date_added, ifnull(r.last_modified, 0)) as date_last_modified, r.reviews_rating, pd.products_name, l.name as languages_name, l.code as languages_code from :table_reviews r left join :table_products_description pd on (r.products_id = pd.products_id and r.languages_id = pd.language_id), :table_languages l where r.languages_id = l.languages_id order by date_last_modified desc limit 6');
      $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
      $Qreviews->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qreviews->bindTable(':table_languages', TABLE_LANGUAGES);
      $Qreviews->execute();

      while ($Qreviews->next()) {
        $this->_data .= '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' .
                        '      <td>' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, 'reviews&rID=' . $Qreviews->valueInt('reviews_id') . '&action=rEdit'), osc_icon('write.png', ICON_PREVIEW) . '&nbsp;' . $Qreviews->value('products_name')) . '</td>' .
                        '      <td align="center">' . osc_image('../includes/languages/' . $Qreviews->value('languages_code') . '/images/icon.gif', $Qreviews->value('languages_name')) . '</td>' .
                        '      <td align="center">' . osc_image('../images/stars_' . $Qreviews->valueInt('reviews_rating') . '.gif', $Qreviews->valueInt('reviews_rating') . '/5') . '</td>' .
                        '      <td>' . $Qreviews->value('date_last_modified') . '</td>' .
                        '    </tr>';
      }

      $Qreviews->freeResult();

      $this->_data .= '  </tbody>' .
                      '</table>';
    }
  }
?>
