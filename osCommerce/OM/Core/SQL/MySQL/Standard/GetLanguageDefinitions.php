<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetLanguageDefinitions {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qdef = $OSCOM_PDO->prepare('select * from :table_languages_definitions where languages_id = :languages_id and content_group = :content_group');
      $Qdef->bindInt(':languages_id', $data['language_id']);
      $Qdef->bindValue(':content_group', $data['group']);
// HPDL      $Qdef->setCache('languages-' . $language_code . '-' . $key);
      $Qdef->execute();

      return $Qdef->fetchAll();
    }
  }
?>
