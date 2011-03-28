<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class DeleteLanguageDefinitions {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qdel = $OSCOM_PDO->prepare('delete from :table_languages_definitions where definition_key = :definition_key and content_group = :content_group');
      $Qdel->bindValue(':definition_key', $data['key']);
      $Qdel->bindValue(':content_group', $data['group']);
      $Qdel->execute();

      return !$Qdel->isError();
    }
  }
?>
