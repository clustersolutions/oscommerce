<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Model;

  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Cache;

  class insertDefinition {
    public static function execute($data) {
      $languages = Languages::getAll(-1);
      $languages = $languages['entries'];

      $values = $data['values'];

      unset($data['values']);

      foreach ( $languages as $l ) {
        $data['language_id'] = $l['languages_id'];
        $data['value'] = $values[$l['languages_id']];

        OSCOM::callDB('Admin\Languages\InsertDefinition', $data);

        Cache::clear('languages-' . $l['code'] . '-' . $data['group']);
      }

      return true;
    }
  }
?>
