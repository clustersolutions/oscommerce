<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class InsertDefinition {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qdef = $OSCOM_PDO->prepare('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
      $Qdef->bindInt(':languages_id', $data['language_id']);
      $Qdef->bindValue(':content_group', $data['group']);
      $Qdef->bindValue(':definition_key', $data['key']);
      $Qdef->bindValue(':definition_value', $data['value']);
      $Qdef->execute();

      return ( ($Qdef->rowCount() === 1) || !$Qdef->isError() );
    }
  }
?>
