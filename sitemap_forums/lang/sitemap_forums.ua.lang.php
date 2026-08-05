<?php
/**
 * Ukrainian Language File for Sitemap Forums Plugin
 *
 * Усі текстові рядки, що використовуються плагіном в інтерфейсі Cotonti:
 * - назва та опис плагіна (info_name, info_desc)
 * - налаштування в адмін-панелі (cfg_…)
 * - підказки до полів (cfg_…_hint)
 * - значення для випадаючих списків (cfg_…_params)
 *
 * Filename: plugins/sitemap_forums/lang/sitemap_forums.uk.lang.php
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
// ІНФОРМАЦІЯ ПРО ПЛАГІН (АДМІНКА)
// ========================
$L['info_name']  = 'Sitemap Forums (карта форумів)';
$L['info_desc']  = 'Генерує XML-карту сайту тільки для розділів форуму (категорії, теми та, опціонально, окремі пости).';
$L['info_notes'] = 'Після встановлення додайте/перевірте правила в .htaccess та robots.txt. <strong>Подробиці <a target="_blank" href="https://github.com/webitproff/sitemap-forums-xml-cotonti/blob/main/README.md"> у документації README.md</a></strong>.';

// ========================
// ЗАГОЛОВКИ ТА ОПИСИ (ті самі значення, підтягуються іншими ключами)
// ========================
$L['sitemap_forums_title']       = $L['info_name'];
$L['sitemap_forums_desc']        = $L['info_desc'];

// ========================
// ЧАСТОТИ ОНОВЛЕННЯ (ЗАГАЛЬНІ)
// ========================
$sitemap_forums_freqs = [
    'default' => 'За замовчуванням',
    'always'  => 'Завжди',
    'hourly'  => 'Щогодини',
    'daily'   => 'Щодня',
    'weekly'  => 'Щотижня',
    'monthly' => 'Щомісяця',
    'yearly'  => 'Щороку',
    'never'   => 'Ніколи',
];

// ========================
// НАЛАШТУВАННЯ ПЛАГІНА (АДМІНКА)
// ========================

// --- Кеш і частота за замовчуванням ---
$L['cfg_cache_ttl']          = 'Час життя кешу (секунд)';
$L['cfg_cache_ttl_hint']     = 'Через скільки секунд карта буде перезгенерована при зверненні.';
$L['cfg_freq']               = 'Частота змін за замовчуванням';
$L['cfg_freq_params']        = $sitemap_forums_freqs;
$L['cfg_prio']               = 'Пріоритет за замовчуванням';

// --- Максимальна кількість посилань в одному файлі ---
$L['cfg_perpage']            = 'Макс. посилань на частину карти';
$L['cfg_perpage_hint']       = 'Якщо записів більше, карта розбивається на декілька файлів (sitemap index).';

// --- ЧПУ (зрозумілі URL) ---
$L['cfg_use_pretty_urls']    = 'Використовувати зрозумілі URL для карти сайту';
$L['cfg_use_pretty_urls_hint'] = 'Якщо увімкнено, адреси карт матимуть вигляд /sitemap-forums.xml та /sitemap-forums-index.xml. Інакше – прямі посилання з index.php.';

// --- Налаштування для форумів ---
$L['cfg_forumsSep']           = 'Форуми';
$L['cfg_forums']              = 'Увімкнути розділи форуму';
$L['cfg_forums_hint']         = 'Додавати посилання на категорії, теми та (якщо увімкнено) окремі пости форуму до карти сайту.';
$L['cfg_forums_freq']         = 'Частота змін форумів';
$L['cfg_forums_freq_params']  = $sitemap_forums_freqs;
$L['cfg_forums_prio']         = 'Пріоритет сторінок форуму';

// --- Окремі пости ---
$L['cfg_include_posts']       = 'Включати окремі пости в карту сайту';
$L['cfg_include_posts_hint']  = 'Якщо увімкнено, кожен пост (повідомлення) форуму буде додано як окремий URL до карти. Увага: на форумах з великою кількістю повідомлень це може значно збільшити розмір карти та час генерації.';