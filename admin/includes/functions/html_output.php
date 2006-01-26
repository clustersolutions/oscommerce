<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  function tep_href_link($page = '', $parameters = '', $connection = 'SSL', $administration = true) {
    $path = ($administration === true) ? DIR_WS_CATALOG . 'admin/' : DIR_WS_CATALOG;

    $link = ((($connection == 'SSL') && (ENABLE_SSL == 'true') ) ? HTTPS_SERVER : HTTP_SERVER) . $path;

    if (empty($parameters)) {
      $link = $link . $page . '?' . SID;
    } else {
      $link = $link . $page . '?' . $parameters . '&' . SID;
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) {
      $link = substr($link, 0, -1);
    }

    return $link;
  }

////
// The HTML image wrapper function
  function tep_image($src, $alt = '', $width = '', $height = '', $params = '') {
    $image = '<img src="' . $src . '" border="0" alt="' . $alt . '"';
    if ($alt) {
      $image .= ' title=" ' . $alt . ' "';
    }
    if ($width) {
      $image .= ' width="' . $width . '"';
    }
    if ($height) {
      $image .= ' height="' . $height . '"';
    }
    if ($params) {
      $image .= ' ' . $params;
    }
    $image .= '>';

    return $image;
  }

  function tep_icon($image, $alt = '', $width = '', $height = '', $params = '') {
    global $template;

    return tep_image('templates/' . $template . '/images/icons/16x16/' . $image, $alt, $width, $height, $params);
  }

////
// The HTML form submit button wrapper function
// Outputs a button in the selected language
  function tep_image_submit($image, $alt = '', $parameters = '') {
    global $osC_Language;

    $image_submit = '<input type="image" src="' . tep_output_string('includes/languages/' . $osC_Language->getDirectory() . '/images/buttons/' . $image) . '" border="0" alt="' . tep_output_string($alt) . '"';

    if (tep_not_null($alt)) $image_submit .= ' title=" ' . tep_output_string($alt) . ' "';

    if (tep_not_null($parameters)) $image_submit .= ' ' . $parameters;

    $image_submit .= '>';

    return $image_submit;
  }

////
// Output a separator either through whitespace, or with an image
  function tep_draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1') {
    return tep_image('images/' . $image, '', $width, $height);
  }

////
// Output a function button in the selected language
  function tep_image_button($image, $alt = '', $params = '') {
    global $osC_Language;

    return tep_image('includes/languages/' . $osC_Language->getDirectory() . '/images/buttons/' . $image, $alt, '', '', $params);
  }

////
// javascript to dynamically update the states/provinces list when the country is changed
// TABLES: zones
  function tep_js_zone_list($country, $form, $field) {
    global $osC_Database;

    $num_country = 1;
    $output_string = '';

    $Qcountries = $osC_Database->query('select distinct zone_country_id from :table_zones order by zone_country_id');
    $Qcountries->bindTable(':table_zones', TABLE_ZONES);
    $Qcountries->execute();

    while ($Qcountries->next()) {
      if ($num_country == 1) {
        $output_string .= '  if (' . $country . ' == "' . $Qcountries->valueInt('zone_country_id') . '") {' . "\n";
      } else {
        $output_string .= '  } else if (' . $country . ' == "' . $Qcountries->valueInt('zone_country_id') . '") {' . "\n";
      }

      $num_state = 1;

      $Qzones = $osC_Database->query('select zone_name, zone_id from :table_zones where zone_country_id = :zone_country_id order by zone_name');
      $Qzones->bindTable(':table_zones', TABLE_ZONES);
      $Qzones->bindInt(':zone_country_id', $Qcountries->valueInt('zone_country_id'));
      $Qzones->execute();

      while ($Qzones->next()) {
        if ($num_state == '1') {
          $output_string .= '    ' . $form . '.' . $field . '.options[0] = new Option("' . PLEASE_SELECT . '", "");' . "\n";
        }

        $output_string .= '    ' . $form . '.' . $field . '.options[' . $num_state . '] = new Option("' . $Qzones->value('zone_name') . '", "' . $Qzones->valueInt('zone_id') . '");' . "\n";

        $num_state++;
      }

      $num_country++;
    }

    $output_string .= '  } else {' . "\n" .
                      '    ' . $form . '.' . $field . '.options[0] = new Option("' . TYPE_BELOW . '", "");' . "\n" .
                      '  }' . "\n";

    return $output_string;
  }

