<?php
/**
 * English Language File for Sitemap Forums Plugin
 *
 * All text strings used by the plugin in the Cotonti interface:
 * - plugin name and description (info_name, info_desc)
 * - admin settings (cfg_…)
 * - field hints (cfg_…_hint)
 * - dropdown values (cfg_…_params)
 *
 * Filename: plugins/sitemap_forums/lang/sitemap_forums.en.lang.php
 *
 * Plugin URI:  https://abuyfile.com/ru/market/cotonti/plugs/sitemap-forums-xml
 * Support:     https://abuyfile.com/forums/cotonti/custom/plugs/
 * Source:      https://github.com/webitproff/sitemap-forums-xml-cotonti
 *
 * Date: Aug 5, 2026
 * @package sitemap_forums
 * @version 1.1.1
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/sitemap-forums-xml-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

// ========================
// PLUGIN INFO (ADMIN PANEL)
// ========================
$L['info_name']  = 'Sitemap Forums (forum map)';
$L['info_desc']  = 'Generates an XML sitemap only for forum sections (categories, topics, and optionally individual posts).';
$L['info_notes'] = 'After installation, add/check the rules in .htaccess and robots.txt. <strong>Details <a target="_blank" href="https://github.com/webitproff/sitemap-forums-xml-cotonti/blob/main/README.md">in the README.md documentation</a></strong>.';

// ========================
// TITLES AND DESCRIPTIONS (same values, pulled by other keys)
// ========================
$L['sitemap_forums_title']       = $L['info_name'];
$L['sitemap_forums_desc']        = $L['info_desc'];

// ========================
// CHANGE FREQUENCIES (COMMON)
// ========================
$sitemap_forums_freqs = [
    'default' => 'Default',
    'always'  => 'Always',
    'hourly'  => 'Hourly',
    'daily'   => 'Daily',
    'weekly'  => 'Weekly',
    'monthly' => 'Monthly',
    'yearly'  => 'Yearly',
    'never'   => 'Never',
];

// ========================
// PLUGIN SETTINGS (ADMIN PANEL)
// ========================

// --- Cache and default frequency ---
$L['cfg_cache_ttl']          = 'Cache lifetime (seconds)';
$L['cfg_cache_ttl_hint']     = 'After how many seconds the sitemap will be regenerated upon request.';
$L['cfg_freq']               = 'Default change frequency';
$L['cfg_freq_params']        = $sitemap_forums_freqs;
$L['cfg_prio']               = 'Default priority';

// --- Maximum number of links per file ---
$L['cfg_perpage']            = 'Max items per sitemap page';
$L['cfg_perpage_hint']       = 'If there are more entries, the sitemap is split into multiple files (sitemap index).';

// --- Clean URLs ---
$L['cfg_use_pretty_urls']    = 'Use clean URLs for the sitemap';
$L['cfg_use_pretty_urls_hint'] = 'If enabled, sitemap addresses will look like /sitemap-forums.xml and /sitemap-forums-index.xml. Otherwise, direct links with index.php are used.';

// --- Forum settings ---
$L['cfg_forumsSep']           = 'Forums';
$L['cfg_forums']              = 'Include forum sections';
$L['cfg_forums_hint']         = 'Add links to categories, topics, and (if enabled) individual forum posts to the sitemap.';
$L['cfg_forums_freq']         = 'Forum change frequency';
$L['cfg_forums_freq_params']  = $sitemap_forums_freqs;
$L['cfg_forums_prio']         = 'Forum page priority';

// --- Individual posts ---
$L['cfg_include_posts']       = 'Include individual posts in the sitemap';
$L['cfg_include_posts_hint']  = 'If enabled, each forum post (message) will be added as a separate URL in the sitemap. Note: on forums with many messages, this can significantly increase the sitemap size and generation time.';
