# $Id$
#
# osCommerce, Open Source E-Commerce Solutions
# http://www.oscommerce.com
#
# Copyright (c) 2006 osCommerce
#
# Released under the GNU General Public License
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

INSERT INTO osc_categories VALUES ('1', 'category_hardware.gif', '0', '1', now(), null);
INSERT INTO osc_categories VALUES ('2', 'category_software.gif', '0', '2', now(), null);
INSERT INTO osc_categories VALUES ('3', 'category_dvd_movies.gif', '0', '3', now(), null);
INSERT INTO osc_categories VALUES ('4', 'subcategory_graphic_cards.gif', '1', '0', now(), null);
INSERT INTO osc_categories VALUES ('5', 'subcategory_printers.gif', '1', '0', now(), null);
INSERT INTO osc_categories VALUES ('6', 'subcategory_monitors.gif', '1', '0', now(), null);
INSERT INTO osc_categories VALUES ('7', 'subcategory_speakers.gif', '1', '0', now(), null);
INSERT INTO osc_categories VALUES ('8', 'subcategory_keyboards.gif', '1', '0', now(), null);
INSERT INTO osc_categories VALUES ('9', 'subcategory_mice.gif', '1', '0', now(), null);
INSERT INTO osc_categories VALUES ('10', 'subcategory_action.gif', '3', '0', now(), null);
INSERT INTO osc_categories VALUES ('11', 'subcategory_science_fiction.gif', '3', '0', now(), null);
INSERT INTO osc_categories VALUES ('12', 'subcategory_comedy.gif', '3', '0', now(), null);
INSERT INTO osc_categories VALUES ('13', 'subcategory_cartoons.gif', '3', '0', now(), null);
INSERT INTO osc_categories VALUES ('14', 'subcategory_thriller.gif', '3', '0', now(), null);
INSERT INTO osc_categories VALUES ('15', 'subcategory_drama.gif', '3', '0', now(), null);
INSERT INTO osc_categories VALUES ('16', 'subcategory_memory.gif', '1', '0', now(), null);
INSERT INTO osc_categories VALUES ('17', 'subcategory_cdrom_drives.gif', '1', '0', now(), null);
INSERT INTO osc_categories VALUES ('18', 'subcategory_simulation.gif', '2', '0', now(), null);
INSERT INTO osc_categories VALUES ('19', 'subcategory_action_games.gif', '2', '0', now(), null);
INSERT INTO osc_categories VALUES ('20', 'subcategory_strategy.gif', '2', '0', now(), null);

INSERT INTO osc_categories_description VALUES ( '1', '1', 'Hardware');
INSERT INTO osc_categories_description VALUES ( '2', '1', 'Software');
INSERT INTO osc_categories_description VALUES ( '3', '1', 'DVD Movies');
INSERT INTO osc_categories_description VALUES ( '4', '1', 'Graphics Cards');
INSERT INTO osc_categories_description VALUES ( '5', '1', 'Printers');
INSERT INTO osc_categories_description VALUES ( '6', '1', 'Monitors');
INSERT INTO osc_categories_description VALUES ( '7', '1', 'Speakers');
INSERT INTO osc_categories_description VALUES ( '8', '1', 'Keyboards');
INSERT INTO osc_categories_description VALUES ( '9', '1', 'Mice');
INSERT INTO osc_categories_description VALUES ( '10', '1', 'Action');
INSERT INTO osc_categories_description VALUES ( '11', '1', 'Science Fiction');
INSERT INTO osc_categories_description VALUES ( '12', '1', 'Comedy');
INSERT INTO osc_categories_description VALUES ( '13', '1', 'Cartoons');
INSERT INTO osc_categories_description VALUES ( '14', '1', 'Thriller');
INSERT INTO osc_categories_description VALUES ( '15', '1', 'Drama');
INSERT INTO osc_categories_description VALUES ( '16', '1', 'Memory');
INSERT INTO osc_categories_description VALUES ( '17', '1', 'CDROM Drives');
INSERT INTO osc_categories_description VALUES ( '18', '1', 'Simulation');
INSERT INTO osc_categories_description VALUES ( '19', '1', 'Action');
INSERT INTO osc_categories_description VALUES ( '20', '1', 'Strategy');

