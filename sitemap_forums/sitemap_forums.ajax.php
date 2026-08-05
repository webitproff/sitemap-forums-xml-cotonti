<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */

/**
 * Sitemap Forums – генератор XML-карты сайта для модуля Forums.
 *
 * Этот скрипт вызывается через AJAX‑хук Cotonti (index.php?r=sitemap_forums).
 * В зависимости от параметров он:
 *   - генерирует (или берёт из кеша) основную карту форума,
 *   - генерирует индексный файл (sitemap index), если ссылок больше,
 *     чем разрешено в одном файле (настройка perpage).
 *
 * Все сгенерированные XML‑файлы сохраняются в папке datas/cache/sitemap_forums/.
 */

defined('COT_CODE') or die('Wrong URL');

// ---------------------------------------------------------------
// 1. ИНИЦИАЛИЗАЦИЯ И ЗАЩИТА ВЫВОДА
// ---------------------------------------------------------------

// Очищаем буфер вывода, чтобы никакие случайные пробелы или BOM
// не попали в XML и не сломали заголовки.
if (ob_get_level()) ob_clean();
error_reporting(0);            // Отключаем показ ошибок в выводе
ini_set('display_errors', 0);  // (для чистого XML)
ob_start();                    // Запускаем новый буфер

// Подключаем файл с функциями плагина (лежит в inc/sitemap_forums.functions.php)
require_once cot_incfile('sitemap_forums', 'plug');

// Устанавливаем правильный Content-Type – XML
header('Content-Type: application/xml; charset=utf-8');

// ---------------------------------------------------------------
// 2. ПАРАМЕТРЫ ЗАПРОСА
// ---------------------------------------------------------------

// $d – номер части карты (если она разбита на несколько файлов)
$d = cot_import('d', 'G', 'INT') ?? 0;
// $a – действие: 'index' для индексного файла, иначе основная карта
$a = cot_import('a', 'G', 'ALP') ?? '';

// ---------------------------------------------------------------
// 3. НАСТРОЙКИ ПЛАГИНА
// ---------------------------------------------------------------

$cfgPlug = Cot::$cfg['plugin']['sitemap_forums'] ?? [];
$perpage = (int) ($cfgPlug['perpage'] ?? 50000);   // макс. URL в одном файле
$cache_ttl = (int) ($cfgPlug['cache_ttl'] ?? 3600); // время жизни кеша (сек.)

