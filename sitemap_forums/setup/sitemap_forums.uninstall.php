<?php
/**
 * Sitemap Forums – удаление: очистка robots.txt и .htaccess от ссылок и правил,
 * добавленных при установке плагина.
 *
 * Этот файл выполняется при удалении плагина через админку Cotonti.
 * Он автоматически находит и удаляет все строки, содержащие sitemap-forums или
 * sitemap_forums в robots.txt, а также правила рерайта для sitemap-forums.xml
 * и sitemap-forums-index.xml из .htaccess.
 *
 * Filename:    plugins/sitemap_forums/setup/sitemap_forums.uninstall.php
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

defined('COT_CODE') or die('Wrong URL');

// ================== 1. Очистка robots.txt ==================

// Путь к robots.txt относительно корня сайта
$robotsFile = './robots.txt';

// Проверяем, что файл существует и доступен для записи
if (file_exists($robotsFile) && is_writable($robotsFile)) {
    // Читаем текущее содержимое построчно
    $lines = file($robotsFile);
    $newLines = [];

    // Проходим по всем строкам и удаляем те, которые содержат
    // любые упоминания sitemap-forums или sitemap_forums (оба варианта)
    foreach ($lines as $line) {
        if (stripos($line, 'sitemap-forums') === false && stripos($line, 'sitemap_forums') === false) {
            $newLines[] = $line;
        }
    }

    // Записываем очищенное содержимое обратно (без добавления новых строк)
    file_put_contents($robotsFile, implode('', $newLines));
}

// ================== 2. Очистка .htaccess ==================

$htaccessFile = './.htaccess';
if (file_exists($htaccessFile) && is_writable($htaccessFile)) {
    $content = file_get_contents($htaccessFile);

    // Массив правил, которые нужно удалить (точно такие же, как добавлялись)
    $rulesToRemove = [
        'RewriteRule ^sitemap-forums\.xml$ index.php?r=sitemap_forums [L]',
        'RewriteRule ^sitemap-forums-index\.xml$ index.php?r=sitemap_forums&a=index [L]',
    ];

    // Для каждого правила ищем его в содержимом и удаляем
    // вместе с окружающим переводом строки
    foreach ($rulesToRemove as $rule) {
        $pos = stripos($content, $rule);
        if ($pos !== false) {
            // Находим начало строки с правилом
            $lineStart = strrpos(substr($content, 0, $pos), "\n");
            if ($lineStart === false) {
                $lineStart = 0;
            } else {
                $lineStart++; // начало строки после \n
            }
            // Находим конец строки с правилом
            $lineEnd = strpos($content, "\n", $pos);
            if ($lineEnd === false) {
                $lineEnd = strlen($content);
            }
            // Удаляем строку целиком (включая \n в конце, если он не в начале файла)
            if ($lineStart > 0) {
                // Удаляем вместе с предыдущим \n, чтобы не оставлять пустую строку
                $lineStart--;
                $length = $lineEnd - $lineStart;
            } else {
                $length = $lineEnd - $lineStart;
                if ($lineEnd < strlen($content)) {
                    $length++; // удаляем также последующий \n, если это не конец файла
                }
            }
            $content = substr_replace($content, '', $lineStart, $length);
        }
    }

    // Дополнительно можно удалить пустые строки, оставшиеся после удаления,
    // но это не обязательно. Если нужно – можно добавить trim() и замену \n\n на \n.

    file_put_contents($htaccessFile, $content);
}