INSERT INTO osc_manufacturers VALUES (1,'Matrox','manufacturer_matrox.gif', now(), null);
INSERT INTO osc_manufacturers VALUES (2,'Microsoft','manufacturer_microsoft.gif', now(), null);
INSERT INTO osc_manufacturers VALUES (3,'Warner','manufacturer_warner.gif', now(), null);
INSERT INTO osc_manufacturers VALUES (4,'Fox','manufacturer_fox.gif', now(), null);
INSERT INTO osc_manufacturers VALUES (5,'Logitech','manufacturer_logitech.gif', now(), null);
INSERT INTO osc_manufacturers VALUES (6,'Canon','manufacturer_canon.gif', now(), null);
INSERT INTO osc_manufacturers VALUES (7,'Sierra','manufacturer_sierra.gif', now(), null);
INSERT INTO osc_manufacturers VALUES (8,'GT Interactive','manufacturer_gt_interactive.gif', now(), null);
INSERT INTO osc_manufacturers VALUES (9,'Hewlett Packard','manufacturer_hewlett_packard.gif', now(), null);

INSERT INTO osc_manufacturers_info VALUES (1, 1, 'http://www.matrox.com', 0, null);
INSERT INTO osc_manufacturers_info VALUES (2, 1, 'http://www.microsoft.com', 0, null);
INSERT INTO osc_manufacturers_info VALUES (3, 1, 'http://www.warner.com', 0, null);
INSERT INTO osc_manufacturers_info VALUES (4, 1, 'http://www.fox.com', 0, null);
INSERT INTO osc_manufacturers_info VALUES (5, 1, 'http://www.logitech.com', 0, null);
INSERT INTO osc_manufacturers_info VALUES (6, 1, 'http://www.canon.com', 0, null);
INSERT INTO osc_manufacturers_info VALUES (7, 1, 'http://www.sierra.com', 0, null);
INSERT INTO osc_manufacturers_info VALUES (8, 1, 'http://www.infogrames.com', 0, null);
INSERT INTO osc_manufacturers_info VALUES (9, 1, 'http://www.hewlettpackard.com', 0, null);

INSERT INTO osc_products VALUES (1,32,'matrox/mg200mms.gif',299.99, now(),null,null,23.00,2,1,1,1,0);
INSERT INTO osc_products VALUES (2,32,'matrox/mg400-32mb.gif',499.99, now(),null,null,23.00,2,1,1,1,0);
INSERT INTO osc_products VALUES (3,2,'microsoft/msimpro.gif',49.99, now(),null,null,7.00,2,1,1,3,0);
INSERT INTO osc_products VALUES (4,13,'dvd/replacement_killers.gif',42.00, now(),null,null,23.00,2,1,1,2,0);
INSERT INTO osc_products VALUES (5,17,'dvd/blade_runner.gif',35.99, now(),null,null,7.00,2,1,1,3,0);
INSERT INTO osc_products VALUES (6,10,'dvd/the_matrix.gif',39.99, now(),null,null,7.00,2,1,1,3,0);
INSERT INTO osc_products VALUES (7,10,'dvd/youve_got_mail.gif',34.99, now(),null,null,7.00,2,1,1,3,0);
INSERT INTO osc_products VALUES (8,10,'dvd/a_bugs_life.gif',35.99, now(),null,null,7.00,2,1,1,3,0);
INSERT INTO osc_products VALUES (9,10,'dvd/under_siege.gif',29.99, now(),null,null,7.00,2,1,1,3,0);
INSERT INTO osc_products VALUES (10,10,'dvd/under_siege2.gif',29.99, now(),null,null,7.00,2,1,1,3,0);
INSERT INTO osc_products VALUES (11,10,'dvd/fire_down_below.gif',29.99, now(),null,null,7.00,2,1,1,3,0);
INSERT INTO osc_products VALUES (12,10,'dvd/die_hard_3.gif',39.99, now(),null,null,7.00,2,1,1,4,0);
INSERT INTO osc_products VALUES (13,10,'dvd/lethal_weapon.gif',34.99, now(),null,null,7.00,2,1,1,3,0);
INSERT INTO osc_products VALUES (14,10,'dvd/red_corner.gif',32.00, now(),null,null,7.00,2,1,1,3,0);
INSERT INTO osc_products VALUES (15,10,'dvd/frantic.gif',35.00, now(),null,null,7.00,2,1,1,3,0);
INSERT INTO osc_products VALUES (16,10,'dvd/courage_under_fire.gif',38.99, now(),null,null,7.00,2,1,1,4,0);
INSERT INTO osc_products VALUES (17,10,'dvd/speed.gif',39.99, now(),null,null,7.00,2,1,1,4,0);
INSERT INTO osc_products VALUES (18,10,'dvd/speed_2.gif',42.00, now(),null,null,7.00,2,1,1,4,0);
INSERT INTO osc_products VALUES (19,10,'dvd/theres_something_about_mary.gif',49.99, now(),null,null,7.00,2,1,1,4,0);
INSERT INTO osc_products VALUES (20,10,'dvd/beloved.gif',54.99, now(),null,null,7.00,2,1,1,3,0);
INSERT INTO osc_products VALUES (21,16,'sierra/swat_3.gif',79.99, now(),null,null,7.00,2,1,1,7,0);
INSERT INTO osc_products VALUES (22,13,'gt_interactive/unreal_tournament.gif',89.99, now(),null,null,7.00,2,1,1,8,0);
INSERT INTO osc_products VALUES (23,16,'gt_interactive/wheel_of_time.gif',99.99, now(),null,null,10.00,2,1,1,8,0);
INSERT INTO osc_products VALUES (24,17,'gt_interactive/disciples.gif',90.00, now(),null,null,8.00,2,1,1,8,0);
INSERT INTO osc_products VALUES (25,16,'microsoft/intkeyboardps2.gif',69.99, now(),null,null,8.00,2,1,1,2,0);
INSERT INTO osc_products VALUES (26,10,'microsoft/imexplorer.gif',64.95, now(),null,null,8.00,2,1,1,2,0);
INSERT INTO osc_products VALUES (27,8,'hewlett_packard/lj1100xi.gif',499.99, now(),null,null,45.00,2,1,1,9,0);

