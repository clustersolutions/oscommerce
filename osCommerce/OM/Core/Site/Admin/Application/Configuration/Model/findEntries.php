<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\Model;

  use osCommerce\OM\Core\OSCOM;

  class findEntries {
    public static function execute($search, $group_id) {
      $data = array('group_id' => $group_id,
                    'search' => $search);

      return OSCOM::callDB('Admin\Configuration\EntryFind', $data);
    }
  }
?>
