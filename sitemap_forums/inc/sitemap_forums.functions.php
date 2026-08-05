<?php
/**
 * Functions for Sitemap Forums plugin
 *
 * @package sitemap_forums
 * @copyright (c) Cotonti by webitproff 2026
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('sitemap_forums', 'plug');

/**
 * Compresses XML output removing all tabs and newlines from it.
 */
function sitemap_forums_compress(string $xml): string
{
    return str_replace(["\t", "\r", "\n"], '', $xml);
}

/**
 * Converts a timestamp into W3C sitemap date format.
 */
function sitemap_forums_date(int|string $timestamp): string
{
    $ts = (int) $timestamp;
    return $ts > 0 ? date('c', $ts) : '';
}

/**
 * Frequency tag helper: returns empty string for 'default'.
 */
function sitemap_forums_freq(string $value): string
{
    return $value === 'default' ? '' : $value;
}

/**
 * Priority tag helper: returns empty string for 0.5 (default).
 */
function sitemap_forums_prio(string $value): string
{
    return $value === '0.5' ? '' : $value;
}

/**
 * Parses a sitemap entry, handling pagination across files.
 */
function sitemap_forums_parse(XTemplate $t, int &$items, array $item): void
{
    $cfg = Cot::$cfg['plugin']['sitemap_forums'] ?? [];
    $perpage = (int) ($cfg['perpage'] ?? 50000);

    if ($items > 0 && $items % $perpage === 0) {
        // Save current page and start a new one
        $d = $items / $perpage - 1;
        $t->parse();
        sitemap_forums_save($t->text(), $d);
        $t->reset();
    }

    $url = $item['url'] ?? '';
    if (!str_contains($url, '://')) {
        $url = COT_ABSOLUTE_URL . $url;
    }

    $t->assign([
        'SITEMAP_ROW_URL'  => $url,
        'SITEMAP_ROW_DATE' => sitemap_forums_date($item['date'] ?? 0),
        'SITEMAP_ROW_FREQ' => sitemap_forums_freq($item['freq'] ?? 'default'),
        'SITEMAP_ROW_PRIO' => sitemap_forums_prio($item['prio'] ?? '0.5'),
    ]);
    $t->parse('MAIN.SITEMAP_ROW');
    $items++;
}

/**
 * Saves a sitemap cache file.
 */
function sitemap_forums_save(string $xml, int $d = 0): void
{
    $cacheDir = rtrim(Cot::$cfg['cache_dir'] ?? 'datas/cache', '/') . '/sitemap_forums';
    if (!file_exists($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }
    $filename = $d > 0
        ? "{$cacheDir}/sitemap.{$d}.xml"
        : "{$cacheDir}/sitemap.xml";
    file_put_contents($filename, sitemap_forums_compress($xml));
}