INSERT INTO osc_products_description VALUES (1,1,'Matrox G200 MMS','Reinforcing its position as a multi-monitor trailblazer, Matrox Graphics Inc. has once again developed the most flexible and highly advanced solution in the industry. Introducing the new Matrox G200 Multi-Monitor Series; the first graphics card ever to support up to four DVI digital flat panel displays on a single 8&quot; PCI board.<br><br>With continuing demand for digital flat panels in the financial workplace, the Matrox G200 MMS is the ultimate in flexible solutions. The Matrox G200 MMS also supports the new digital video interface (DVI) created by the Digital Display Working Group (DDWG) designed to ease the adoption of digital flat panels. Other configurations include composite video capture ability and onboard TV tuner, making the Matrox G200 MMS the complete solution for business needs.<br><br>Based on the award-winning MGA-G200 graphics chip, the Matrox G200 Multi-Monitor Series provides superior 2D/3D graphics acceleration to meet the demanding needs of business applications such as real-time stock quotes (Versus), live video feeds (Reuters & Bloombergs), multiple windows applications, word processing, spreadsheets and CAD.','MG200MMS','matrox_g200_mms','','www.matrox.com/mga/products/g200_mms/home.cfm',0);
INSERT INTO osc_products_description VALUES (2,1,'Matrox G400 32MB','<b>Dramatically Different High Performance Graphics</b><br><br>Introducing the Millennium G400 Series - a dramatically different, high performance graphics experience. Armed with the industry\'s fastest graphics chip, the Millennium G400 Series takes explosive acceleration two steps further by adding unprecedented image quality, along with the most versatile display options for all your 3D, 2D and DVD applications. As the most powerful and innovative tools in your PC\'s arsenal, the Millennium G400 Series will not only change the way you see graphics, but will revolutionize the way you use your computer.<br><br><b>Key features:</b><ul><li>New Matrox G400 256-bit DualBus graphics chip</li><li>Explosive 3D, 2D and DVD performance</li><li>DualHead Display</li><li>Superior DVD and TV output</li><li>3D Environment-Mapped Bump Mapping</li><li>Vibrant Color Quality rendering </li><li>UltraSharp DAC of up to 360 MHz</li><li>3D Rendering Array Processor</li><li>Support for 16 or 32 MB of memory</li></ul>','MG400-32MB','matrox_g400_32mb','','www.matrox.com/mga/products/mill_g400/home.htm',0);
INSERT INTO osc_products_description VALUES (3,1,'Microsoft IntelliMouse Pro','Every element of IntelliMouse Pro - from its unique arched shape to the texture of the rubber grip around its base - is the product of extensive customer and ergonomic research. Microsoft\'s popular wheel control, which now allows zooming and universal scrolling functions, gives IntelliMouse Pro outstanding comfort and efficiency.','MSIMPRO','microsoft_intellimouse_pro','','www.microsoft.com/hardware/mouse/intellimouse.asp',0);
INSERT INTO osc_products_description VALUES (4,1,'The Replacement Killers','Regional Code: 2 (Japan, Europe, Middle East, South Africa).<br>Languages: English, Deutsch.<br>Subtitles: English, Deutsch, Spanish.<br>Audio: Dolby Surround 5.1.<br>Picture Format: 16:9 Wide-Screen.<br>Length: (approx) 80 minutes.<br>Other: Interactive Menus, Chapter Selection, Subtitles (more languages).','DVD-RPMK','replacement_killers','','www.replacement-killers.com',0);
INSERT INTO osc_products_description VALUES (5,1,'Blade Runner - Director\'s Cut','Regional Code: 2 (Japan, Europe, Middle East, South Africa).<br>Languages: English, Deutsch.<br>Subtitles: English, Deutsch, Spanish.<br>Audio: Dolby Surround 5.1.<br>Picture Format: 16:9 Wide-Screen.<br>Length: (approx) 112 minutes.<br>Other: Interactive Menus, Chapter Selection, Subtitles (more languages).','DVD-BLDRNDC','blade_runner_directors_cut','','www.bladerunner.com',0);
INSERT INTO osc_products_description VALUES (6,1,'The Matrix','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r<br>\nLanguages: English, Deutsch.\r<br>\nSubtitles: English, Deutsch.\r<br>\nAudio: Dolby Surround.\r<br>\nPicture Format: 16:9 Wide-Screen.\r<br>\nLength: (approx) 131 minutes.\r<br>\nOther: Interactive Menus, Chapter Selection, Making Of.','DVD-MATR','the_matrix','What is the Matrix?, Neo','www.thematrix.com',0);
INSERT INTO osc_products_description VALUES (7,1,'You\'ve Got Mail','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r<br>\nLanguages: English, Deutsch, Spanish.\r<br>\nSubtitles: English, Deutsch, Spanish, French, Nordic, Polish.\r<br>\nAudio: Dolby Digital 5.1.\r<br>\nPicture Format: 16:9 Wide-Screen.\r<br>\nLength: (approx) 115 minutes.\r<br>\nOther: Interactive Menus, Chapter Selection, Subtitles (more languages).','DVD-YGEM','youve_got_email','','www.youvegotmail.com',0);
INSERT INTO osc_products_description VALUES (8,1,'A Bug\'s Life','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r<br>\nLanguages: English, Deutsch.\r<br>\nSubtitles: English, Deutsch, Spanish.\r<br>\nAudio: Dolby Digital 5.1 / Dobly Surround Stereo.\r<br>\nPicture Format: 16:9 Wide-Screen.\r<br>\nLength: (approx) 91 minutes.\r<br>\nOther: Interactive Menus, Chapter Selection, Subtitles (more languages).','DVD-ABUG','a_bugs_life','','www.abugslife.com',0);
INSERT INTO osc_products_description VALUES (9,1,'Under Siege','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r<br>\nLanguages: English, Deutsch.\r<br>\nSubtitles: English, Deutsch, Spanish.\r<br>\nAudio: Dolby Surround 5.1.\r<br>\nPicture Format: 16:9 Wide-Screen.\r<br>\nLength: (approx) 98 minutes.\r<br>\nOther: Interactive Menus, Chapter Selection, Subtitles (more languages).','DVD-UNSG','under_siege','','',0);
INSERT INTO osc_products_description VALUES (10,1,'Under Siege 2 - Dark Territory','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r<br>\nLanguages: English, Deutsch.\r<br>\nSubtitles: English, Deutsch, Spanish.\r<br>\nAudio: Dolby Surround 5.1.\r<br>\nPicture Format: 16:9 Wide-Screen.\r<br>\nLength: (approx) 98 minutes.\r<br>\nOther: Interactive Menus, Chapter Selection, Subtitles (more languages).','DVD-UNSG2','under_siege_2','','',0);
INSERT INTO osc_products_description VALUES (11,1,'Fire Down Below','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r<br>\nLanguages: English, Deutsch.\r<br>\nSubtitles: English, Deutsch, Spanish.\r<br>\nAudio: Dolby Surround 5.1.\r<br>\nPicture Format: 16:9 Wide-Screen.\r<br>\nLength: (approx) 100 minutes.\r<br>\nOther: Interactive Menus, Chapter Selection, Subtitles (more languages).','DVD-FDBL','fire_down_below','','',0);
INSERT INTO osc_products_description VALUES (12,1,'Die Hard With A Vengeance','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r<br>\nLanguages: English, Deutsch.\r<br>\nSubtitles: English, Deutsch, Spanish.\r<br>\nAudio: Dolby Surround 5.1.\r<br>\nPicture Format: 16:9 Wide-Screen.\r<br>\nLength: (approx) 122 minutes.\r<br>\nOther: Interactive Menus, Chapter Selection, Subtitles (more languages).','DVD-DHWV','die_hard_with_a_vengeance','','',0);
INSERT INTO osc_products_description VALUES (13,1,'Lethal Weapon','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r<br>\nLanguages: English, Deutsch.\r<br>\nSubtitles: English, Deutsch, Spanish.\r<br>\nAudio: Dolby Surround 5.1.\r<br>\nPicture Format: 16:9 Wide-Screen.\r<br>\nLength: (approx) 100 minutes.\r<br>\nOther: Interactive Menus, Chapter Selection, Subtitles (more languages).','DVD-LTWP','lethal_weapon','','',0);
INSERT INTO osc_products_description VALUES (14,1,'Red Corner','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r<br>\nLanguages: English, Deutsch.\r<br>\nSubtitles: English, Deutsch, Spanish.\r<br>\nAudio: Dolby Surround 5.1.\r<br>\nPicture Format: 16:9 Wide-Screen.\r<br>\nLength: (approx) 117 minutes.\r<br>\nOther: Interactive Menus, Chapter Selection, Subtitles (more languages).','DVD-REDC','red_corner','','',0);
INSERT INTO osc_products_description VALUES (15,1,'Frantic','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r<br>\nLanguages: English, Deutsch.\r<br>\nSubtitles: English, Deutsch, Spanish.\r<br>\nAudio: Dolby Surround 5.1.\r<br>\nPicture Format: 16:9 Wide-Screen.\r<br>\nLength: (approx) 115 minutes.\r<br>\nOther: Interactive Menus, Chapter Selection, Subtitles (more languages).','DVD-FRAN','frantic','','',0);
INSERT INTO osc_products_description VALUES (16,1,'Courage Under Fire','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r<br>\nLanguages: English, Deutsch.\r<br>\nSubtitles: English, Deutsch, Spanish.\r<br>\nAudio: Dolby Surround 5.1.\r<br>\nPicture Format: 16:9 Wide-Screen.\r<br>\nLength: (approx) 112 minutes.\r<br>\nOther: Interactive Menus, Chapter Selection, Subtitles (more languages).','DVD-CUFI','courage_under_fire','','',0);
INSERT INTO osc_products_description VALUES (17,1,'Speed','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r<br>\nLanguages: English, Deutsch.\r<br>\nSubtitles: English, Deutsch, Spanish.\r<br>\nAudio: Dolby Surround 5.1.\r<br>\nPicture Format: 16:9 Wide-Screen.\r<br>\nLength: (approx) 112 minutes.\r<br>\nOther: Interactive Menus, Chapter Selection, Subtitles (more languages).','DVD-SPEED','speed','','',0);
INSERT INTO osc_products_description VALUES (18,1,'Speed 2: Cruise Control','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r<br>\nLanguages: English, Deutsch.\r<br>\nSubtitles: English, Deutsch, Spanish.\r<br>\nAudio: Dolby Surround 5.1.\r<br>\nPicture Format: 16:9 Wide-Screen.\r<br>\nLength: (approx) 120 minutes.\r<br>\nOther: Interactive Menus, Chapter Selection, Subtitles (more languages).','DVD-SPEED2','speed_2_cruise_control','','',0);
INSERT INTO osc_products_description VALUES (19,1,'There\'s Something About Mary','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r<br>\nLanguages: English, Deutsch.\r<br>\nSubtitles: English, Deutsch, Spanish.\r<br>\nAudio: Dolby Surround 5.1.\r<br>\nPicture Format: 16:9 Wide-Screen.\r<br>\nLength: (approx) 114 minutes.\r<br>\nOther: Interactive Menus, Chapter Selection, Subtitles (more languages).','DVD-TSAB','theres_something_about_mary','','',0);
INSERT INTO osc_products_description VALUES (20,1,'Beloved','Regional Code: 2 (Japan, Europe, Middle East, South Africa).\r<br>\nLanguages: English, Deutsch.\r<br>\nSubtitles: English, Deutsch, Spanish.\r<br>\nAudio: Dolby Surround 5.1.\r<br>\nPicture Format: 16:9 Wide-Screen.\r<br>\nLength: (approx) 164 minutes.\r<br>\nOther: Interactive Menus, Chapter Selection, Subtitles (more languages).','DVD-BELOVED','beloved','','',0);
INSERT INTO osc_products_description VALUES (21,1,'SWAT 3: Close Quarters Battle','<b>Windows 95/98</b><br><br>211 in progress with shots fired. Officer down. Armed suspects with hostages. Respond Code 3! Los Angles, 2005, In the next seven days, representatives from every nation around the world will converge on Las Angles to witness the signing of the United Nations Nuclear Abolishment Treaty. The protection of these dignitaries falls on the shoulders of one organization, LAPD SWAT. As part of this elite tactical organization, you and your team have the weapons and all the training necessary to protect, to serve, and \"When needed\" to use deadly force to keep the peace. It takes more than weapons to make it through each mission. Your arsenal includes C2 charges, flashbangs, tactical grenades. opti-Wand mini-video cameras, and other devices critical to meeting your objectives and keeping your men free of injury. Uncompromised Duty, Honor and Valor!','PC-SWAT3','swat_3_close_quarters_battle','','www.swat3.com',0);
INSERT INTO osc_products_description VALUES (22,1,'Unreal Tournament','From the creators of the best-selling Unreal, comes Unreal Tournament. A new kind of single player experience. A ruthless multiplayer revolution.<br><br>This stand-alone game showcases completely new team-based gameplay, groundbreaking multi-faceted single player action or dynamic multi-player mayhem. It\'s a fight to the finish for the title of Unreal Grand Master in the gladiatorial arena. A single player experience like no other! Guide your team of \'bots\' (virtual teamates) against the hardest criminals in the galaxy for the ultimate title - the Unreal Grand Master.','PC-UNTM','unreal_tournament','','www.unrealtournament.net',0);
INSERT INTO osc_products_description VALUES (23,1,'The Wheel Of Time','The world in which The Wheel of Time takes place is lifted directly out of Jordan\'s pages; it\'s huge and consists of many different environments. How you navigate the world will depend largely on which game - single player or multipayer - you\'re playing. The single player experience, with a few exceptions, will see Elayna traversing the world mainly by foot (with a couple notable exceptions). In the multiplayer experience, your character will have more access to travel via Ter\'angreal, Portal Stones, and the Ways. However you move around, though, you\'ll quickly discover that means of locomotion can easily become the least of the your worries...<br><br>During your travels, you quickly discover that four locations are crucial to your success in the game. Not surprisingly, these locations are the homes of The Wheel of Time\'s main characters. Some of these places are ripped directly from the pages of Jordan\'s books, made flesh with Legend\'s unparalleled pixel-pushing ways. Other places are specific to the game, conceived and executed with the intent of expanding this game world even further. Either way, they provide a backdrop for some of the most intense first person action and strategy you\'ll have this year.','PC-TWOF','the_wheel_of_time','','www.wheeloftime.com',0);
INSERT INTO osc_products_description VALUES (24,1,'Disciples: Sacred Lands','A new age is dawning...<br><br>Enter the realm of the Sacred Lands, where the dawn of a New Age has set in motion the most momentous of wars. As the prophecies long foretold, four races now clash with swords and sorcery in a desperate bid to control the destiny of their gods. Take on the quest as a champion of the Empire, the Mountain Clans, the Legions of the Damned, or the Undead Hordes and test your faith in battles of brute force, spellbinding magic and acts of guile. Slay demons, vanquish giants and combat merciless forces of the dead and undead. But to ensure the salvation of your god, the hero within must evolve.<br><br>The day of reckoning has come... and only the chosen will survive.','PC-DISC','disciples_sacred_lands','','',0);
INSERT INTO osc_products_description VALUES (25,1,'Microsoft Internet Keyboard PS/2','The Internet Keyboard has 10 Hot Keys on a comfortable standard keyboard design that also includes a detachable palm rest. The Hot Keys allow you to browse the web, or check e-mail directly from your keyboard. The IntelliType Pro software also allows you to customize your hot keys - make the Internet Keyboard work the way you want it to!','MSINTKB','microsoft_internet_keyboard_ps2','','',0);
INSERT INTO osc_products_description VALUES (26,1,'Microsoft IntelliMouse Explorer','Microsoft introduces its most advanced mouse, the IntelliMouse Explorer! IntelliMouse Explorer features a sleek design, an industrial-silver finish, a glowing red underside and taillight, creating a style and look unlike any other mouse. IntelliMouse Explorer combines the accuracy and reliability of Microsoft IntelliEye optical tracking technology, the convenience of two new customizable function buttons, the efficiency of the scrolling wheel and the comfort of expert ergonomic design. All these great features make this the best mouse for the PC!','MSIMEXP','microsoft_intellimouse_explorer','','www.microsoft.com/hardware/mouse/explorer.asp',0);
INSERT INTO osc_products_description VALUES (27,1,'Hewlett Packard LaserJet 1100Xi','HP has always set the pace in laser printing technology. The new generation HP LaserJet 1100 series sets another impressive pace, delivering a stunning 8 pages per minute print speed. The 600 dpi print resolution with HP\'s Resolution Enhancement technology (REt) makes every document more professional.<br><br>Enhanced print speed and laser quality results are just the beginning. With 2MB standard memory, HP LaserJet 1100xi users will be able to print increasingly complex pages. Memory can be increased to 18MB to tackle even more complex documents with ease. The HP LaserJet 1100xi supports key operating systems including Windows 3.1, 3.11, 95, 98, NT 4.0, OS/2 and DOS. Network compatibility available via the optional HP JetDirect External Print Servers.<br><br>HP LaserJet 1100xi also features The Document Builder for the Web Era from Trellix Corp. (featuring software to create Web documents).','HPLJ1100XI','hp_laserjet_1100xi','','www.pandi.hp.com/pandi-db/prodinfo.main?product=laserjet1100',0);

