<?php
/**
 * Russian language file for Sitemap Forums plugin
 */

defined('COT_CODE') or die('Wrong URL.');

$L['sitemap_forums_title'] = 'XML-карта форумов';
$L['sitemap_forums_description'] = 'Генерирует XML-карту сайта только для разделов форума';

$sitemap_forums_freqs = [
    'default' => 'По умолчанию',
    'always'  => 'Всегда',
    'hourly'  => 'Ежечасно',
    'daily'   => 'Ежедневно',
    'weekly'  => 'Еженедельно',
    'monthly' => 'Ежемесячно',
    'yearly'  => 'Ежегодно',
    'never'   => 'Никогда',
];

$L['cfg_cache_ttl']      = 'Время жизни кеша (секунд)';
$L['cfg_perpage']        = 'Максимум ссылок на одну часть карты';
$L['cfg_perpage_hint']   = 'При превышении карта делится на части. См. index.php?r=sitemap_forums&a=index';
$L['cfg_freq']           = 'Частота изменения по умолчанию';
$L['cfg_freq_params']    = $sitemap_forums_freqs;
$L['cfg_prio']           = 'Приоритет по умолчанию';
$L['cfg_forumsSep']      = 'Форумы';
$L['cfg_forums']         = 'Включить форумы';
$L['cfg_forums_freq']    = 'Частота изменения форумов';
$L['cfg_forums_freq_params'] = $sitemap_forums_freqs;
$L['cfg_forums_prio']    = 'Приоритет страниц форума';
$L['cfg_include_posts'] = 'Включать отдельные посты в карту сайта';
$L['cfg_use_pretty_urls'] = 'Использовать ЧПУ для карты (например, sitemap-forums.xml)'; // ru