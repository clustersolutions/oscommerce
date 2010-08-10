<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;

/**
 * Wrapper function for set_time_limit(), which can't be used in safe_mode
 *
 * @param int $limit The limit to set the maximium execution time to
 * @access public
 */

  function osc_set_time_limit($limit) {
    if (!get_cfg_var('safe_mode')) {
      set_time_limit($limit);
    }
  }

/**
 * Redirect to a URL address
 *
 * @param string $url The URL address to redirect to
 * @access public
 */

  function osc_redirect_admin($url) {
    if ( (strpos($url, "\n") !== false) || (strpos($url, "\r") !== false) ) {
      $url = osc_href_link_admin(FILENAME_DEFAULT);
    }

    if (strpos($url, '&amp;') !== false) {
      $url = str_replace('&amp;', '&', $url);
    }

    header('Location: ' . $url);

    exit;
  }

/**
 * Parse file permissions to a human readable layout
 *
 * @param int $mode The file permission to parse
 * @access public
 */

  function osc_get_file_permissions($mode) {
// determine type
    if ( ($mode & 0xC000) == 0xC000) { // unix domain socket
      $type = 's';
    } elseif ( ($mode & 0x4000) == 0x4000) { // directory
      $type = 'd';
    } elseif ( ($mode & 0xA000) == 0xA000) { // symbolic link
      $type = 'l';
    } elseif ( ($mode & 0x8000) == 0x8000) { // regular file
      $type = '-';
    } elseif ( ($mode & 0x6000) == 0x6000) { //bBlock special file
      $type = 'b';
    } elseif ( ($mode & 0x2000) == 0x2000) { // character special file
      $type = 'c';
    } elseif ( ($mode & 0x1000) == 0x1000) { // named pipe
      $type = 'p';
    } else { // unknown
      $type = '?';
    }

// determine permissions
    $owner['read']    = ($mode & 00400) ? 'r' : '-';
    $owner['write']   = ($mode & 00200) ? 'w' : '-';
    $owner['execute'] = ($mode & 00100) ? 'x' : '-';
    $group['read']    = ($mode & 00040) ? 'r' : '-';
    $group['write']   = ($mode & 00020) ? 'w' : '-';
    $group['execute'] = ($mode & 00010) ? 'x' : '-';
    $world['read']    = ($mode & 00004) ? 'r' : '-';
    $world['write']   = ($mode & 00002) ? 'w' : '-';
    $world['execute'] = ($mode & 00001) ? 'x' : '-';

// adjust for SUID, SGID and sticky bit
    if ($mode & 0x800 ) $owner['execute'] = ($owner['execute'] == 'x') ? 's' : 'S';
    if ($mode & 0x400 ) $group['execute'] = ($group['execute'] == 'x') ? 's' : 'S';
    if ($mode & 0x200 ) $world['execute'] = ($world['execute'] == 'x') ? 't' : 'T';

    return $type .
           $owner['read'] . $owner['write'] . $owner['execute'] .
           $group['read'] . $group['write'] . $group['execute'] .
           $world['read'] . $world['write'] . $world['execute'];
  }

/*
 * Recursively remove a directory or a single file
 *
 * @param string $source The source to remove
 * @access public
 */

  function osc_remove($source) {
    global $osC_Language, $osC_MessageStack;

    if (is_dir($source)) {
      $dir = dir($source);

      while ($file = $dir->read()) {
        if ( ($file != '.') && ($file != '..') ) {
          if (is_writeable($source . '/' . $file)) {
            osc_remove($source . '/' . $file);
          } else {
            $osC_MessageStack->add('header', sprintf($osC_Language->get('ms_error_file_not_removable'), $source . '/' . $file), 'error');
          }
        }
      }

      $dir->close();

      if (is_writeable($source)) {
        return rmdir($source);
      } else {
        $osC_MessageStack->add('header', sprintf($osC_Language->get('ms_error_directory_not_removable'), $source), 'error');
      }
    } else {
      if (is_writeable($source)) {
        return unlink($source);
      } else {
        $osC_MessageStack->add('header', sprintf($osC_Language->get('ms_error_file_not_removable'), $source), 'error');
      }
    }
  }

/**
 * Return an image type that the server supports
 *
 * @access public
 */

  function osc_dynamic_image_extension() {
    static $extension;

    if (!isset($extension)) {
      if (function_exists('imagetypes')) {
        if (imagetypes() & IMG_PNG) {
          $extension = 'png';
        } elseif (imagetypes() & IMG_JPG) {
          $extension = 'jpeg';
        } elseif (imagetypes() & IMG_GIF) {
          $extension = 'gif';
        }
      } elseif (function_exists('imagepng')) {
        $extension = 'png';
      } elseif (function_exists('imagejpeg')) {
        $extension = 'jpeg';
      } elseif (function_exists('imagegif')) {
        $extension = 'gif';
      }
    }

    return $extension;
  }

