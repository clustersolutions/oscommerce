# $Id$
#
# osCommerce, Open Source E-Commerce Solutions
# http://www.oscommerce.com
#
# Copyright (c) 2006 osCommerce
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License v2 (1991)
# as published by the Free Software Foundation.
#
# NOTE: * Please make any modifications to this file by hand!
#       * DO NOT use a mysqldump created file for new changes!
#       * Please take note of the table structure, and use this
#         structure as a standard for future modifications!
#       * Any tables you add here should be added in admin/backup.php
#         and in catalog/install/includes/functions/database.php
#       * To see the 'diff'erence between MySQL databases, use
#         the mysqldiff perl script located in the extras
#         directory of the 'catalog' module.
#       * Comments should be like these, full line comments.
#         (don't use inline comments)

INSERT INTO osc_categories VALUES (1, 'books.gif', 0, 0, now(), null);
INSERT INTO osc_categories VALUES (2, 'php.gif', 1, 0, now(), null);

INSERT INTO osc_categories_description VALUES (1, 1, 'Books');
INSERT INTO osc_categories_description VALUES (2, 1, 'PHP');

INSERT INTO osc_manufacturers VALUES (1, 'Apress', 'apress.gif', now(), null);

INSERT INTO osc_manufacturers_info VALUES (1, 1, 'http://www.apress.com', 0, null);

INSERT INTO osc_products VALUES (1, 10, 44.99, now(), now(), null, 1.8, 4, 1, 1, 1, 0);

INSERT INTO osc_products_description VALUES (1, 1, 'Pro PHP Security', '<p><i>Pro PHP Security</i> is one of the first books devoted solely to PHP security. It will serve as your complete guide for taking defensive and proactive security measures within your PHP applications. (And the methods discussed are compatible with PHP versions 3, 4, and 5.)</p><p>The knowledge you\'ll gain from this comprehensive guide will help you prevent attackers from potentially disrupting site operation or destroying data. And you\'ll learn about various security measures, for example, creating and deploying "captchas," validating e-mail, fending off SQL injection attacks, and preventing cross-site scripting attempts.</p><h3>Author Information</h3><p align="justify"><h4>Chris Snyder</h4><p>Chris Snyder is a software engineer at Fund for the City of New York, where he helps develop next-generation websites and services for nonprofit organizations. He is a member of the Executive Board of New York PHP, and has been looking for new ways to build scriptable, linked, multimedia content since he saw his first Hypercard stack in 1988.</p></p><p align="justify"><h4>Michael Southwell</h4><p>Michael Southwell is a retired English professor who has been developing websites for more than 10 years in the small business, nonprofit, and educational areas, with special interest in problems of accessibility. He has authored and co-authored 8 books and numerous articles about writing, writing and computers, and writing education. He is a member of the Executive Board of New York PHP, and a Zend Certified Engineer.</p></p>', '1590595084', 'pro_php_security', 'pro php security book apress', '', 0);

#INSERT INTO osc_products_attributes VALUES (1,1,4,1,0.00,'+');
#INSERT INTO osc_products_attributes VALUES (2,1,4,2,50.00,'+');
#INSERT INTO osc_products_attributes VALUES (3,1,4,3,70.00,'+');
#INSERT INTO osc_products_attributes VALUES (4,1,3,5,0.00,'+');
#INSERT INTO osc_products_attributes VALUES (5,1,3,6,100.00,'+');
#INSERT INTO osc_products_attributes VALUES (6,2,4,3,10.00,'-');
#INSERT INTO osc_products_attributes VALUES (7,2,4,4,0.00,'+');
#INSERT INTO osc_products_attributes VALUES (8,2,3,6,0.00,'+');
#INSERT INTO osc_products_attributes VALUES (9,2,3,7,120.00,'+');
#INSERT INTO osc_products_attributes VALUES (10,26,3,8,0.00,'+');
#INSERT INTO osc_products_attributes VALUES (11,26,3,9,6.00,'+');
#INSERT INTO osc_products_attributes VALUES (26, 22, 5, 10, '0.00', '+');
#INSERT INTO osc_products_attributes VALUES (27, 22, 5, 13, '0.00', '+');

#INSERT INTO osc_products_attributes_download VALUES (26, 'unreal.zip', 7, 3);

#INSERT INTO osc_products_options VALUES (1,1,'Color');
#INSERT INTO osc_products_options VALUES (2,1,'Size');
#INSERT INTO osc_products_options VALUES (3,1,'Model');
#INSERT INTO osc_products_options VALUES (4,1,'Memory');
#INSERT INTO osc_products_options VALUES (5, 1, 'Version');

#INSERT INTO osc_products_options_values VALUES (1,1,'4 mb');
#INSERT INTO osc_products_options_values VALUES (2,1,'8 mb');
#INSERT INTO osc_products_options_values VALUES (3,1,'16 mb');
#INSERT INTO osc_products_options_values VALUES (4,1,'32 mb');
#INSERT INTO osc_products_options_values VALUES (5,1,'Value');
#INSERT INTO osc_products_options_values VALUES (6,1,'Premium');
#INSERT INTO osc_products_options_values VALUES (7,1,'Deluxe');
#INSERT INTO osc_products_options_values VALUES (8,1,'PS/2');
#INSERT INTO osc_products_options_values VALUES (9,1,'USB');
#INSERT INTO osc_products_options_values VALUES (10, 1, 'Download: Windows - English');
#INSERT INTO osc_products_options_values VALUES (13, 1, 'Box: Windows - English');

#INSERT INTO osc_products_options_values_to_products_options VALUES (1,4,1);
#INSERT INTO osc_products_options_values_to_products_options VALUES (2,4,2);
#INSERT INTO osc_products_options_values_to_products_options VALUES (3,4,3);
#INSERT INTO osc_products_options_values_to_products_options VALUES (4,4,4);
#INSERT INTO osc_products_options_values_to_products_options VALUES (5,3,5);
#INSERT INTO osc_products_options_values_to_products_options VALUES (6,3,6);
#INSERT INTO osc_products_options_values_to_products_options VALUES (7,3,7);
#INSERT INTO osc_products_options_values_to_products_options VALUES (8,3,8);
#INSERT INTO osc_products_options_values_to_products_options VALUES (9,3,9);
#INSERT INTO osc_products_options_values_to_products_options VALUES (10, 5, 10);
#INSERT INTO osc_products_options_values_to_products_options VALUES (13, 5, 13);

INSERT INTO osc_products_to_categories VALUES (1, 2);

INSERT INTO osc_products_images VALUES (1, 1, 'pro_php_security.jpg', 1, 0, now());

#INSERT INTO osc_reviews VALUES (1,19,0,'John doe',5,1,'this has to be one of the funniest movies released for 1999!',now(),null,0,1);

#INSERT INTO osc_specials VALUES (1,3, 39.99, now(), null, null, null, null, '1');
#INSERT INTO osc_specials VALUES (2,5, 30.00, now(), null, null, null, null, '1');
#INSERT INTO osc_specials VALUES (3,6, 30.00, now(), null, null, null, null, '1');
#INSERT INTO osc_specials VALUES (4,16, 29.99, now(), null, null, null, null, '1');
