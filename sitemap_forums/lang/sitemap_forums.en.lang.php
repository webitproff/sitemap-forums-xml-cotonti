<?php
/**
 * English language file for Sitemap Forums plugin
 */

defined('COT_CODE') or die('Wrong URL.');

$L['sitemap_forums_title'] = 'Forums Sitemap';
$L['sitemap_forums_description'] = 'Generates XML sitemap for forum sections only';

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

$L['cfg_cache_ttl']      = 'Cache TTL (seconds)';
$L['cfg_perpage']        = 'Max items per sitemap page';
$L['cfg_perpage_hint']   = 'If exceeded, the map is split. See index.php?r=sitemap_forums&a=index';
$L['cfg_freq']           = 'Default change frequency';
$L['cfg_freq_params']    = $sitemap_forums_freqs;
$L['cfg_prio']           = 'Default priority';
$L['cfg_forumsSep']      = 'Forums';
$L['cfg_forums']         = 'Include forums';
$L['cfg_forums_freq']    = 'Forums change frequency';
$L['cfg_forums_freq_params'] = $sitemap_forums_freqs;
$L['cfg_forums_prio']    = 'Forums priority';
$L['cfg_include_posts'] = 'Include individual posts in sitemap';
$L['cfg_use_pretty_urls'] = 'Use pretty URLs for sitemap (e.g. sitemap-forums.xml)';     // en