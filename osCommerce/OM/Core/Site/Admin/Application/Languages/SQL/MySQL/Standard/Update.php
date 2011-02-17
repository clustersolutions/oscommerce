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

  class Update {
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $OSCOM_Database->beginTransaction();

      $Qlanguage = $OSCOM_Database->prepare('update :table_languages set name = :name, code = :code, locale = :locale, charset = :charset, date_format_short = :date_format_short, date_format_long = :date_format_long, time_format = :time_format, text_direction = :text_direction, currencies_id = :currencies_id, numeric_separator_decimal = :numeric_separator_decimal, numeric_separator_thousands = :numeric_separator_thousands, parent_id = :parent_id, sort_order = :sort_order where languages_id = :languages_id');
      $Qlanguage->bindValue(':name', $data['name']);
      $Qlanguage->bindValue(':code', $data['code']);
      $Qlanguage->bindValue(':locale', $data['locale']);
      $Qlanguage->bindValue(':charset', $data['charset']);
      $Qlanguage->bindValue(':date_format_short', $data['date_format_short']);
      $Qlanguage->bindValue(':date_format_long', $data['date_format_long']);
      $Qlanguage->bindValue(':time_format', $data['time_format']);
      $Qlanguage->bindValue(':text_direction', $data['text_direction']);
      $Qlanguage->bindInt(':currencies_id', $data['currencies_id']);
      $Qlanguage->bindValue(':numeric_separator_decimal', $data['numeric_separator_decimal']);
      $Qlanguage->bindValue(':numeric_separator_thousands', $data['numeric_separator_thousands']);
      $Qlanguage->bindInt(':parent_id', $data['parent_id']);
      $Qlanguage->bindInt(':sort_order', $data['sort_order']);
      $Qlanguage->bindInt(':languages_id', $data['id']);
      $Qlanguage->execute();

      if ( !$Qlanguage->isError() ) {
        if ( $data['set_default'] === true ) {
          $Qupdate = $OSCOM_Database->prepare('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
          $Qupdate->bindValue(':configuration_value', $data['code']);
          $Qupdate->bindValue(':configuration_key', 'DEFAULT_LANGUAGE');
          $Qupdate->execute();
        }

        $OSCOM_Database->commit();

        return true;
      }

      $OSCOM_Database->rollBack();

      return false;
    }
  }
?>
