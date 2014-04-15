-- osCommerce Online Merchant
--
-- @copyright Copyright (c) 2014 osCommerce; http://www.oscommerce.com
-- @license BSD License; http://www.oscommerce.com/bsdlicense.txt

ALTER TABLE osc_address_book add CONSTRAINT osc_address_book_customers_id_fkey FOREIGN KEY (customers_id) REFERENCES osc_customers (customers_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE osc_address_book add CONSTRAINT osc_address_book_entry_country_id_fkey FOREIGN KEY (entry_country_id) REFERENCES osc_countries (countries_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE osc_address_book add CONSTRAINT osc_address_book_entry_zone_id_fkey FOREIGN KEY (entry_zone_id) REFERENCES osc_zones (zone_id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE osc_administrator_shortcuts add CONSTRAINT osc_administrator_shortcuts_administrators_id_fkey FOREIGN KEY (administrators_id) REFERENCES osc_administrators (id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_administrators_access add CONSTRAINT osc_administrators_access_administrators_id_fkey FOREIGN KEY (administrators_id) REFERENCES osc_administrators (id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_audit_log_rows add CONSTRAINT osc_audit_log_audit_log_id_fkey FOREIGN KEY (audit_log_id) REFERENCES osc_audit_log (id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_banners_history add CONSTRAINT osc_banners_history_banners_id_fkey FOREIGN KEY (banners_id) REFERENCES osc_banners (banners_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_categories add CONSTRAINT osc_categories_parent_id_fkey FOREIGN KEY (parent_id) REFERENCES osc_categories (categories_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_categories_description add CONSTRAINT osc_categories_description_categories_id_fkey FOREIGN KEY (categories_id) REFERENCES osc_categories (categories_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE osc_categories_description add CONSTRAINT osc_categories_description_language_id_fkey FOREIGN KEY (language_id) REFERENCES osc_languages (languages_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_configuration add CONSTRAINT osc_configuration_configuration_group_id_fkey FOREIGN KEY (configuration_group_id) REFERENCES osc_configuration_group (configuration_group_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_customers add CONSTRAINT osc_customers_customers_default_address_id_fkey FOREIGN KEY (customers_default_address_id) REFERENCES osc_address_book (address_book_id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE osc_languages add CONSTRAINT osc_languages_currencies_id_fkey FOREIGN KEY (currencies_id) REFERENCES osc_currencies (currencies_id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE osc_languages_definitions add CONSTRAINT osc_languages_definitions_languages_id_fkey FOREIGN KEY (languages_id) REFERENCES osc_languages (languages_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_manufacturers_info add CONSTRAINT osc_manufacturers_info_manufacturers_id_fkey FOREIGN KEY (manufacturers_id) REFERENCES osc_manufacturers (manufacturers_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE osc_manufacturers_info add CONSTRAINT osc_manufacturers_info_languages_id_fkey FOREIGN KEY (languages_id) REFERENCES osc_languages (languages_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_newsletters_log add CONSTRAINT osc_newsletters_log_newsletters_id_fkey FOREIGN KEY (newsletters_id) REFERENCES osc_newsletters (newsletters_id) ON DELETE CASCADE ON UPDATE CASCADE;

--ALTER TABLE osc_orders add CONSTRAINT osc_orders_orders_status_fkey FOREIGN KEY (orders_status) REFERENCES osc_orders_status (orders_status_id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE osc_orders_products add CONSTRAINT osc_orders_products_orders_id_fkey FOREIGN KEY (orders_id) REFERENCES osc_orders (orders_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_orders_products_download add CONSTRAINT osc_orders_products_download_orders_id_fkey FOREIGN KEY (orders_id) REFERENCES osc_orders (orders_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_orders_products_variants add CONSTRAINT osc_orders_products_variants_orders_id_fkey FOREIGN KEY (orders_id) REFERENCES osc_orders (orders_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_orders_status add CONSTRAINT osc_orders_status_language_id_fkey FOREIGN KEY (language_id) REFERENCES osc_languages (languages_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_orders_status_history add CONSTRAINT osc_orders_status_history_orders_id_fkey FOREIGN KEY (orders_id) REFERENCES osc_orders (orders_id) ON DELETE CASCADE ON UPDATE CASCADE;
--ALTER TABLE osc_orders_status_history add CONSTRAINT osc_orders_status_history_orders_status_id_fkey FOREIGN KEY (orders_status_id) REFERENCES osc_orders_status (orders_status_id) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE osc_orders_total add CONSTRAINT osc_orders_total_orders_id_fkey FOREIGN KEY (orders_id) REFERENCES osc_orders (orders_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_orders_transactions_history add CONSTRAINT osc_orders_transactions_history_orders_id_fkey FOREIGN KEY (orders_id) REFERENCES osc_orders (orders_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_orders_transactions_status add CONSTRAINT osc_orders_transactions_status_language_id_fkey FOREIGN KEY (language_id) REFERENCES osc_languages (languages_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_product_attributes add CONSTRAINT osc_product_attributes_products_id_fkey FOREIGN KEY (products_id) REFERENCES osc_products (products_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE osc_product_attributes add CONSTRAINT osc_product_attributes_languages_id_fkey FOREIGN KEY (languages_id) REFERENCES osc_languages (languages_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_product_types_assignments add CONSTRAINT osc_product_types_assignments_types_id_fkey FOREIGN KEY (types_id) REFERENCES osc_product_types (id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_products add CONSTRAINT osc_products_parent_id_fkey FOREIGN KEY (parent_id) REFERENCES osc_products (products_id) ON DELETE CASCADE ON UPDATE CASCADE;
--ALTER TABLE osc_products add CONSTRAINT osc_products_products_weight_class_fkey FOREIGN KEY (products_weight_class) REFERENCES osc_weight_classes (weight_class_id) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE osc_products add CONSTRAINT osc_products_products_tax_class_id_fkey FOREIGN KEY (products_tax_class_id) REFERENCES osc_tax_class (tax_class_id) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE osc_products add CONSTRAINT osc_products_products_types_id_fkey FOREIGN KEY (products_types_id) REFERENCES osc_product_types (id) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE osc_products add CONSTRAINT osc_products_manufacturers_id_fkey FOREIGN KEY (manufacturers_id) REFERENCES osc_manufacturers (manufacturers_id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE osc_products_description add CONSTRAINT osc_products_description_products_id_fkey FOREIGN KEY (products_id) REFERENCES osc_products (products_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE osc_products_description add CONSTRAINT osc_products_description_language_id_fkey FOREIGN KEY (language_id) REFERENCES osc_languages (languages_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_products_images add CONSTRAINT osc_products_images_products_id_fkey FOREIGN KEY (products_id) REFERENCES osc_products (products_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_products_images_groups add CONSTRAINT osc_products_images_groups_language_id_fkey FOREIGN KEY (language_id) REFERENCES osc_languages (languages_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_products_notifications add CONSTRAINT osc_products_notifications_products_id_fkey FOREIGN KEY (products_id) REFERENCES osc_products (products_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE osc_products_notifications add CONSTRAINT osc_products_notifications_customers_id_fkey FOREIGN KEY (customers_id) REFERENCES osc_customers (customers_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_products_to_categories add CONSTRAINT osc_products_to_categories_products_id_fkey FOREIGN KEY (products_id) REFERENCES osc_products (products_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE osc_products_to_categories add CONSTRAINT osc_products_to_categories_categories_id_fkey FOREIGN KEY (categories_id) REFERENCES osc_categories (categories_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_products_variants add CONSTRAINT osc_products_variants_products_id_fkey FOREIGN KEY (products_id) REFERENCES osc_products (products_id) ON DELETE CASCADE ON UPDATE CASCADE;
--ALTER TABLE osc_products_variants add CONSTRAINT osc_products_variants_products_variants_values_id_fkey FOREIGN KEY (products_variants_values_id) REFERENCES osc_products_variants_values (id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_products_variants_groups add CONSTRAINT osc_products_variants_groups_languages_id_fkey FOREIGN KEY (languages_id) REFERENCES osc_languages (languages_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_products_variants_values add CONSTRAINT osc_products_variants_values_languages_id_fkey FOREIGN KEY (languages_id) REFERENCES osc_languages (languages_id) ON DELETE CASCADE ON UPDATE CASCADE;
--ALTER TABLE osc_products_variants_values add CONSTRAINT osc_products_variants_values_products_variants_groups_id_fkey FOREIGN KEY (products_variants_groups_id) REFERENCES osc_products_variants_groups (id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_reviews add CONSTRAINT osc_reviews_products_id_fkey FOREIGN KEY (products_id) REFERENCES osc_products (products_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE osc_reviews add CONSTRAINT osc_reviews_customers_id_fkey FOREIGN KEY (customers_id) REFERENCES osc_customers (customers_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE osc_reviews add CONSTRAINT osc_reviews_languages_id_fkey FOREIGN KEY (languages_id) REFERENCES osc_languages (languages_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_shipping_availability add CONSTRAINT osc_shipping_availability_languages_id_fkey FOREIGN KEY (languages_id) REFERENCES osc_languages (languages_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_shopping_carts add CONSTRAINT osc_shopping_carts_customers_id_fkey FOREIGN KEY (customers_id) REFERENCES osc_customers (customers_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE osc_shopping_carts add CONSTRAINT osc_shopping_carts_products_id_fkey FOREIGN KEY (products_id) REFERENCES osc_products (products_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_shopping_carts_custom_variants_values add CONSTRAINT osc_shopping_carts_custom_variants_values_customers_id_fkey FOREIGN KEY (customers_id) REFERENCES osc_customers (customers_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE osc_shopping_carts_custom_variants_values add CONSTRAINT osc_shopping_carts_custom_variants_values_products_id_fkey FOREIGN KEY (products_id) REFERENCES osc_products (products_id) ON DELETE CASCADE ON UPDATE CASCADE;
--ALTER TABLE osc_shopping_carts_custom_variants_values add CONSTRAINT osc_shopping_carts_custom_variants_values_products_variants_values_id_fkey FOREIGN KEY (products_variants_values_id) REFERENCES osc_products_variants_values (id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_specials add CONSTRAINT osc_specials_products_id_fkey FOREIGN KEY (products_id) REFERENCES osc_products (products_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_tax_rates add CONSTRAINT osc_tax_rates_tax_zone_id_fkey FOREIGN KEY (tax_zone_id) REFERENCES osc_geo_zones (geo_zone_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE osc_tax_rates add CONSTRAINT osc_tax_rates_tax_class_id_fkey FOREIGN KEY (tax_class_id) REFERENCES osc_tax_class (tax_class_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_templates_boxes_to_pages add CONSTRAINT osc_templates_boxes_to_pages_templates_boxes_id_fkey FOREIGN KEY (templates_boxes_id) REFERENCES osc_templates_boxes (id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE osc_templates_boxes_to_pages add CONSTRAINT osc_templates_boxes_to_pages_templates_id_fkey FOREIGN KEY (templates_id) REFERENCES osc_templates (id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_weight_classes add CONSTRAINT osc_weight_classes_language_id_fkey FOREIGN KEY (language_id) REFERENCES osc_languages (languages_id) ON DELETE CASCADE ON UPDATE CASCADE;

--ALTER TABLE osc_weight_classes_rules add CONSTRAINT osc_weight_classes_weight_class_from_id_fkey FOREIGN KEY (weight_class_from_id) REFERENCES osc_weight_classes (weight_class_id) ON DELETE CASCADE ON UPDATE CASCADE;
--ALTER TABLE osc_weight_classes_rules add CONSTRAINT osc_weight_classes_weight_class_to_id_fkey FOREIGN KEY (weight_class_to_id) REFERENCES osc_weight_classes (weight_class_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_whos_online add CONSTRAINT osc_whos_online_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES osc_customers (customers_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_zones add CONSTRAINT osc_zones_zone_country_id_fkey FOREIGN KEY (zone_country_id) REFERENCES osc_countries (countries_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE osc_zones_to_geo_zones add CONSTRAINT osc_zones_to_geo_zones_zone_country_id_fkey FOREIGN KEY (zone_country_id) REFERENCES osc_countries (countries_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE osc_zones_to_geo_zones add CONSTRAINT osc_zones_to_geo_zones_zone_id_fkey FOREIGN KEY (zone_id) REFERENCES osc_zones (zone_id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE osc_zones_to_geo_zones add CONSTRAINT osc_zones_to_geo_zones_geo_zone_id_fkey FOREIGN KEY (geo_zone_id) REFERENCES osc_geo_zones (geo_zone_id) ON DELETE CASCADE ON UPDATE CASCADE;
