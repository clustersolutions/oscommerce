<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\Model;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Cache;
  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;

  class updateDefinition {
    public static function execute($data) {
      $definitions = $data['definitions'];

      unset($data['definitions']);

      foreach ( $definitions as $key => $value ) {
        $data['key'] = $key;
        $data['value'] = $value;

        OSCOM::callDB('Admin\Languages\UpdateDefinition', $data);

        Cache::clear('languages-' . Languages::get($data['language_id'], 'code') . '-' . $data['group']);
      }

      return true;
    }
  }
?>
