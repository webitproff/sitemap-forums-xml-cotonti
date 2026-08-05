<?php
/**
 * Russian Language File for Sitemap Forums Plugin
 *
 * Все текстовые строки, используемые плагином в интерфейсе Cotonti:
 * - название и описание плагина (info_name, info_desc)
 * - настройки в админ-панели (cfg_…)
 * - подсказки к полям (cfg_…_hint)
 * - значения для выпадающих списков (cfg_…_params)
 *
 * Filename: plugins/sitemap_forums/lang/sitemap_forums.ru.lang.php
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
// ИНФОРМАЦИЯ О ПЛАГИНЕ (АДМИНКА)
// ========================
$L['info_name']  = 'Sitemap Forums (карта форумов)';
$L['info_desc']  = 'Генерирует XML-карту сайта только для разделов форума (категории, темы и, опционально, отдельные посты).';
$L['info_notes'] = 'После установки добавить/проверить правила в .htaccess и robots.txt. <strong>Подробности <a target="_blank" href="https://github.com/webitproff/sitemap-forums-xml-cotonti/blob/main/README.md"> в документации README.md</a></strong>.';

// ========================
// ЗАГОЛОВКИ И ОПИСАНИЯ (где те же значения, вытягиваются другими ключами)
// ========================
$L['sitemap_forums_title']       = $L['info_name'];
$L['sitemap_forums_desc']        = $L['info_desc'];

// ========================
// ЧАСТОТЫ ОБНОВЛЕНИЯ (ОБЩИЕ)
// ========================
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

// ========================
// НАСТРОЙКИ ПЛАГИНА (АДМИНКА)
// ========================

// --- Кеш и частота по умолчанию ---
$L['cfg_cache_ttl']          = 'Время жизни кеша (секунд)';
$L['cfg_cache_ttl_hint']     = 'Через сколько секунд карта будет пересоздана при обращении.';
$L['cfg_freq']               = 'Частота изменения по умолчанию';
$L['cfg_freq_params']        = $sitemap_forums_freqs;
$L['cfg_prio']               = 'Приоритет по умолчанию';

// --- Максимальное количество ссылок в одном файле ---
$L['cfg_perpage']            = 'Макс. ссылок на часть карты';
$L['cfg_perpage_hint']       = 'Если записей больше, карта разбивается на несколько файлов (sitemap index).';

// --- Красивые URL ---
$L['cfg_use_pretty_urls']    = 'Использовать красивые URL для карты сайта';
$L['cfg_use_pretty_urls_hint'] = 'Если включено, адреса карт будут вида /sitemap-forums.xml и /sitemap-forums-index.xml. Иначе – прямые ссылки с index.php.';

// --- Настройки для форумов ---
$L['cfg_forumsSep']           = 'Форумы';
$L['cfg_forums']              = 'Включить разделы форума';
$L['cfg_forums_hint']         = 'Добавлять ссылки на категории, темы и (если включено) отдельные посты форума в карту сайта.';
$L['cfg_forums_freq']         = 'Частота изменения форумов';
$L['cfg_forums_freq_params']  = $sitemap_forums_freqs;
$L['cfg_forums_prio']         = 'Приоритет страниц форума';

// --- Отдельные посты ---
$L['cfg_include_posts']       = 'Включать отдельные посты в карту сайта';
$L['cfg_include_posts_hint']  = 'Если включено, каждый пост (сообщение) форума будет добавлен как отдельный URL в карту. Внимание: на форумах с большим количеством сообщений это может значительно увеличить размер карты и время генерации.';
