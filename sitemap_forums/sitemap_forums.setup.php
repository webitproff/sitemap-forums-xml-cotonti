<?php
/* ====================
[BEGIN_COT_EXT]
Code=sitemap_forums
Name=Forums Sitemap
Category=seo
Description=XML sitemap for forums only
Version=1.1.1
Date=2026-08-05
Author=webitproff
Copyright=Copyright (c) webitproff 2026 https://github.com/webitproff
Notes=BSD License
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
Requires_modules=forums
Requires_plugins=
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
cache_ttl=01:string::3600:Cache TTL in seconds
perpage=10:string::50000:Max items per sitemap page
freq=20:select:default,always,hourly,daily,weekly,monthly,yearly,never:default:Default change frequency
prio=30:select:0.0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0:0.5:Default priority
forumsSep=40:separator:::Forums
forums=43:radio::1:Include forums
forums_freq=46:select:default,always,hourly,daily,weekly,monthly,yearly,never:daily:Forums change frequency
forums_prio=49:select:0.0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1.0:0.5:Forums priority
use_pretty_urls=52:radio::1:Use pretty URLs for sitemap (e.g. sitemap-forums.xml)
include_posts=55:radio::0:Include individual posts in sitemap
[END_COT_EXT_CONFIG]
==================== */

defined('COT_CODE') or die('Wrong URL');


/**
 * sitemap_forums.setup.php - Register data in $db_core and $db_config. Setup & Config File for the Plugin sitemap_forums
 * Filename: sitemap_forums.setup.php
 *
 * sitemap_forums plugin for Cotonti v1.+, PHP 8.5+, MySQL 8.4
 *
 * ReadMeMore:       https://abuyfile.com/ru/market/cotonti/plugs/sitemap-forums-xml
 * Support:          https://abuyfile.com/forums/cotonti/custom/plugs/
 * Source:           https://github.com/webitproff/sitemap-forums-xml-cotonti
 * 
 * Date: Aug 5Th, 2026
 * @package sitemap_forums
 * @version 1.1.1
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/sitemap-forums-xml-cotonti
 * @license BSD
 */
