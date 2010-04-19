<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Module_IndexModules_Reviews extends OSCOM_Site_Admin_Application_Index_IndexModules {
    public function __construct() {
      OSCOM_Registry::get('osC_Language')->loadIniFile('modules/IndexModules/Reviews.php');

      $this->_title = OSCOM::getDef('admin_indexmodules_reviews_title');
      $this->_title_link = OSCOM::getLink(null, 'Reviews');

      if ( osC_Access::hasAccess(OSCOM::getSite(), 'reviews') ) {
        $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                       '  <thead>' .
                       '    <tr>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_reviews_table_heading_products') . '</th>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_reviews_table_heading_language') . '</th>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_reviews_table_heading_rating') . '</th>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_reviews_table_heading_date') . '</th>' .
                       '    </tr>' .
                       '  </thead>' .
                       '  <tbody>';

        $Qreviews = OSCOM_Registry::get('Database')->query('select r.reviews_id, r.products_id, greatest(r.date_added, greatest(r.date_added, r.last_modified)) as date_last_modified, r.reviews_rating, pd.products_name, l.name as languages_name, l.code as languages_code from :table_reviews r left join :table_products_description pd on (r.products_id = pd.products_id and r.languages_id = pd.language_id), :table_languages l where r.languages_id = l.languages_id order by date_last_modified desc limit 6');
        $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
        $Qreviews->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
        $Qreviews->bindTable(':table_languages', TABLE_LANGUAGES);
        $Qreviews->execute();

        $counter = 0;
 
        while ( $Qreviews->next() ) {
          $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');"' . ($counter % 2 ? ' class="alt"' : '') . '>' .
                          '      <td>' . osc_link_object(OSCOM::getLink(null, 'Reviews', 'rID=' . $Qreviews->valueInt('reviews_id') . '&action=save'), osc_icon('reviews.png') . '&nbsp;' . $Qreviews->value('products_name')) . '</td>' .
                          '      <td align="center">' . OSCOM_Registry::get('osC_Language')->showImage($Qreviews->value('languages_code')) . '</td>' .
                          '      <td align="center">' . osc_image('../images/stars_' . $Qreviews->valueInt('reviews_rating') . '.png', $Qreviews->valueInt('reviews_rating') . '/5') . '</td>' .
                          '      <td>' . $Qreviews->value('date_last_modified') . '</td>' .
                          '    </tr>';

          $counter++;
        }

        $this->_data .= '  </tbody>' .
                        '</table>';
      }
    }
  }
?>
