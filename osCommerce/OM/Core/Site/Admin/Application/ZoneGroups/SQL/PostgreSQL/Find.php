<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ZoneGroups\SQL\PostgreSQL;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class Find {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $sql_query = 'select distinct gz.*, (select count(*) from :table_zones_to_geo_zones z2gz where z2gz.geo_zone_id = gz.geo_zone_id) as total_entries from :table_geo_zones gz join :table_zones_to_geo_zones z2gz on (gz.geo_zone_id = z2gz.geo_zone_id), :table_countries c, :table_zones z where z2gz.zone_country_id = c.countries_id and z2gz.zone_id = z.zone_id and (gz.geo_zone_name ilike :geo_zone_name or gz.geo_zone_description ilike :geo_zone_description or c.countries_name ilike :countries_name or z.zone_name ilike :zone_name) order by gz.geo_zone_name';

      if ( $data['batch_pageset'] !== -1 ) {
        $sql_query .= ' limit :batch_max_results offset :batch_pageset';
      }

      $Qgroups = $OSCOM_PDO->prepare($sql_query);
      $Qgroups->bindValue(':geo_zone_name', '%' . $data['keywords'] . '%');
      $Qgroups->bindValue(':geo_zone_description', '%' . $data['keywords'] . '%');
      $Qgroups->bindValue(':countries_name', '%' . $data['keywords'] . '%');
      $Qgroups->bindValue(':zone_name', '%' . $data['keywords'] . '%');

      if ( $data['batch_pageset'] !== -1 ) {
        $Qgroups->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qgroups->bindInt(':batch_max_results', $data['batch_max_results']);
      }

      $Qgroups->execute();

      $result['entries'] = $Qgroups->fetchAll();

      $Qtotal = $OSCOM_PDO->prepare('select count(distinct gz.geo_zone_id) from :table_geo_zones gz join :table_zones_to_geo_zones z2gz on (gz.geo_zone_id = z2gz.geo_zone_id), :table_countries c, :table_zones z where z2gz.zone_country_id = c.countries_id and z2gz.zone_id = z.zone_id and (gz.geo_zone_name ilike :geo_zone_name or gz.geo_zone_description ilike :geo_zone_description or c.countries_name ilike :countries_name or z.zone_name ilike :zone_name)');
      $Qtotal->bindValue(':geo_zone_name', '%' . $data['keywords'] . '%');
      $Qtotal->bindValue(':geo_zone_description', '%' . $data['keywords'] . '%');
      $Qtotal->bindValue(':countries_name', '%' . $data['keywords'] . '%');
      $Qtotal->bindValue(':zone_name', '%' . $data['keywords'] . '%');
      $Qtotal->execute();

      $result['total'] = $Qtotal->fetchColumn();

      return $result;
    }
  }
?>