INSERT INTO osc_products_attributes VALUES (1,1,4,1,0.00,'+');
INSERT INTO osc_products_attributes VALUES (2,1,4,2,50.00,'+');
INSERT INTO osc_products_attributes VALUES (3,1,4,3,70.00,'+');
INSERT INTO osc_products_attributes VALUES (4,1,3,5,0.00,'+');
INSERT INTO osc_products_attributes VALUES (5,1,3,6,100.00,'+');
INSERT INTO osc_products_attributes VALUES (6,2,4,3,10.00,'-');
INSERT INTO osc_products_attributes VALUES (7,2,4,4,0.00,'+');
INSERT INTO osc_products_attributes VALUES (8,2,3,6,0.00,'+');
INSERT INTO osc_products_attributes VALUES (9,2,3,7,120.00,'+');
INSERT INTO osc_products_attributes VALUES (10,26,3,8,0.00,'+');
INSERT INTO osc_products_attributes VALUES (11,26,3,9,6.00,'+');
INSERT INTO osc_products_attributes VALUES (26, 22, 5, 10, '0.00', '+');
INSERT INTO osc_products_attributes VALUES (27, 22, 5, 13, '0.00', '+');

INSERT INTO osc_products_attributes_download VALUES (26, 'unreal.zip', 7, 3);

