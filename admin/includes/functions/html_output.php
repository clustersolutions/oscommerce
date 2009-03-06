<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

/**
 * Generate an internal URL address for the administration side
 *
 * @param string $page The page to link to
 * @param string $parameters The parameters to pass to the page (in the GET scope)
 * @access public
 */

  function osc_href_link_admin($page = null, $parameters = null) {
    if (ENABLE_SSL === true) {
      $link = HTTPS_SERVER . DIR_WS_HTTPS_CATALOG . 'admin/';
    } else {
      $link = HTTP_SERVER . DIR_WS_HTTP_CATALOG . 'admin/';
    }

    $link .= $page;

    if (empty($parameters) && !osc_empty(SID)) {
      $link .= '?' . SID;
    } else {
      $link .= '?' . $parameters;

      if (!osc_empty(SID)) {
        $link .= '&' . SID;
      }
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) {
      $link = substr($link, 0, -1);
    }

    return $link;
  }

/**
 * Display an icon from a template set
 *
 * @param string $image The icon to display
 * @param string $title The title of the icon
 * @param string $group The size group of the icon
 * @param string $parameters The parameters to pass to the image
 * @access public
 */

  function osc_icon($image, $title = null, $group = null, $parameters = null) {
    global $osC_Language, $osC_Template;

    if ( is_null($title) ) {
      $title = $osC_Language->get('icon_' . substr($image, 0, strpos($image, '.')));
    }

    if ( is_null($group) ) {
      $group = '16x16';
    }

    return osc_image('templates/' . $osC_Template->getCode() . '/images/icons/' . (!empty($group) ? $group . '/' : null) . $image, $title, null, null, $parameters);
  }

/**
 * Get the raw URL to an icon from a template set
 *
 * @param string $image The icon to display
 * @param string $group The size group of the icon
 * @access public
 */

  function osc_icon_raw($image, $group = '16x16') {
    global $osC_Template;

    return 'templates/' . $osC_Template->getCode() . '/images/icons/' . (!empty($group) ? $group . '/' : null) . $image;
  }

////
// javascript to dynamically update the states/provinces list when the country is changed
// TABLES: zones
  function osc_js_zone_list($country, $form, $field) {
    global $osC_Database, $osC_Language;

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
          $output_string .= '    ' . $form . '.' . $field . '.options[0] = new Option("' . $osC_Language->get('all_zones') . '", "");' . "\n";
        }

        $output_string .= '    ' . $form . '.' . $field . '.options[' . $num_state . '] = new Option("' . $Qzones->value('zone_name') . '", "' . $Qzones->valueInt('zone_id') . '");' . "\n";

        $num_state++;
      }

      $num_country++;
    }

    $output_string .= '  } else {' . "\n" .
                      '    ' . $form . '.' . $field . '.options[0] = new Option("' . $osC_Language->get('all_zones') . '", "");' . "\n" .
                      '  }' . "\n";

    return $output_string;
  }
?>
