<?php
/*
  $Id: languages.php,v 1.18 2004/11/24 15:33:37 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

 if (sizeof($osC_Language->getAll() > 1)) {
?>
<!-- languages //-->
          <tr>
            <td>
<?php
  $info_box_contents = array();
  $info_box_contents[] = array('text' => BOX_HEADING_LANGUAGES);

  new infoBoxHeading($info_box_contents, false, false);

  $languages_string = '';
  foreach ($osC_Language->getAll() as $language) {
    $languages_string .= ' <a href="' . tep_href_link(basename($_SERVER['PHP_SELF']), tep_get_all_get_params(array('language', 'currency')) . 'language=' . $language['code'], $request_type) . '">' . tep_image(DIR_WS_LANGUAGES .  $language['directory'] . '/images/' . $language['image'], $language['name']) . '</a> ';
  }

  $info_box_contents = array();
  $info_box_contents[] = array('align' => 'center',
                               'text' => $languages_string);

  new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- languages_eof //-->
<?php
  }
?>