INSERT INTO osc_products_options VALUES (1,1,'Color');
INSERT INTO osc_products_options VALUES (2,1,'Size');
INSERT INTO osc_products_options VALUES (3,1,'Model');
INSERT INTO osc_products_options VALUES (4,1,'Memory');
INSERT INTO osc_products_options VALUES (5, 1, 'Version');

INSERT INTO osc_products_options_values VALUES (1,1,'4 mb');
INSERT INTO osc_products_options_values VALUES (2,1,'8 mb');
INSERT INTO osc_products_options_values VALUES (3,1,'16 mb');
INSERT INTO osc_products_options_values VALUES (4,1,'32 mb');
INSERT INTO osc_products_options_values VALUES (5,1,'Value');
INSERT INTO osc_products_options_values VALUES (6,1,'Premium');
INSERT INTO osc_products_options_values VALUES (7,1,'Deluxe');
INSERT INTO osc_products_options_values VALUES (8,1,'PS/2');
INSERT INTO osc_products_options_values VALUES (9,1,'USB');
INSERT INTO osc_products_options_values VALUES (10, 1, 'Download: Windows - English');
INSERT INTO osc_products_options_values VALUES (13, 1, 'Box: Windows - English');

INSERT INTO osc_products_options_values_to_products_options VALUES (1,4,1);
INSERT INTO osc_products_options_values_to_products_options VALUES (2,4,2);
INSERT INTO osc_products_options_values_to_products_options VALUES (3,4,3);
INSERT INTO osc_products_options_values_to_products_options VALUES (4,4,4);
INSERT INTO osc_products_options_values_to_products_options VALUES (5,3,5);
INSERT INTO osc_products_options_values_to_products_options VALUES (6,3,6);
INSERT INTO osc_products_options_values_to_products_options VALUES (7,3,7);
INSERT INTO osc_products_options_values_to_products_options VALUES (8,3,8);
INSERT INTO osc_products_options_values_to_products_options VALUES (9,3,9);
INSERT INTO osc_products_options_values_to_products_options VALUES (10, 5, 10);
INSERT INTO osc_products_options_values_to_products_options VALUES (13, 5, 13);

