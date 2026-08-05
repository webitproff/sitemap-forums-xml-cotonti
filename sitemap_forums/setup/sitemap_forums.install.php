<?php
/**
 * Sitemap Forums – установка: добавление ссылок на карту форумов в robots.txt
 * и правил рерайта в .htaccess (если их ещё нет).
 *
 * Этот файл выполняется при установке плагина через админку Cotonti.
 * Он автоматически находит текущий файл robots.txt, удаляет из него все
 * старые упоминания sitemap-forums.xml и прямых ссылок (если они были) и добавляет
 * актуальные строки в зависимости от того, включены ли ЧПУ (URLEditor).
 * Также при необходимости добавляет правила rewrite в корневой .htaccess.
 *
 * Filename:    plugins/sitemap_forums/setup/sitemap_forums.install.php
 * Plugin URI:  https://abuyfile.com/ru/page/cotonti/plugs/sitemap-forums-xml
 * Support:     https://abuyfile.com/forums/cotonti/custom/plugs/
 * Source:      https://github.com/webitproff/sitemap-forums-xml-cotonti
 *
 * Date:       Aug 5, 2026
 * @package    sitemap_forums
 * @version    1.1.0
 * @author     webitproff
 * @copyright  Copyright (c) webitproff 2026 | https://github.com/webitproff/sitemap-forums-xml-cotonti
 * @license    BSD
 */

defined('COT_CODE') or die('Wrong URL');

// ================== 1. Добавление ссылок в robots.txt ==================

// Путь к robots.txt относительно корня сайта
$robotsFile = './robots.txt';

// Проверяем, что файл существует и доступен для записи
if (file_exists($robotsFile) && is_writable($robotsFile)) {
    // Читаем текущее содержимое robots.txt построчно
    $lines = file($robotsFile);
    $newLines = [];

    // Собираем базовый URL сайта, удаляя возможный конечный слеш
    $baseUrl = rtrim(Cot::$cfg['mainurl'], '/');

    // Проверяем, работают ли ЧПУ через плагин URLEditor
    $handyEnabled = cot_plugin_active('urleditor') 
        && !empty(Cot::$cfg['plugin']['urleditor']['preset']) 
        && Cot::$cfg['plugin']['urleditor']['preset'] !== 'none';

    // Формируем список ссылок для добавления
    // Прямые ссылки на основную карту и индекс нужны всегда, независимо от ЧПУ
    $sitemapLinks = [
        $baseUrl . '/index.php?r=sitemap_forums',             // основная карта
        $baseUrl . '/index.php?r=sitemap_forums&a=index',     // индекс
    ];

    // Если ЧПУ включены, добавляем к уже существующему массиву прямых ссылок
    // ещё и "красивые" URL (через XML-файлы), чтобы поисковики могли их найти.
    // Используем array_merge, чтобы не дублировать перечисление прямых ссылок,
    // а просто объединить базовый массив с новыми элементами.
    if ($handyEnabled) {
        $sitemapLinks = array_merge($sitemapLinks, [
            $baseUrl . '/sitemap-forums.xml',
            $baseUrl . '/sitemap-forums-index.xml',
        ]);
    }

    // Удаляем все старые строки, содержащие sitemap-forums или sitemap_forums
    // (это позволяет избежать дублирования при повторных установках)
    foreach ($lines as $line) {
        if (stripos($line, 'sitemap-forums') === false && stripos($line, 'sitemap_forums') === false) {
            $newLines[] = $line;
        }
    }

    // Добавляем новые строки Sitemap
    foreach ($sitemapLinks as $url) {
        $newLines[] = "\nSitemap: " . $url . "\n";
    }

    // Записываем обновлённое содержимое обратно в robots.txt
    file_put_contents($robotsFile, implode('', $newLines));
}

// ================== 2. Добавление правил в .htaccess ==================

/**
 * Добавляет указанные правила RewriteRule в корневой .htaccess,
 * если их там ещё нет. Вставка производится после строки
 * RewriteBase "/" или # Sitemap shortcut (если они существуют).
 * В противном случае правила добавляются после RewriteEngine On.
 *
 * @param array $rules Массив строк с правилами RewriteRule
 */
function sitemap_forums_add_htaccess_rules($rules) {
    $htaccessFile = './.htaccess';
    if (!file_exists($htaccessFile) || !is_writable($htaccessFile)) {
        return; // файл недоступен – ничего не делаем
    }

    $content = file_get_contents($htaccessFile);

    // Проверяем, есть ли уже все указанные правила
    $alreadyExists = true;
    foreach ($rules as $rule) {
        if (stripos($content, $rule) === false) {
            $alreadyExists = false;
            break;
        }
    }
    if ($alreadyExists) {
        return; // все правила уже есть, выходим
    }

    // Определяем место для вставки:
    // 1. После строки RewriteBase "/"
    // 2. Если её нет – после строки # Sitemap shortcut
    // 3. Если и её нет – после RewriteEngine On
    $insertPos = false;

    if (($pos = stripos($content, 'RewriteBase "/"')) !== false) {
        $insertPos = strpos($content, "\n", $pos);
    } elseif (($pos = stripos($content, '# Sitemap shortcut')) !== false) {
        $insertPos = strpos($content, "\n", $pos);
    } elseif (($pos = stripos($content, 'RewriteEngine On')) !== false) {
        $insertPos = strpos($content, "\n", $pos);
    }

    if ($insertPos !== false) {
        // Перемещаемся на начало следующей строки (сразу после \n)
        $insertPos++; // теперь указывает на первый символ следующей строки
        $newContent = substr_replace($content, "\n" . implode("\n", $rules) . "\n", $insertPos, 0);
    } else {
        // Если ни одного якоря не найдено, добавляем в конец файла
        $newContent = $content . "\n" . implode("\n", $rules) . "\n";
    }

    file_put_contents($htaccessFile, $newContent);
}

// Вызываем функцию с нужными правилами для sitemap_forums
sitemap_forums_add_htaccess_rules([
    'RewriteRule ^sitemap-forums\.xml$ index.php?r=sitemap_forums [L]',
    'RewriteRule ^sitemap-forums-index\.xml$ index.php?r=sitemap_forums&a=index [L]',
]);