<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  $Qmanufacturers = $osC_Database->query('select manufacturers_id, manufacturers_name from :table_manufacturers order by manufacturers_name');
  $Qmanufacturers->bindRaw(':table_manufacturers', TABLE_MANUFACTURERS);

  $Qmanufacturers->setCache('manufacturers');

  $Qmanufacturers->execute();

  if ($Qmanufacturers->numberOfRows() > 0) {
?>
<!-- manufacturers //-->
          <tr>
            <td>
<?php
    $info_box_contents = array();
    $info_box_contents[] = array('text' => BOX_HEADING_MANUFACTURERS);

    new infoBoxHeading($info_box_contents, false, false);

    if ($Qmanufacturers->numberOfRows() <= MAX_DISPLAY_MANUFACTURERS_IN_A_LIST) {
// Display a list
      $manufacturers_list = '';
      while ($Qmanufacturers->next()) {
        if (strlen($Qmanufacturers->value('manufacturers_name')) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) {
          $manufacturers_name = substr($Qmanufacturers->value('manufacturers_name'), 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN) . '..';
        } else {
          $manufacturers_name = $Qmanufacturers->value('manufacturers_name');
        }

        if (isset($_GET['manufacturers_id']) && ($_GET['manufacturers_id'] == $Qmanufacturers->valueInt('manufacturers_id'))) {
          $manufacturers_name = '<b>' . $manufacturers_name .'</b>';
        }

        $manufacturers_list .= '<a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $Qmanufacturers->valueInt('manufacturers_id')) . '">' . $manufacturers_name . '</a><br>';
      }

      $manufacturers_list = substr($manufacturers_list, 0, -4);

      $info_box_contents = array();
      $info_box_contents[] = array('text' => $manufacturers_list);
    } else {
// Display a drop-down
      $manufacturers_array = array();

      if (MAX_MANUFACTURERS_LIST < 2) {
        $manufacturers_array[] = array('id' => '', 'text' => PULL_DOWN_DEFAULT);
      }

      while ($Qmanufacturers->next()) {
        if (strlen($Qmanufacturers->value('manufacturers_name')) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) {
          $manufacturers_name = substr($Qmanufacturers->value('manufacturers_name'), 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN) . '..';
        } else {
          $manufacturers_name = $Qmanufacturers->value('manufacturers_name');
        }

        $manufacturers_array[] = array('id' => $Qmanufacturers->valueInt('manufacturers_id'),
                                       'text' => $manufacturers_name);
      }

      $info_box_contents = array();
      $info_box_contents[] = array('form' => tep_draw_form('manufacturers', tep_href_link(FILENAME_DEFAULT, '', 'NONSSL', false), 'get'),
                                   'text' => osc_draw_pull_down_menu('manufacturers_id', $manufacturers_array, (isset($_GET['manufacturers_id']) ? $_GET['manufacturers_id'] : ''), 'onChange="this.form.submit();" size="' . MAX_MANUFACTURERS_LIST . '" style="width: 100%"') . tep_hide_session_id());
    }

    new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- manufacturers_eof //-->
<?php
  }

  $Qmanufacturers->freeResult();
?>