INSERT INTO osc_products_to_categories VALUES (1,4);
INSERT INTO osc_products_to_categories VALUES (2,4);
INSERT INTO osc_products_to_categories VALUES (3,9);
INSERT INTO osc_products_to_categories VALUES (4,10);
INSERT INTO osc_products_to_categories VALUES (5,11);
INSERT INTO osc_products_to_categories VALUES (6,10);
INSERT INTO osc_products_to_categories VALUES (7,12);
INSERT INTO osc_products_to_categories VALUES (8,13);
INSERT INTO osc_products_to_categories VALUES (9,10);
INSERT INTO osc_products_to_categories VALUES (10,10);
INSERT INTO osc_products_to_categories VALUES (11,10);
INSERT INTO osc_products_to_categories VALUES (12,10);
INSERT INTO osc_products_to_categories VALUES (13,10);
INSERT INTO osc_products_to_categories VALUES (14,15);
INSERT INTO osc_products_to_categories VALUES (15,14);
INSERT INTO osc_products_to_categories VALUES (16,15);
INSERT INTO osc_products_to_categories VALUES (17,10);
INSERT INTO osc_products_to_categories VALUES (18,10);
INSERT INTO osc_products_to_categories VALUES (19,12);
INSERT INTO osc_products_to_categories VALUES (20,15);
INSERT INTO osc_products_to_categories VALUES (21,18);
INSERT INTO osc_products_to_categories VALUES (22,19);
INSERT INTO osc_products_to_categories VALUES (23,20);
INSERT INTO osc_products_to_categories VALUES (24,20);
INSERT INTO osc_products_to_categories VALUES (25,8);
INSERT INTO osc_products_to_categories VALUES (26,9);
INSERT INTO osc_products_to_categories VALUES (27,5);

INSERT INTO osc_reviews VALUES (1,19,0,'John doe',5,1,'this has to be one of the funniest movies released for 1999!',now(),null,0,1);

INSERT INTO osc_specials VALUES (1,3, 39.99, now(), null, null, null, null, '1');
INSERT INTO osc_specials VALUES (2,5, 30.00, now(), null, null, null, null, '1');
INSERT INTO osc_specials VALUES (3,6, 30.00, now(), null, null, null, null, '1');
INSERT INTO osc_specials VALUES (4,16, 29.99, now(), null, null, null, null, '1');