/**
 * Parse a category path to avoid loops with duplicate values
 *
 * @param string $cPath The category path to parse
 * @access public
 */

  function osc_parse_category_path($cPath) {
// make sure the category IDs are integers
    $cPath_array = array_map('intval', explode('_', $cPath));

// make sure no duplicate category IDs exist which could lock the server in a loop
    $tmp_array = array();
    $n = sizeof($cPath_array);
    for ($i=0; $i<$n; $i++) {
      if (!in_array($cPath_array[$i], $tmp_array)) {
        $tmp_array[] = $cPath_array[$i];
      }
    }

    return $tmp_array;
  }

/**
 * Return an array as a string value
 *
 * @param array $array The array to return as a string value
 * @param array $exclude An array of parameters to exclude from the string
 * @param string $equals The equals character to symbolize what value a parameter is defined to
 * @param string $separator The separate to use between parameters
 */

  function osc_array_to_string($array, $exclude = '', $equals = '=', $separator = '&') {
    if (!is_array($exclude)) $exclude = array();

    $get_string = '';
    if (sizeof($array) > 0) {
      while (list($key, $value) = each($array)) {
        if ( (!in_array($key, $exclude)) && ($key != 'x') && ($key != 'y') ) {
          $get_string .= $key . $equals . $value . $separator;
        }
      }
      $remove_chars = strlen($separator);
      $get_string = substr($get_string, 0, -$remove_chars);
    }

    return $get_string;
  }

/**
 * Return a variable value from a serialized string
 *
 * @param string $serialization_data The serialized string to return values from
 * @param string $variable_name The variable to return
 * @param string $variable_type The variable type
 */

  function osc_get_serialized_variable(&$serialization_data, $variable_name, $variable_type = 'string') {
    $serialized_variable = '';

    switch ($variable_type) {
      case 'string':
        $start_position = strpos($serialization_data, $variable_name . '|s');

        $serialized_variable = substr($serialization_data, strpos($serialization_data, '|', $start_position) + 1, strpos($serialization_data, '|', $start_position) - 1);
        break;
      case 'array':
      case 'object':
        if ($variable_type == 'array') {
          $start_position = strpos($serialization_data, $variable_name . '|a');
        } else {
          $start_position = strpos($serialization_data, $variable_name . '|O');
        }

        $tag = 0;

        for ($i=$start_position, $n=sizeof($serialization_data); $i<$n; $i++) {
          if ($serialization_data[$i] == '{') {
            $tag++;
          } elseif ($serialization_data[$i] == '}') {
            $tag--;
          } elseif ($tag < 1) {
            break;
          }
        }

        $serialized_variable = substr($serialization_data, strpos($serialization_data, '|', $start_position) + 1, $i - strpos($serialization_data, '|', $start_position) - 1);
        break;
    }

    return $serialized_variable;
  }

/**
 * Call a function given in string format used by configuration set and use functions
 *
 * @param string $function The complete function to call
 * @param string $default The default value to pass to the function
 * @param string $key The key value to use for the input field
 */

  function osc_call_user_func($function, $default = null, $key = null) {
    if (strpos($function, '::') !== false) {
      $class_method = explode('::', $function);

      return call_user_func(array($class_method[0], $class_method[1]), $default, $key);
    } else {
      $function_name = $function;
      $function_parameter = '';

      if (strpos($function, '(') !== false) {
        $function_array = explode('(', $function, 2);

        $function_name = $function_array[0];
        $function_parameter = substr($function_array[1], 0, -1);
      }

      if (!function_exists($function_name)) {
        include(OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/includes/functions/cfg_parameters/' . $function_name . '.php');
      }

      if (!empty($function_parameter)) {
        return call_user_func($function_name, $function_parameter, $default, $key);
      } else {
        return call_user_func($function_name, $default, $key);
      }
    }
  }

/**
 * Validate a plain text password against an encrypted value
 *
 * @param string $plain The plain text password
 * @param string $encrypted The encrypted password to validate against
 */

  function osc_validate_password($plain, $encrypted) {
    if (!empty($plain) && !empty($encrypted)) {
// split apart the hash / salt
      $stack = explode(':', $encrypted);

      if (sizeof($stack) != 2) {
        return false;
      }

      if (md5($stack[1] . $plain) == $stack[0]) {
        return true;
      }
    }

    return false;
  }

  function osc_toObjectInfo($array) {
    return new osCommerce\OM\Core\ObjectInfo($array);
  }
?>