////
// Output a form
  function tep_draw_form($name, $action, $parameters = '', $method = 'post', $params = '') {
    $form = '<form name="' . tep_output_string($name) . '" action="';
    if (tep_not_null($parameters)) {
      $form .= tep_href_link($action, $parameters);
    } else {
      $form .= tep_href_link($action);
    }
    $form .= '" method="' . tep_output_string($method) . '"';
    if (tep_not_null($params)) {
      $form .= ' ' . $params;
    }
    $form .= '>';

    return $form;
  }

////
// Output a form input field
  function tep_draw_input_field($name, $value = '', $parameters = '', $required = false, $type = 'text', $reinsert_value = true) {
    $field = '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';

    if (isset($GLOBALS[$name]) && ($reinsert_value == true) && is_string($GLOBALS[$name])) {
      $field .= ' value="' . tep_output_string($GLOBALS[$name]) . '"';
    } elseif (tep_not_null($value)) {
      $field .= ' value="' . tep_output_string($value) . '"';
    }

    if (tep_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    if ($required == true) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }

////
// Output a form password field
  function tep_draw_password_field($name, $value = '', $required = false) {
    $field = tep_draw_input_field($name, $value, 'maxlength="40"', $required, 'password', false);

    return $field;
  }

////
// Output a form filefield
  function tep_draw_file_field($name, $required = false) {
    $field = tep_draw_input_field($name, '', '', $required, 'file');

    return $field;
  }

  function osc_draw_file_field($name, $max_size = true, $required = false) {
    static $upload_max_filesize;

    $field = osc_draw_input_field($name, '', '', $required, 'file');

    if (is_bool($max_size) && ($max_size === true)) {
      if (!isset($upload_max_filesize)) {
        $upload_max_filesize = @ini_get('upload_max_filesize');
      }

      $max_size = $upload_max_filesize;
    }

    if (!empty($max_size)) {
      $field .= '&nbsp;' . sprintf(MAXIMUM_FILE_UPLOAD_SIZE, tep_output_string($max_size));
    }

    return $field;
  }

////
// Output a selection field - alias function for tep_draw_checkbox_field() and tep_draw_radio_field()
  function tep_draw_selection_field($name, $type, $value = '', $checked = false, $compare = '') {
    $selection = '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';

    if (tep_not_null($value)) $selection .= ' value="' . tep_output_string($value) . '"';

    if ( ($checked == true) || (isset($GLOBALS[$name]) && is_string($GLOBALS[$name]) && ($GLOBALS[$name] == 'on')) || (isset($value) && isset($GLOBALS[$name]) && ($GLOBALS[$name] == $value)) || (tep_not_null($value) && tep_not_null($compare) && ($value == $compare)) ) {
      $selection .= ' checked="checked"';
    }

    $selection .= '>';

    return $selection;
  }

////
// Output a form checkbox field
  function tep_draw_checkbox_field($name, $value = '', $checked = false, $compare = '') {
    return tep_draw_selection_field($name, 'checkbox', $value, $checked, $compare);
  }

////
// Output a form radio field
  function tep_draw_radio_field($name, $value = '', $checked = false, $compare = '') {
    return tep_draw_selection_field($name, 'radio', $value, $checked, $compare);
  }

////
// Output a form textarea field
  function tep_draw_textarea_field($name, $wrap, $width, $height, $text = '', $parameters = '', $reinsert_value = true) {
    $field = '<textarea name="' . tep_output_string($name) . '" wrap="' . tep_output_string($wrap) . '" cols="' . tep_output_string($width) . '" rows="' . tep_output_string($height) . '"';

    if (tep_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    if ( (isset($GLOBALS[$name])) && ($reinsert_value == true) ) {
      $field .= $GLOBALS[$name];
    } elseif (tep_not_null($text)) {
      $field .= $text;
    }

    $field .= '</textarea>';

    return $field;
  }

  function osc_draw_textarea_field($name, $value = '', $width = '60', $height = '5', $wrap = 'soft', $parameters = '', $reinsert_value = true, $required = false) {
    if ($reinsert_value === true) {
      if (isset($_GET[$name])) {
        $value = $_GET[$name];
      } elseif (isset($_POST[$name])) {
        $value = $_POST[$name];
      }
    }

    $field = '<textarea name="' . tep_output_string($name) . '" wrap="' . tep_output_string($wrap) . '" cols="' . tep_output_string($width) . '" rows="' . tep_output_string($height) . '"';

    if (!empty($parameters)) {
      $field .= ' ' . $parameters;
    }

    $field .= '>' . $value . '</textarea>';

    if ($required === true) {
      $field .= '&nbsp;<span class="inputRequirement">*</span>';
    }

    return $field;
  }

////
// Output a form hidden field
  function tep_draw_hidden_field($name, $value = '', $parameters = '') {
    $field = '<input type="hidden" name="' . tep_output_string($name) . '"';

    if (tep_not_null($value)) {
      $field .= ' value="' . tep_output_string($value) . '"';
    } elseif (isset($GLOBALS[$name]) && is_string($GLOBALS[$name])) {
      $field .= ' value="' . tep_output_string($GLOBALS[$name]) . '"';
    }

    if (tep_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    return $field;
  }

////
// Output a form pull down menu
  function tep_draw_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false) {
    $field = '<select name="' . tep_output_string($name) . '"';

    if (tep_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    if (empty($default) && isset($GLOBALS[$name])) $default = $GLOBALS[$name];

    for ($i=0, $n=sizeof($values); $i<$n; $i++) {
      $field .= '<option value="' . tep_output_string($values[$i]['id']) . '"';
      if ($default == $values[$i]['id']) {
        $field .= ' selected="selected"';
      }

      $field .= '>' . tep_output_string($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
    }
    $field .= '</select>';

    if ($required == true) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }

  function osc_draw_selection_field($name, $type, $values, $default = '', $parameters = '', $required = false, $separator = '&nbsp;&nbsp;') {
    if (!is_array($values)) {
      $values = array($values);
    }

    if (isset($_GET[$name])) {
      $default = $_GET[$name];
    } elseif (isset($_POST[$name])) {
      $default = $_POST[$name];
    }

    $field = '';

    $counter = 0;
    foreach ($values as $key => $value) {
      $counter++;

      if (is_array($value)) {
        $selection_value = $value['id'];
        $selection_text = '<label for="' . tep_output_string($name) . '_' . $counter . '">&nbsp;' . $value['text'] . '</label>';
      } else {
        $selection_value = $value;
        $selection_text = '';
      }

      $field .= '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';

      if (!empty($selection_value)) {
        $field .= ' value="' . tep_output_string($selection_value) . '"';
      }

      if ((is_bool($default) && $default === true) || (!empty($default) && ($default == $selection_value))) {
        $field .= ' checked="checked"';
      }

      if (!empty($parameters)) {
        $field .= ' ' . $parameters;
      }

      $field .= ' id="' . tep_output_string($name) . '_' . $counter . '">' . $selection_text . $separator;
    }

    $field = substr($field, 0, strlen($field)-strlen($separator));

    if ($required === true) {
      $field .= '&nbsp;<span class="inputRequirement">*</span>';
    }

    return $field;
  }

  function osc_draw_checkbox_field($name, $values = '', $default = '', $parameters = '', $required = false, $separator = '&nbsp;&nbsp;') {
    return osc_draw_selection_field($name, 'checkbox', $values, $default, $parameters, $required, $separator);
  }

  function osc_draw_radio_field($name, $values = '', $default = '', $parameters = '', $required = false, $separator = '&nbsp;&nbsp;') {
    return osc_draw_selection_field($name, 'radio', $values, $default, $parameters, $required, $separator);
  }

  function osc_draw_input_field($name, $value = '', $parameters = '', $required = false, $type = 'text', $reinsert_value = true) {
    $field_value = $value;

    $field = '<input type="' . tep_output_string($type) . '" name="' . tep_output_string($name) . '"';

    if ($reinsert_value === true) {
      if (isset($_GET[$name])) {
        $field_value = $_GET[$name];
      } elseif (isset($_POST[$name])) {
        $field_value = $_POST[$name];
      }
    }

    if (strlen(trim($field_value)) > 0) {
      $field .= ' value="' . tep_output_string($field_value) . '"';
    }

    if (!empty($parameters)) {
      $field .= ' ' . $parameters;
    }

    $field .= '>';

    if ($required === true) {
      $field .= '&nbsp;<span class="inputRequirement">*</span>';
    }

    return $field;
  }

  function osc_draw_password_field($name, $value = '', $parameters = '', $required = false) {
    return osc_draw_input_field($name, $value, $parameters, $required, 'password', false);
  }

  function osc_draw_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false) {
    $group = false;

    $field = '<select name="' . tep_output_string($name) . '"';

    if (!empty($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    if (isset($_GET[$name])) {
      $default = $_GET[$name];
    } elseif (isset($_POST[$name])) {
      $default = $_POST[$name];
    }

    for ($i=0, $n=sizeof($values); $i<$n; $i++) {
      if (isset($values[$i]['group'])) {
        if ($group != $values[$i]['group']) {
          $group = $values[$i]['group'];
          $field .= '<optgroup label="' . tep_output_string($values[$i]['group']) . '">';
        }
      }
      $field .= '<option value="' . tep_output_string($values[$i]['id']) . '"';

      if ( (is_string($default) && ($default == $values[$i]['id'])) || (is_array($default) && in_array($values[$i]['id'], $default)) ) {
        $field .= ' selected="selected"';
      }

      $field .= '>' . tep_output_string($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';

      if (($group !== false) && ( ($group != $values[$i]['group']) || (isset($values[$i+1]) === false) )) {
        $group = false;

        $field .= '</optgroup>';
      }
    }

    $field .= '</select>';

    if ($required === true) {
      $field .= '&nbsp;<span class="inputRequirement">*</span>';
    }

    return $field;
  }

  function osc_draw_hidden_field($name, $value = '', $parameters = '') {
    if (empty($value)) {
      if (isset($_GET[$name])) {
        $value = $_GET[$name];
      } elseif (isset($_POST[$name])) {
        $value = $_POST[$name];
      }
    }

    $field = '<input type="hidden" name="' . tep_output_string($name) . '"';

    if (!empty($value)) {
      $field .= ' value="' . tep_output_string($value) . '"';
    }

    if (!empty($parameters)) {
      $field .= ' ' . $parameters;
    }

    $field .= '>';

    return $field;
  }

  function tep_draw_date_pull_down_menu($name, $value = '', $default_today = true, $show_days = true, $use_month_names = true, $year_range_start = '0', $year_range_end  = '1') {
    $params = '';

// days pull down menu
    $days_select_string = '';

    if ($show_days === true) {
      $params = 'onchange="updateDatePullDownMenu(this.form, \'' . $name . '\');"';

      $days_in_month = ($default_today === true) ? date('t') : 31;

      $days_array = array();
      for ($i=1; $i<=$days_in_month; $i++) {
        $days_array[] = array('id' => $i,
                              'text' => $i);
      }

      if (isset($GLOBALS[$name . '_days'])) {
        $days_default = $GLOBALS[$name . '_days'];
      } elseif (!empty($value)) {
        $days_default = date('j', $value);
      } elseif ($default_today === true) {
        $days_default = date('j');
      } else {
        $days_default = 1;
      }

      $days_select_string = osc_draw_pull_down_menu($name . '_days', $days_array, $days_default);
    }

// months pull down menu
    $months_array = array();
    for ($i=1; $i<=12; $i++) {
      $months_array[] = array('id' => $i,
                              'text' => (($use_month_names === true) ? strftime('%B', mktime(0, 0, 0, $i)) : $i));
    }

    if (isset($GLOBALS[$name . '_months'])) {
      $months_default = $GLOBALS[$name . '_months'];
    } elseif (!empty($value)) {
      $months_default = date('n', $value);
    } elseif ($default_today === true) {
      $months_default = date('n');
    } else {
      $months_default = 1;
    }

    $months_select_string = osc_draw_pull_down_menu($name . '_months', $months_array, $months_default, $params);

// year pull down menu
    $year = date('Y');

    $years_array = array();
    for ($i = ($year - $year_range_start); $i <= ($year + $year_range_end); $i++) {
      $years_array[] = array('id' => $i,
                             'text' => $i);
    }

    if (isset($GLOBALS[$name . '_years'])) {
      $years_default = $GLOBALS[$name . '_years'];
    } elseif (!empty($value)) {
      $years_default = date('Y', $value);
    } elseif ($default_today === true) {
      $years_default = $year;
    } else {
      $years_default = $year - $year_range_start;
    }

    $years_select_string = osc_draw_pull_down_menu($name . '_years', $years_array, $years_default, $params);

    return $days_select_string . $months_select_string . $years_select_string;
  }
?>