// Папка для кеша карт
$cacheDir = rtrim(Cot::$cfg['cache_dir'] ?? 'datas/cache', '/') . '/sitemap_forums';
if (!file_exists($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

// ================================================================
// 4. ГЕНЕРАЦИЯ / ВЫВОД ИНДЕКСНОГО ФАЙЛА (sitemap index)
// ================================================================
if ($a === 'index') {
    $indexFile = $cacheDir . '/sitemap_forums_index.xml';
    $needRegen = true;

    // Проверяем, не просрочен ли кеш индексного файла
    if (file_exists($indexFile) && (Cot::$sys['now'] - filemtime($indexFile)) < $cache_ttl) {
        $needRegen = false;   // кеш ещё актуален
    }

    if ($needRegen) {
        // Узнаём, сколько всего URL было сгенерировано (из файла sitemap.count)
        $countFile = $cacheDir . '/sitemap.count';
        $items = 0;
        if (file_exists($countFile)) {
            $items = (int) file_get_contents($countFile);
        }

        // Сколько потребуется файлов (частей)
        $t = new XTemplate(cot_tplfile('sitemap_forums.index', 'plug'));
        $pages = max(1, (int) ceil($items / $perpage));

        // Формируем ссылки на каждую часть карты
        $usePretty = (bool) ($cfgPlug['use_pretty_urls'] ?? false);
        for ($pg = 0; $pg < $pages; $pg++) {
            if ($usePretty) {
                // Красивый URL: /sitemap-forums.xml?d=1
                $url = Cot::$cfg['mainurl'] . '/sitemap-forums.xml';
                if ($pg > 0) $url .= '?d=' . $pg;
            } else {
                // Прямая ссылка: index.php?r=sitemap_forums&d=1
                $dParam = $pg > 0 ? "&d={$pg}" : '';
                $url = Cot::$cfg['mainurl'] . '/index.php?r=sitemap_forums' . $dParam;
            }
            $t->assign([
                'SITEMAP_ROW_URL'  => $url,
                'SITEMAP_ROW_DATE' => sitemap_forums_date(time()),
            ]);
            $t->parse('MAIN.SITEMAP_ROW');
        }
        $t->parse('MAIN');

        // Убираем возможный BOM и дублирующиеся XML-заголовки,
        // подставляем свой правильный заголовок
        $indexXml = $t->text();
        $indexXml = preg_replace('/^[\s\xEF\xBB\xBF]+/', '', $indexXml);
        $indexXml = preg_replace('/^<\?xml.*?\?>\s*/', '', $indexXml);
        $indexXml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $indexXml;

        // Сохраняем индексный файл в кеш
        file_put_contents($indexFile, $indexXml);
    }

    // Отдаём готовый индекс из кеша
    header('Content-Type: application/xml; charset=utf-8');
    echo file_get_contents($indexFile);
    ob_end_flush();
    exit;
}

// ================================================================
// 5. ГЕНЕРАЦИЯ / ВЫВОД ОСНОВНОЙ КАРТЫ
// ================================================================
$countFile = $cacheDir . '/sitemap.count';
$needRegen = true;
$items = 0;   // общее количество добавленных URL

// Проверяем актуальность кеша по времени изменения файла-счётчика
if (file_exists($countFile) && filesize($countFile) > 0) {
    $mtime = filemtime($countFile);
    if ($mtime !== false && (Cot::$sys['now'] - $mtime) < $cache_ttl) {
        $needRegen = false;
        $items = (int) file_get_contents($countFile);
    }
}

// Если кеш просрочен или отсутствует – пересоздаём карту
if ($needRegen) {
    $t = new XTemplate(cot_tplfile('sitemap_forums', 'plug'));
    $items = 0;   // начинаем подсчёт заново

    // Проверяем, что модуль forums активен и опция "Включить форумы" включена
    if (($cfgPlug['forums'] ?? true) && cot_module_active('forums')) {
        require_once cot_incfile('forums', 'module');

        // -----------------------------------------------------------
        // 5.1. СТАТИСТИКА ФОРУМОВ
        // -----------------------------------------------------------
        // Собираем информацию о каждом разделе (количество тем, дата последнего поста)
        $cat_top = [];
        $res = Cot::$db->query('SELECT * FROM ' . Cot::$db->forum_stats . ' ORDER BY fs_cat DESC');
        while ($row = $res->fetch()) {
            $cat_top[$row['fs_cat']] = $row;
        }
        $res->closeCursor();

        // -----------------------------------------------------------
        // 5.2. РАЗДЕЛЫ ФОРУМА (КАТЕГОРИИ)
        // -----------------------------------------------------------
        // Здесь мы добавляем страницы со списком тем внутри каждого раздела
        $auth_cache = [];                // кеш прав доступа
        $maxTopicsPerPage = (int) (Cot::$cfg['forums']['maxtopicsperpage'] ?? 30);
        $category_list = Cot::$structure['forums'] ?? [];

        // Хук для модификации списка категорий (если нужно)
        foreach (cot_getextplugins('sitemap_forums.categorylist') as $pl) {
            include $pl;
        }

        // Перебираем все разделы форума
        foreach ($category_list as $c => $cat) {
            if (!is_array($cat)) continue;

            // Проверяем право на чтение раздела
            $auth_cache[$c] = cot_auth('forums', $c, 'R');
            if (!$auth_cache[$c] || substr_count($cat['path'] ?? '', '.') === 0) continue;

            // Сколько страниц со списком тем получится для этого раздела
            $count = isset($cat_top[$c]['fs_topiccount']) ? (int) $cat_top[$c]['fs_topiccount'] : 0;
            $subs = max(1, (int) floor($count / $maxTopicsPerPage) + 1);
            $easypagenav = Cot::$cfg['easypagenav'] ?? false;

            // Генерируем URL для каждой страницы списка тем
            for ($pg = 1; $pg <= $subs; $pg++) {
                $offset = $easypagenav ? $pg : ($pg - 1) * $maxTopicsPerPage;
                $urlp = $pg > 1 ? "m=topics&s={$c}&d={$offset}" : "m=topics&s={$c}";
                sitemap_forums_parse($t, $items, [
                    'url'  => cot_url('forums', $urlp),
                    'date' => (int) ($cat_top[$c]['fs_lt_date'] ?? 0),
                    'freq' => $cfgPlug['forums_freq'] ?? 'daily',
                    'prio' => $cfgPlug['forums_prio'] ?? '0.5',
                ]);
            }
        }

        // -----------------------------------------------------------
        // 5.3. ТЕМЫ (ТОПИКИ)
        // -----------------------------------------------------------
        // Добавляем страницы с постами внутри каждой темы
        $sitemap_join_columns = '';
        $sitemap_join_tables = '';
        $sitemap_where = [];

        // Хук для модификации запроса
        foreach (cot_getextplugins('sitemap_forums.query') as $pl) {
            include $pl;
        }

        $whereSQL = !empty($sitemap_where) ? 'WHERE ' . implode(' AND ', $sitemap_where) : '';

        // Выбираем все темы с количеством постов
        $res = Cot::$db->query(
            "SELECT t.ft_id, t.ft_cat, t.ft_updated, t.ft_postcount {$sitemap_join_columns}
             FROM " . Cot::$db->forum_topics . " t {$sitemap_join_tables}
             LEFT JOIN " . Cot::$db->structure . " s ON (s.structure_area = 'forums' AND t.ft_cat = s.structure_code)
             {$whereSQL}
             ORDER BY t.ft_cat"
        );

        $maxPostsPerPage = (int) (Cot::$cfg['forums']['maxpostsperpage'] ?? 15);
        $easypagenav = Cot::$cfg['easypagenav'] ?? false;

        while ($row = $res->fetch()) {
            $cat = $row['ft_cat'] ?? '';
            if (empty($cat) || empty($auth_cache[$cat])) continue;

            $q = (int) ($row['ft_id'] ?? 0);           // ID темы
            $postCount = (int) ($row['ft_postcount'] ?? 0); // сколько всего постов
            $subs = max(1, (int) floor($postCount / $maxPostsPerPage) + 1);

            // Добавляем каждую страницу постов для этой темы
            for ($pg = 1; $pg <= $subs; $pg++) {
                $offset = $easypagenav ? $pg : ($pg - 1) * $maxPostsPerPage;
                $urlp = $pg > 1 ? "m=posts&q={$q}&d={$offset}" : "m=posts&q={$q}";
                sitemap_forums_parse($t, $items, [
                    'url'  => cot_url('forums', $urlp),
                    'date' => (int) ($row['ft_updated'] ?? 0),
                    'freq' => $cfgPlug['forums_freq'] ?? 'daily',
                    'prio' => $cfgPlug['forums_prio'] ?? '0.5',
                ]);
            }
            unset($row);
        }
        $res->closeCursor();
        unset($cat_top);

        // -----------------------------------------------------------
        // 5.4. ИНДИВИДУАЛЬНЫЕ ПОСТЫ (СООБЩЕНИЯ) – ОПЦИОНАЛЬНО
        // -----------------------------------------------------------
        // Этот блок срабатывает, только если в настройках плагина
        // включена опция include_posts.
        // Добавляет прямые ссылки на каждый отдельный пост форума.
        // -----------------------------------------------------------
        if (!empty($cfgPlug['include_posts'])) {
            // Выбираем все посты вместе с категорией, чтобы проверить права доступа
            $postRes = Cot::$db->query(
                "SELECT p.fp_id, p.fp_topicid, t.ft_cat, p.fp_updated
                 FROM " . Cot::$db->forum_posts . " AS p
                 LEFT JOIN " . Cot::$db->forum_topics . " AS t ON p.fp_topicid = t.ft_id
                 LEFT JOIN " . Cot::$db->structure . " AS s ON (s.structure_area = 'forums' AND t.ft_cat = s.structure_code)
                 ORDER BY t.ft_cat, p.fp_id"
            );

            while ($prow = $postRes->fetch()) {
                // Категория, к которой относится пост
                $cat = $prow['ft_cat'] ?? '';
                // Если категория недоступна для чтения – пропускаем
                if (empty($cat) || empty($auth_cache[$cat])) {
                    continue;
                }

                // URL на отдельный пост: /forums/…/post123
                sitemap_forums_parse($t, $items, [
                    'url'  => cot_url('forums', 'm=posts&id=' . (int)$prow['fp_id']),
                    'date' => (int) ($prow['fp_updated'] ?? 0),
                    'freq' => $cfgPlug['forums_freq'] ?? 'daily',
                    'prio' => $cfgPlug['forums_prio'] ?? '0.5',
                ]);
            }
            $postRes->closeCursor();
        }
        // -----------------------------------------------------------
    }

    // Хук для добавления сторонних данных (другие плагины)
    foreach (cot_getextplugins('sitemap_forums.main') as $pl) {
        include $pl;
    }

    // Сохраняем последнюю часть карты и записываем счётчик
    $t->parse();
    sitemap_forums_save($t->text(), (int) ceil($items / $perpage) - 1);
    file_put_contents($countFile, $items);
}

// ---------------------------------------------------------------
// 6. ОТДАЧА ГОТОВОГО ФАЙЛА КАРТЫ (ОСНОВНАЯ ИЛИ ЧАСТЬ)
// ---------------------------------------------------------------
$cacheFile = $d > 0
    ? $cacheDir . "/sitemap.{$d}.xml"
    : $cacheDir . '/sitemap.xml';

header('Content-Type: application/xml; charset=utf-8');
if (file_exists($cacheFile)) {
    $xml = file_get_contents($cacheFile);
    // Очистка от BOM и лишних XML-деклараций
    $xml = preg_replace('/^[\s\xEF\xBB\xBF]+/', '', $xml);
    $xml = preg_replace('/^<\?xml.*?\?>\s*/', '', $xml);
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $xml;
} else {
    // Если файла нет (например, форум пуст) – отдаём пустой urlset
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
       . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
}

ob_end_flush();
exit;
