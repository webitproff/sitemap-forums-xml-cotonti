
# Sitemap Forums — XML Sitemap for the Forums Module

**A plugin for Cotonti**  
Generates an XML sitemap **only for the forum** (sections, topics, and optionally individual posts).  
Helps search engines find and index all forum pages faster.


<img width="1536" height="1024" alt="sitemap-forums-xml-cotonti" src="https://github.com/user-attachments/assets/351d60cf-dab0-4376-b743-63eb30e9958c" />



---

## Table of Contents

- [History and Why This Is Needed](#history)
- [Features](#features)
- [Requirements](#requirements)
- [Plugin Structure](#plugin-structure)
- [Installation](#installation)
- [Plugin Settings in the Admin Panel](#plugin-settings)
- [Setting Up Clean URLs (Human-Friendly URLs)](#clean-urls)
- [Usage](#usage)
- [Caching and Cache Clearing](#caching)
- [Detailed Description of Each File](#detailed-description)
- [Usage Example](#usage-example)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Links](#links)

---

## History and Why This Is Needed

This plugin is part of a project to split the standard **SiteMap** plugin (included in Cotonti) into several independent plugins.

The original SiteMap generated one large sitemap for the entire site at once: pages, forums, users, products.  
Any changes or errors in one section meant having to “fix” the entire plugin, often breaking the whole map.

To avoid this problem, it was decided to split the single plugin into specialized ones, each responsible only for its own content type:

- **sitemap_forums** – XML sitemap for forums (this plugin)
- **sitemap_pages** – XML sitemap for pages (with multilingual support)
- **sitemap_users** – XML sitemap for user profiles
- **sitemap_market** – XML sitemap for products (Market module)

Each plugin is completely autonomous, configured separately, and does not affect the others.  
This approach simplifies maintenance and allows flexible management of site maps.

`sitemap_forums` is based on the code of the original SiteMap but has been completely reworked specifically for the `forums` module and adapted for PHP 8.x.

---

## Features

- Generates an XML sitemap for:
  - Forum sections (categories) with paginated topic lists,
  - Topics (threads) with paginated posts within each topic,
  - **Individual posts** (messages) — optional, enabled in settings.
- Automatically splits the sitemap into multiple files (sitemap index) if the total number of URLs exceeds the specified limit (`perpage`).
- During installation, automatically adds sitemap links to `robots.txt`.
- During installation, automatically adds rewrite rules to `.htaccess` for clean URLs (if the URLEditor plugin is active).
- Caches ready-made maps in files for quick delivery.
- Flexible settings: update frequency, priority for forum pages.
- Full compatibility with PHP 8.x.

---

## Requirements

- **Cotonti Siena** (latest version recommended)
- **Module `forums`** – must be installed and active
- **Plugin `urleditor`** – if you plan to use clean URLs (`sitemap-forums.xml`)
- Write permission for the `datas/cache/` folder (for caching sitemaps)
- PHP 8.0 or higher

---

## Plugin Structure

```
sitemap_forums/
├── sitemap_forums.setup.php           # Header, metadata, and plugin settings
├── sitemap_forums.ajax.php            # Main request handler (AJAX)
├── inc/
│   └── sitemap_forums.functions.php   # Functions: XML compression, date handling, map saving
├── lang/
│   ├── sitemap_forums.ru.lang.php     # Russian labels for settings
│   └── sitemap_forums.en.lang.php     # English labels for settings
├── tpl/
│   ├── sitemap_forums.tpl             # Template for a regular urlset
│   └── sitemap_forums.index.tpl       # Template for the index file (sitemapindex)
└── setup/
    └── sitemap_forums.install.php     # Automatically adds links to robots.txt
                                        # and rules to .htaccess during installation
```

---

## Installation

1. Download the ZIP archive of the plugin from the [GitHub repository](https://github.com/webitproff/sitemap-forums-xml-cotonti).
2. Extract the archive into the `plugins/` folder of your site.  
   You should get the structure described above.
3. Go to the Cotonti admin panel → **Extensions**.
4. Find the plugin **«Forums Sitemap»** in the list and click **«Install»**.

> **Important:**  
> During installation, the plugin automatically:
> - Adds links to the forum sitemap in `robots.txt`.
> - Adds rewrite rules to `.htaccess` for clean URLs (only if URLEditor is active and the rules are not already present).

After installation, the plugin is ready to work.

---

## Plugin Settings in the Admin Panel

All settings are located under **Extensions → Forums Sitemap → Configuration**.

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `cache_ttl` | string | `3600` | Cache lifetime in seconds. After this time, the sitemap is regenerated. |
| `perpage` | string | `50000` | Maximum number of URLs in a single sitemap file. If exceeded, the map is split into parts (sitemap index). |
| `freq` | select | `default` | Default change frequency (if not specified for a specific module). |
| `prio` | select | `0.5` | Default priority. |
| `forums` | radio | `1` (on) | Include forum sections in the sitemap. |
| `forums_freq` | select | `daily` | Change frequency for forum pages (sections, topics, posts). |
| `forums_prio` | select | `0.5` | Priority of forum pages. |
| `use_pretty_urls` | radio | `1` (on) | Use clean URLs for the sitemap (e.g., `sitemap-forums.xml`). If disabled, links will look like `index.php?r=sitemap_forums`. |
| `include_posts` | radio | `0` (off) | Include individual posts (messages) in the sitemap. **Caution:** on forums with many messages, this can significantly increase the sitemap size and generation time. |

It is recommended to keep the default values unless you have specific requirements.

---

## Setting Up Clean URLs (Human-Friendly URLs)

To make your sitemap available at:  
- `https://yoursite/sitemap-forums.xml` – main sitemap  
- `https://yoursite/sitemap-forums-index.xml` – index (when there are many URLs)

the **URLEditor** plugin must be active with any preset other than `none`.

During installation, the plugin automatically adds the following rules to `.htaccess` (if they are not already there):

```apache
RewriteRule ^sitemap-forums\.xml$ index.php?r=sitemap_forums [L]
RewriteRule ^sitemap-forums-index\.xml$ index.php?r=sitemap_forums&a=index [L]
```

**You do not need to add these rules manually** — the installation does it automatically.  
If you have disabled automatic `.htaccess` modifications, simply copy the lines above into your `.htaccess` after the `RewriteEngine On` line.

---

## Usage

### Viewing the Sitemap

- **Main sitemap:** open `https://yoursite/sitemap-forums.xml` in your browser  
  or `https://yoursite/index.php?r=sitemap_forums`
- **Index (if the sitemap is split):** `https://yoursite/sitemap-forums-index.xml`  
  or `https://yoursite/index.php?r=sitemap_forums&a=index`

### Adding to robots.txt

During installation, the plugin automatically adds the following lines to your root `robots.txt` (depending on the clean URL settings):

```
Sitemap: https://yoursite/index.php?r=sitemap_forums
Sitemap: https://yoursite/index.php?r=sitemap_forums&a=index
Sitemap: https://yoursite/sitemap-forums.xml
Sitemap: https://yoursite/sitemap-forums-index.xml
```

If you need to modify or remove these links — edit `robots.txt` manually.

### Validity Check

You can use the [XML Sitemap Validator](https://www.xml-sitemaps.com/validate-xml-sitemap.html) or similar services to ensure the sitemap is generated correctly.

---

## Caching and Cache Clearing

Ready-made sitemaps are stored in the `datas/cache/sitemap_forums/` folder and are updated only after the `cache_ttl` time has expired (default 1 hour). This greatly reduces server load under frequent search engine requests.

If you need to force an early update of the sitemaps, simply delete the files in the cache folder (`datas/cache/sitemap_forums/`). They will be regenerated on the next request.

---

## Detailed Description of Each File

Below is a **very detailed** description of each file included in the plugin.  
The description goes from general purpose to specific details so that a beginner can understand.

---

### 1. `sitemap_forums.setup.php`

**Purpose:**  
This is the “passport” of the plugin. It contains information that Cotonti uses to register the plugin in the system and to build the settings page in the admin panel. The file itself does not perform any actions other than declaring metadata.

**What it contains:**

- **`[BEGIN_COT_EXT]` / `[END_COT_EXT]` section** – plugin metadata: its code (`sitemap_forums`), name, category (seo), description, version, author, required modules (`forums`), guest and user access rights. Cotonti reads this section during installation and displays the plugin information.
- **`[BEGIN_COT_EXT_CONFIG]` / `[END_COT_EXT_CONFIG]` section** – a list of settings that will be available to the administrator via the admin panel. Each line has the format:
  ```
  parameter_name=order:type:default_value:hint
  ```
  Types: `string` (text field), `select` (dropdown list), `radio` (toggle), `separator` (group separator).  
  Examples:
  - `cache_ttl` — text field with a value of `3600` (seconds).
  - `freq` — dropdown list with preset values `default,always,hourly,…` and a default value of `default`.
  - `include_posts` — radio button (`1` or `0`) default `0` (off).

**Important:** manual editing of this file is not recommended — all settings can be changed via the admin panel and they will be saved in the database.

---

### 2. `sitemap_forums.ajax.php`

**Purpose:**  
The heart of the plugin. This file is called directly via the Cotonti AJAX hook when someone opens a link like `index.php?r=sitemap_forums` (or its “clean” counterpart `sitemap-forums.xml`). It generates and delivers the ready XML sitemap of the forum.

**How it works (step by step):**

1. **Output protection:**  
   - Clears all previous buffers, turns off error display, starts a new buffer.  
   - Sets the header `Content-Type: application/xml; charset=utf-8` – so that search engines understand that this is XML.

2. **Getting parameters:**  
   - `$d` – part number of the sitemap (if the sitemap is split into multiple files due to a large number of links).  
   - `$a` – action: if `'index'`, the index file should be returned; otherwise, the main sitemap.

3. **Reading plugin settings:**  
   - Loads configuration from `Cot::$cfg['plugin']['sitemap_forums']` with all parameters (perpage, cache_ttl, etc.).

4. **Cache paths:**  
   - The folder `datas/cache/sitemap_forums/` is created if it does not exist.

5. **Index file generation (if `?a=index` is requested):**  
   - Checks the cache (file `sitemap_forums_index.xml` and its modification time).  
   - If the cache is outdated, it recalculates the total number of URLs (from the `sitemap.count` file), determines the required number of parts (pages), and for each part generates a link in a `<sitemap>` block. Links can be clean (`sitemap-forums.xml?d=2`) or direct (`index.php?r=sitemap_forums&d=2`) depending on the `use_pretty_urls` setting.  
   - The finished index is saved to cache and delivered to the client.

6. **Main sitemap generation (if a regular sitemap is requested):**  
   - Checks the cache relevance using the `sitemap.count` file.  
   - If the cache is outdated, a full rebuild is started:
     - Connects the `forums` module.
     - Gathers section statistics (from the `cot_forum_stats` table).
     - For each accessible section, pages of the topic list are added (with pagination).
     - For each topic, pages with posts are added (with pagination inside the topic).
     - If the `include_posts` option is enabled, all posts are additionally retrieved from the `cot_forum_posts` table, and a separate URL is created for each.
     - In the process, each entry is passed to the `sitemap_forums_parse()` function, which adds it to the template and, if necessary, saves the current part of the sitemap to a file.
   - After processing all data, the last part of the sitemap is saved, and the total number of links is written to `sitemap.count`.

7. **Delivering the ready file:**  
   - Determines which cache file to serve (main `sitemap.xml` or part `sitemap.1.xml`, `sitemap.2.xml`…).  
   - Reads the file, cleans it from possible BOM and extra XML declarations, then outputs it with the correct header and explicit `<?xml version="1.0" encoding="UTF-8"?>`.

**Key points for understanding:**  
- This script is not intended for direct browser access without Cotonti (it uses the constant `COT_CODE`).  
- All the caching magic is hidden in the functions `sitemap_forums_parse()` and `sitemap_forums_save()` (see functions.php).  
- If you want to add support for other forum entities, you need to modify this file (and possibly functions.php).

---

### 3. `inc/sitemap_forums.functions.php`

**Purpose:**  
A library of auxiliary functions used in `sitemap_forums.ajax.php`. All functions are static and independent of context, which simplifies testing and maintenance.

**Description of each function:**

- **`sitemap_forums_compress(string $xml): string`**  
  Removes all tab, carriage return, and line feed characters from the string. Applied to the ready XML before saving to file to reduce size and remove extra whitespace.

- **`sitemap_forums_date(int|string $timestamp): string`**  
  Takes a Unix timestamp (or a string that will be converted to an integer). Returns a W3C (ISO 8601) formatted date for the `<lastmod>` tag, e.g.: `2026-08-04T12:00:00+00:00`. If the timestamp is 0 or empty, returns an empty string (the tag will not be displayed).

- **`sitemap_forums_freq(string $value): string`**  
  Returns the value for the `<changefreq>` tag. If `'default'` is passed, returns an empty string (the tag will not appear). Otherwise, returns the value itself (`always`, `hourly`, `daily`, etc.).

- **`sitemap_forums_prio(string $value): string`**  
  Similar for the `<priority>` tag. If the value is `'0.5'` (standard), returns an empty string. Otherwise, returns the number (e.g., `0.8`). This helps to avoid cluttering the XML with standard values.

- **`sitemap_forums_parse(XTemplate $t, int &$items, array $item): void`**  
  The main function for adding one entry to the sitemap.  
  - Takes an `XTemplate` template object, a counter of added links (passed by reference), and an `$item` array with keys `url`, `date`, `freq`, `prio`.  
  - If the current number of links is a multiple of the `perpage` limit, the current template is saved to a cache file, reset, and a new one begins. This implements the splitting into parts.  
  - Then fills the template variables: `SITEMAP_ROW_URL`, `SITEMAP_ROW_DATE`, `SITEMAP_ROW_FREQ`, `SITEMAP_ROW_PRIO` (the last two via the helper functions above).  
  - Calls `$t->parse('MAIN.SITEMAP_ROW')` to add the block to the main template.  
  - Increments the counter `$items`.

- **`sitemap_forums_save(string $xml, int $d = 0): void`**  
  Saves the passed XML to a cache file. If `$d = 0`, creates the file `sitemap.xml`; if `$d > 0`, then `sitemap.1.xml`, etc. Before writing, XML is compressed via `sitemap_forums_compress()`. The folder `datas/cache/sitemap_forums/` is created automatically if it does not exist.

**Why the functions are separated:**  
- Code cleanliness — `ajax.php` does not become a mess.  
- Reusability (even in other plugins).  
- Easier debugging: you can test the compression logic or date formatting separately.

---

### 4. Language Files (`lang/sitemap_forums.ru.lang.php` and `en.lang.php`)

**Purpose:**  
Contain translated strings that are displayed in the Cotonti admin panel when editing the plugin settings. Without them, the administrator would only see technical keys like `cfg_cache_ttl`, which is not user-friendly.

**What’s inside:**  
- An associative array `$L` whose keys correspond to the parameter names from `setup.php`. For example, `$L['cfg_cache_ttl'] = 'Cache lifetime (seconds)';`.  
- Also includes headings (`$L['sitemap_forums_title']`) and arrays for dropdown list values (`$sitemap_forums_freqs`).

**Important:**  
If you add a new parameter in `setup.php`, be sure to add the corresponding strings to both language files; otherwise, a technical key will appear in the admin panel.

---

### 5. Templates (`tpl/sitemap_forums.tpl` and `sitemap_forums.index.tpl`)

**Purpose:**  
Define the appearance of the generated XML documents. Cotonti uses the XTemplate template engine, so the files contain special markers and conditional blocks.

#### `sitemap_forums.tpl` (regular sitemap)
Contains blocks:
- `<!-- BEGIN: MAIN -->` ... `<!-- END: MAIN -->` — the root element `<urlset>` with the correct namespace.
- `<!-- BEGIN: SITEMAP_ROW -->` ... `<!-- END: SITEMAP_ROW -->` — template for a single URL. Inside:
  - `<loc>{SITEMAP_ROW_URL}</loc>` — absolute link.
  - Conditional blocks `<!-- IF {SITEMAP_ROW_DATE} -->` – if the date is not empty, the `<lastmod>` tag is output.
  - Similarly for `SITEMAP_ROW_FREQ` and `SITEMAP_ROW_PRIO`.

Thus, if a parameter is not set (empty string), the corresponding tag does not appear in the final XML.

#### `sitemap_forums.index.tpl` (index file)
Similar to the main one, but the root element is `<sitemapindex>`, and inside the `SITEMAP_ROW` block — `<sitemap>` with `<loc>` and optionally `<lastmod>`. This template is used only for `?a=index`.

**Important:**  
Never add the string `<?xml version="1.0" encoding="UTF-8"?>` to these templates — it is inserted programmatically in `ajax.php` to avoid duplication and BOM issues.

---

### 6. `setup/sitemap_forums.install.php`

**Purpose:**  
A script that runs once during the installation (or reinstallation) of the plugin. Its task is to automatically configure the external environment so that the sitemap is immediately available to search engines.

**What it does (in detail):**

1. **Working with `robots.txt`:**
   - Checks whether the `robots.txt` file exists in the site root and is writable.
   - Reads its contents line by line.
   - Determines whether clean URLs are enabled on the site (checks the activity of the `urleditor` plugin and its preset).
   - Builds a list of links to add:
     - Direct links `index.php?r=sitemap_forums` and `index.php?r=sitemap_forums&a=index` are always added.
     - If clean URLs are working, `sitemap-forums.xml` and `sitemap-forums-index.xml` are added.
   - Removes **all** lines containing `sitemap-forums` or `sitemap_forums` (to avoid old duplicates from previous installations).
   - Appends new lines in the format `Sitemap: https://yoursite/...` and writes the file back.

2. **Working with `.htaccess`:**
   - Checks the existence and writability of the root `.htaccess`.
   - Reads its contents.
   - Checks whether rules for `sitemap-forums.xml` and `sitemap-forums-index.xml` already exist. If both rules are present, does nothing.
   - If the rules are missing, searches for a suitable insertion point:
     - First looks for the line `RewriteBase "/"` — the rules are inserted right after it.
     - If not found, looks for `# Sitemap shortcut`.
     - If that is also missing, inserts after `RewriteEngine On`.
   - Inserts a block of two RewriteRule lines that transform clean URLs into Cotonti internal routes.
   - Writes the updated `.htaccess`.

**Why this is important:**  
Without these automatic actions, the user would have to manually edit `robots.txt` and `.htaccess`, which can be difficult for beginners and error-prone. The plugin does it itself, ensuring correct operation “out of the box”.

---

## Usage Example

1. Install the plugin (see [Installation](#installation)).
2. Check that `Sitemap:` lines have appeared in `robots.txt`.
3. Open `https://yoursite/sitemap-forums.xml` — you should see an XML with a list of forum sections and topics.
4. If you want to include individual posts in the sitemap, go to the plugin settings and set `include_posts = 1`. After that, the sitemap will contain direct links to each forum post (may significantly increase in size).
5. To check the sitemap with an online validator (e.g., xml-sitemaps.com), make sure that the `visitor_stats` plugin’s bot whitelist (if installed) contains the entry `'Sitemaps Generator'`. If necessary, add it to the file `plugins/visitor_stats/lib/Fixtures/WhitelistBots.php`.

---

## Troubleshooting

- **Sitemap does not open (error 404 or 500).**  
  Check that the `forums` module is active. Make sure the folder `datas/cache/sitemap_forums/` exists and is writable.

- **Clean URLs (`sitemap-forums.xml`) do not work.**  
  Ensure that the `urleditor` plugin is active and its preset is not `none`. Check whether the rules have been added to `.htaccess` (see above). You can insert them manually.

- **Posts are missing in the sitemap.**  
  Check that the `include_posts` option is enabled in the plugin settings. If enabled but posts still don’t appear, clear the cache (`datas/cache/sitemap_forums/`).

- **The validator shows the error `Incorrect http header content-type: text/html`.**  
  This means the server is returning HTML instead of XML. A common cause is the `visitor_stats` plugin blocking the validator’s request because its User‑Agent (`Sitemaps Generator`) is not in the bot whitelist.  
  **Solution:** open the file `plugins/visitor_stats/lib/Fixtures/WhitelistBots.php`, find the `getAllowed()` method, and add the string `'Sitemaps Generator'` (if it’s not already there). After that, the sitemap should pass the check.

- **Index file is empty or contains incorrect links.**  
  Clear the cache and check the settings `perpage` and `use_pretty_urls`. The index will be regenerated on the next request.

---

## License

Distributed under the **BSD** license.  
Free to use and modify, provided the copyright notice is retained.

---

## Links

- [Plugin repository](https://github.com/webitproff/sitemap-forums-xml-cotonti)
- [Support forum](https://abuyfile.com/forums/cotonti/custom/plugs/)
- [Original SiteMap source code (part of Cotonti)](https://github.com/Cotonti/Cotonti)
- [URLEditor plugin (required for clean URLs)](https://github.com/Cotonti/Cotonti/tree/main/plugins/urleditor)

---

If you have any questions, you know what to do )

___
> RU 
___

# Sitemap Forums — XML-карта сайта для модуля Forums

**Плагин для Cotonti Siena**  
Генерирует XML-карту сайта (sitemap) **только для форума** (разделы, темы и, опционально, отдельные посты).  
Помогает поисковым системам быстрее находить и индексировать все страницы форума.

---

## Оглавление

- [История создания и зачем это нужно](#история-создания)
- [Возможности](#возможности)
- [Требования](#требования)
- [Структура плагина](#структура-плагина)
- [Установка](#установка)
- [Настройка плагина в админке](#настройка-плагина-в-админке)
- [Настройка красивых URL (ЧПУ)](#настройка-красивых-url-чпу)
- [Использование](#использование)
- [Кеширование и его очистка](#кеширование-и-его-очистка)
- [Подробное описание каждого файла](#подробное-описание-каждого-файла)
- [Пример использования](#пример-использования)
- [Устранение неполадок](#устранение-неполадок)
- [Лицензия](#лицензия)
- [Ссылки](#ссылки)

---

## История создания и зачем это нужно

Этот плагин — часть проекта по разделению стандартного плагина **SiteMap** (входящего в комплект Cotonti) на несколько независимых плагинов.

Оригинальный SiteMap генерировал одну большую карту для всего сайта сразу: страницы, форум, пользователи, товары.  
При любых изменениях или ошибках в одном из разделов приходилось «чинить» весь плагин целиком, что часто приводило к поломкам всей карты.

Чтобы избежать этой проблемы, было решено разбить единый плагин на специализированные, каждый из которых отвечает только за свой тип контента:

- **sitemap_forums** – XML-карта форумов (этот плагин)
- **sitemap_pages** – XML-карта страниц (с мультиязычной поддержкой)
- **sitemap_users** – XML-карта профилей пользователей
- **sitemap_market** – XML-карта товаров (модуль Market)

Каждый плагин полностью автономен, настраивается отдельно и не влияет на работу других.  
Такой подход упрощает поддержку и позволяет гибко управлять картами сайта.

`sitemap_forums` создан на основе кода оригинального SiteMap, но полностью переработан специально для модуля `forums` и адаптирован под PHP 8.x.

---

## Возможности

- Генерирует XML-карту для:
  - разделов форума (категорий) с пагинацией списков тем,
  - тем (топиков) с пагинацией постов внутри каждой темы,
  - **отдельных постов** (сообщений) — опционально, включается в настройках.
- Автоматически разбивает карту на несколько файлов (sitemap index), если общее число ссылок превышает заданный лимит (`perpage`).
- При установке автоматически добавляет ссылки на карту в `robots.txt`.
- При установке автоматически добавляет правила rewrite в `.htaccess` для красивых URL (если активен плагин URLEditor).
- Кеширует готовые карты в файлы для быстрой отдачи.
- Гибкие настройки: частота обновления, приоритет для страниц форума.
- Полная совместимость с PHP 8.x.

---

## Требования

- **Cotonti Siena** (рекомендуется актуальная версия)
- **Модуль `forums`** — должен быть установлен и активен
- **Плагин `urleditor`** — если планируете использовать красивые URL (`sitemap-forums.xml`)
- Права на запись в папку `datas/cache/` (для кеширования карт)
- PHP 8.0 или выше

---

## Структура плагина

```
sitemap_forums/
├── sitemap_forums.setup.php           # Заголовок, метаданные и настройки плагина
├── sitemap_forums.ajax.php            # Главный обработчик запросов (AJAX)
├── inc/
│   └── sitemap_forums.functions.php   # Функции: сжатие XML, работа с датами, сохранение карт
├── lang/
│   ├── sitemap_forums.ru.lang.php     # Русские подписи к настройкам
│   └── sitemap_forums.en.lang.php     # Английские подписи к настройкам
├── tpl/
│   ├── sitemap_forums.tpl             # Шаблон для обычного urlset
│   └── sitemap_forums.index.tpl       # Шаблон для индексного файла (sitemapindex)
└── setup/
    └── sitemap_forums.install.php     # Автоматическое добавление ссылок в robots.txt
                                        # и правил в .htaccess при установке
```

---

## Установка

1. Скачайте ZIP-архив плагина из [репозитория GitHub](https://github.com/webitproff/sitemap-forums-xml-cotonti).
2. Распакуйте архив в папку `plugins/` вашего сайта.  
   Должна получиться структура, описанная выше.
3. Зайдите в админ-панель Cotonti → **Расширения**.
4. Найдите в списке плагин **«Forums Sitemap»** и нажмите **«Установить»**.

> **Важно:**  
> При установке плагин автоматически:
> - Добавит ссылки на карту форума в `robots.txt`.
> - Добавит правила rewrite в `.htaccess` для красивых URL (только если активен URLEditor и если этих правил ещё нет).

После установки плагин сразу начинает работать.

---

## Настройка плагина в админке

Все параметры находятся в **Расширения → Forums Sitemap → Конфигурация**.

| Параметр | Тип | По умолчанию | Описание |
|----------|-----|-------------|----------|
| `cache_ttl` | строка | `3600` | Время жизни кеша в секундах. По истечении карта перегенерируется. |
| `perpage` | строка | `50000` | Максимальное число URL в одном файле карты. При превышении создаются части (sitemap index). |
| `freq` | select | `default` | Частота изменения по умолчанию (если не задана для конкретного модуля). |
| `prio` | select | `0.5` | Приоритет по умолчанию. |
| `forums` | radio | `1` (вкл.) | Включить разделы форума в карту сайта. |
| `forums_freq` | select | `daily` | Частота изменения страниц форума (разделов, тем, постов). |
| `forums_prio` | select | `0.5` | Приоритет страниц форума. |
| `use_pretty_urls` | radio | `1` (вкл.) | Использовать красивые URL для карты (например, `sitemap-forums.xml`). Если отключено, ссылки будут вида `index.php?r=sitemap_forums`. |
| `include_posts` | radio | `0` (выкл.) | Включать отдельные посты (сообщения) в карту сайта. **Осторожно:** на форумах с большим количеством сообщений это может значительно увеличить размер карты и время генерации. |

Рекомендуется оставить значения по умолчанию, если нет специфических требований.

---

## Настройка красивых URL (ЧПУ)

Чтобы ваша карта была доступна по адресам:  
- `https://вашсайт/sitemap-forums.xml` – основная карта  
- `https://вашсайт/sitemap-forums-index.xml` – индекс (при большом количестве URL)

необходимо, чтобы на сайте был активен плагин **URLEditor** с любым пресетом, отличным от `none`.

При установке плагин сам добавляет следующие правила в `.htaccess` (если их ещё нет):

```apache
RewriteRule ^sitemap-forums\.xml$ index.php?r=sitemap_forums [L]
RewriteRule ^sitemap-forums-index\.xml$ index.php?r=sitemap_forums&a=index [L]
```

**Вручную эти правила прописывать не нужно** — установка сделает всё автоматически.  
Если вы отключали автоматическое изменение `.htaccess`, просто скопируйте строки выше в свой `.htaccess` после строки `RewriteEngine On`.

---

## Использование

### Просмотр карты

- **Основная карта:** откройте в браузере `https://вашсайт/sitemap-forums.xml`  
  или `https://вашсайт/index.php?r=sitemap_forums`
- **Индекс (если карта разбита на части):** `https://вашсайт/sitemap-forums-index.xml`  
  или `https://вашсайт/index.php?r=sitemap_forums&a=index`

### Добавление в robots.txt

При установке плагин автоматически добавляет в корневой `robots.txt` следующие строки (в зависимости от настроек ЧПУ):

```
Sitemap: https://вашсайт/index.php?r=sitemap_forums
Sitemap: https://вашсайт/index.php?r=sitemap_forums&a=index
Sitemap: https://вашсайт/sitemap-forums.xml
Sitemap: https://вашсайт/sitemap-forums-index.xml
```

Если вам нужно изменить или удалить эти ссылки — отредактируйте `robots.txt` вручную.

### Проверка валидности

Вы можете использовать [XML Sitemap Validator](https://www.xml-sitemaps.com/validate-xml-sitemap.html) или аналогичные сервисы, чтобы убедиться, что карта сформирована правильно.

---

## Кеширование и его очистка

Готовые карты сохраняются в папке `datas/cache/sitemap_forums/` и обновляются только по истечении времени `cache_ttl` (по умолчанию 1 час). Это значительно снижает нагрузку на сервер при частых запросах от поисковых систем.

Если вам нужно принудительно обновить карты раньше срока — просто удалите файлы в папке кэша (`datas/cache/sitemap_forums/`). При следующем обращении они создадутся заново.

---

## Подробное описание каждого файла

Ниже приведено **очень подробное** описание каждого файла, входящего в плагин.  
Описание ведётся от общего назначения к конкретным деталям, чтобы новичок смог разобраться.

---

## 1. `sitemap_forums.setup.php`

**Назначение:**  
Это «паспорт» плагина. Здесь хранится информация, которую Cotonti использует для регистрации плагина в системе и для построения страницы настроек в админ-панели. Сам файл не выполняет никаких действий, кроме объявления метаданных.

**Что содержит:**

- **Секция `[BEGIN_COT_EXT]` и `[END_COT_EXT]`** – метаданные плагина: его код (`sitemap_forums`), название, категорию (seo), описание, версию, автора, требуемые модули (`forums`), права доступа гостей и пользователей. Cotonti читает эту секцию при установке и отображает информацию о плагине.
- **Секция `[BEGIN_COT_EXT_CONFIG]` и `[END_COT_EXT_CONFIG]`** – список настроек, которые будут доступны администратору через админ-панель. Каждая строка имеет формат:
  ```
  имя_параметра=порядок:тип:значение_по_умолчанию:подсказка
  ```
  Типы: `string` (текстовое поле), `select` (выпадающий список), `radio` (переключатель), `separator` (разделитель групп).  
  Примеры:
  - `cache_ttl` — текстовое поле со значением `3600` (секунд).
  - `freq` — выпадающий список с предустановленными значениями `default,always,hourly,…` и значением по умолчанию `default`.
  - `include_posts` — радиокнопка (`1` или `0`) по умолчанию `0` (выключено).

**Важно:** изменение этого файла вручную не рекомендуется — все настройки можно менять через админку, и они сохранятся в базе данных.

---

## 2. `sitemap_forums.ajax.php`

**Назначение:**  
Сердце плагина. Этот файл вызывается напрямую через AJAX-хук Cotonti, когда кто-то открывает ссылку вида `index.php?r=sitemap_forums` (или её «красивый» аналог `sitemap-forums.xml`). Именно он формирует и отдаёт готовую XML-карту форума.

**Как он работает (по шагам):**

1. **Защита вывода:**  
   - Очищает все предыдущие буферы, отключает отображение ошибок, запускает новый буфер.  
   - Устанавливает заголовок `Content-Type: application/xml; charset=utf-8` – чтобы поисковик понимал, что это XML.

2. **Получение параметров:**  
   - `$d` – номер части карты (если карта разбита на несколько файлов из-за большого количества ссылок).  
   - `$a` – действие: если `'index'`, то нужно отдать индексный файл, иначе основную карту.

3. **Чтение настроек плагина:**  
   - Загружает конфигурацию из `Cot::$cfg['plugin']['sitemap_forums']` со всеми параметрами (perpage, cache_ttl и т.д.).

4. **Определение путей к кешу:**  
   - Папка `datas/cache/sitemap_forums/` создаётся, если её нет.

5. **Генерация индексного файла (если запрошен `?a=index`):**  
   - Проверяется кеш (файл `sitemap_forums_index.xml` и время его изменения).  
   - Если кеш устарел, заново пересчитывается количество всех URL (из файла `sitemap.count`), определяется необходимое число частей (страниц), и для каждой части формируется ссылка в `<sitemap>` блоке. Ссылки могут быть красивыми (`sitemap-forums.xml?d=2`) или прямыми (`index.php?r=sitemap_forums&d=2`) в зависимости от настройки `use_pretty_urls`.  
   - Готовый индекс сохраняется в кеш и отдаётся клиенту.

6. **Генерация основной карты (если запрошена обычная карта):**  
   - Проверяется актуальность кеша по файлу `sitemap.count`.  
   - Если кеш устарел, запускается полная перестройка:
     - Подключается модуль `forums`.
     - Собирается статистика разделов (из таблицы `cot_forum_stats`).
     - Для каждого доступного раздела добавляются страницы списка тем (с пагинацией).
     - Для каждой темы добавляются страницы с постами (с пагинацией внутри темы).
     - Если включена опция `include_posts`, дополнительно из таблицы `cot_forum_posts` выбираются все посты и для каждого создаётся отдельный URL.
     - В процессе каждая запись передаётся в функцию `sitemap_forums_parse()`, которая добавляет её в шаблон и при необходимости сохраняет текущую часть карты в файл.
   - После обработки всех данных последняя часть карты сохраняется, а общее количество ссылок записывается в `sitemap.count`.

7. **Отдача готового файла:**  
   - Определяется, какой файл кеша нужно отдать (основной `sitemap.xml` или часть `sitemap.1.xml`, `sitemap.2.xml`…).  
   - Файл читается, очищается от возможного BOM и лишних XML-деклараций, после чего выводится с правильным заголовком и явным `<?xml version="1.0" encoding="UTF-8"?>`.

**Ключевые моменты для понимания:**  
- Этот скрипт не предназначен для прямого вызова через браузер без Cotonti (он использует константу `COT_CODE`).  
- Вся магия кеширования спрятана в функциях `sitemap_forums_parse()` и `sitemap_forums_save()` (см. functions.php).  
- Если вы хотите добавить поддержку других сущностей форума, править нужно именно этот файл (и, возможно, functions.php).

---

## 3. `inc/sitemap_forums.functions.php`

**Назначение:**  
Библиотека вспомогательных функций, которые используются в `sitemap_forums.ajax.php`. Все функции статические и не зависят от контекста, что упрощает тестирование и поддержку.

**Описание каждой функции:**

- **`sitemap_forums_compress(string $xml): string`**  
  Удаляет из строки все символы табуляции, возврата каретки и переноса строки. Применяется к готовому XML перед сохранением в файл, чтобы уменьшить размер и убрать лишние пробелы.

- **`sitemap_forums_date(int|string $timestamp): string`**  
  Принимает Unix-метку времени (или строку, которая будет преобразована в целое). Возвращает дату в формате W3C (ISO 8601) для тега `<lastmod>`, например: `2026-08-04T12:00:00+00:00`. Если метка равна 0 или пуста, возвращает пустую строку (тег не будет выведен).

- **`sitemap_forums_freq(string $value): string`**  
  Возвращает значение для тега `<changefreq>`. Если передано `'default'`, возвращает пустую строку (тег не появится). В остальных случаях возвращает само значение (`always`, `hourly`, `daily` и т.д.).

- **`sitemap_forums_prio(string $value): string`**  
  Аналогично, для тега `<priority>`. Если значение равно `'0.5'` (стандартное), возвращает пустую строку. Иначе возвращает число (например `0.8`). Это позволяет не загромождать XML стандартными значениями.

- **`sitemap_forums_parse(XTemplate $t, int &$items, array $item): void`**  
  Основная функция добавления одной записи в карту.  
  - Принимает объект шаблона `XTemplate`, счётчик добавленных ссылок (по ссылке) и массив `$item` с ключами `url`, `date`, `freq`, `prio`.  
  - Если текущее количество ссылок кратно лимиту `perpage`, текущий шаблон сохраняется в файл кеша, сбрасывается и начинается новый. Таким образом реализовано разбиение на части.  
  - Затем заполняются переменные шаблона: `SITEMAP_ROW_URL`, `SITEMAP_ROW_DATE`, `SITEMAP_ROW_FREQ`, `SITEMAP_ROW_PRIO` (последние два — через вспомогательные функции выше).  
  - Вызывается `$t->parse('MAIN.SITEMAP_ROW')`, чтобы добавить блок в основной шаблон.  
  - Счётчик `$items` увеличивается.

- **`sitemap_forums_save(string $xml, int $d = 0): void`**  
  Сохраняет переданный XML в файл кеша. Если `$d = 0`, создаётся файл `sitemap.xml`; если `$d > 0`, то `sitemap.1.xml` и т.д. Перед записью XML сжимается через `sitemap_forums_compress()`. Папка `datas/cache/sitemap_forums/` создаётся автоматически, если её нет.

**Почему функции вынесены отдельно:**  
- Чистота кода — `ajax.php` не превращается в месиво.  
- Возможность переиспользования (хоть в других плагинах).  
- Упрощение отладки: можно протестировать логику сжатия или форматирования даты отдельно.

---

## 4. Языковые файлы (`lang/sitemap_forums.ru.lang.php` и `en.lang.php`)

**Назначение:**  
Хранят переведённые строки, которые отображаются в админ-панели Cotonti при редактировании настроек плагина. Без них администратор видел бы только технические ключи типа `cfg_cache_ttl`, что неудобно.

**Что внутри:**  
- Ассоциативный массив `$L`, ключи которого соответствуют названиям параметров из `setup.php`. Например, `$L['cfg_cache_ttl'] = 'Время жизни кеша (секунд)';`.  
- Также здесь лежат заголовки (`$L['sitemap_forums_title']`) и массивы для значений выпадающих списков (`$sitemap_forums_freqs`).

**Важно:**  
Если вы добавляете новый параметр в `setup.php`, обязательно добавьте соответствующие строки в оба языковых файла, иначе в админке будет отображаться технический ключ.

---

## 5. Шаблоны (`tpl/sitemap_forums.tpl` и `sitemap_forums.index.tpl`)

**Назначение:**  
Определяют внешний вид генерируемых XML-документов. Cotonti использует шаблонизатор XTemplate, поэтому файлы содержат специальные маркеры и условные блоки.

### `sitemap_forums.tpl` (обычная карта)
Содержит блоки:
- `<!-- BEGIN: MAIN -->` ... `<!-- END: MAIN -->` — корневой элемент `<urlset>` с правильным namespace.
- `<!-- BEGIN: SITEMAP_ROW -->` ... `<!-- END: SITEMAP_ROW -->` — шаблон для одного URL. Внутри:
  - `<loc>{SITEMAP_ROW_URL}</loc>` — абсолютная ссылка.
  - Условные блоки `<!-- IF {SITEMAP_ROW_DATE} -->` – если дата не пуста, выводится `<lastmod>`.
  - Аналогично для `SITEMAP_ROW_FREQ` и `SITEMAP_ROW_PRIO`.

Таким образом, если параметр не задан (пустая строка), соответствующий тег не появляется в финальном XML.

### `sitemap_forums.index.tpl` (индексный файл)
Похож на основной, но корневой элемент — `<sitemapindex>`, а внутри блока `SITEMAP_ROW` — `<sitemap>` с `<loc>` и опционально `<lastmod>`. Этот шаблон используется только при `?a=index`.

**Важно:**  
Никогда не добавляйте в эти шаблоны строку `<?xml version="1.0" encoding="UTF-8"?>` — она вставляется программно в `ajax.php`, чтобы избежать дублирования и проблем с BOM.

---

## 6. `setup/sitemap_forums.install.php`

**Назначение:**  
Скрипт, который выполняется один раз при установке (или переустановке) плагина. Его задача — автоматически настроить внешнее окружение, чтобы карта сайта была сразу доступна поисковикам.

**Что делает (подробно):**

1. **Работа с `robots.txt`:**
   - Проверяет, существует ли файл `robots.txt` в корне сайта и доступен ли он для записи.
   - Читает его содержимое построчно.
   - Определяет, включены ли ЧПУ на сайте (проверяется активность плагина `urleditor` и его пресет).
   - Формирует список ссылок, которые нужно добавить:
     - Прямые ссылки `index.php?r=sitemap_forums` и `index.php?r=sitemap_forums&a=index` добавляются всегда.
     - Если ЧПУ работают, добавляются `sitemap-forums.xml` и `sitemap-forums-index.xml`.
   - Удаляет **все** строки, в которых встречается `sitemap-forums` или `sitemap_forums` (чтобы не осталось старых дубликатов после прошлых установок).
   - Добавляет новые строки формата `Sitemap: https://вашсайт/...` и записывает файл обратно.

2. **Работа с `.htaccess`:**
   - Проверяет существование и доступность корневого `.htaccess`.
   - Читает его содержимое.
   - Проверяет, есть ли уже правила для `sitemap-forums.xml` и `sitemap-forums-index.xml`. Если оба правила присутствуют — ничего не делает.
   - Если правил нет, ищет подходящее место для вставки:
     - Сначала ищет строку `RewriteBase "/"` — правила вставляются сразу после неё.
     - Если такой строки нет, ищет `# Sitemap shortcut`.
     - Если и этого нет — вставляет после `RewriteEngine On`.
   - Вставляет блок из двух правил RewriteRule, которые преобразуют красивые URL во внутренние маршруты Cotonti.
   - Записывает обновлённый `.htaccess`.

**Почему это важно:**  
Без этих автоматических действий пользователю пришлось бы вручную править `robots.txt` и `.htaccess`, что для новичков может быть сложно и чревато ошибками. Плагин делает это сам, гарантируя корректную работу «из коробки».


---

## Пример использования

1. Установите плагин (см. [Установка](#установка)).
2. Проверьте, что в `robots.txt` появились строки `Sitemap:`.
3. Откройте `https://вашсайт/sitemap-forums.xml` — вы увидите XML со списком разделов и тем форума.
4. Если вы хотите включить в карту отдельные посты, зайдите в настройки плагина и установите `include_posts = 1`. После этого карта начнёт содержать прямые ссылки на каждый пост форума (может значительно увеличиться в размере).
5. Для проверки карты онлайн-валидатором (например, xml-sitemaps.com) убедитесь, что в белом списке ботов плагина `visitor_stats` (если он установлен) присутствует запись `'Sitemaps Generator'`. При необходимости добавьте её в файл `plugins/visitor_stats/lib/Fixtures/WhitelistBots.php`.

---

## Устранение неполадок

- **Карта не открывается (ошибка 404 или 500).**  
  Проверьте, активен ли модуль `forums`. Убедитесь, что папка `datas/cache/sitemap_forums/` существует и доступна для записи.

- **Красивые URL (`sitemap-forums.xml`) не работают.**  
  Убедитесь, что плагин `urleditor` активен и его пресет не `none`. Проверьте, добавились ли правила в `.htaccess` (см. выше). Можно вставить их вручную.

- **В карте отсутствуют посты.**  
  Проверьте, что опция `include_posts` включена в настройках плагина. Если она включена, но постов всё равно нет, очистите кеш (`datas/cache/sitemap_forums/`).

- **Валидатор показывает ошибку `Incorrect http header content-type: text/html`.**  
  Это означает, что сервер возвращает HTML вместо XML. Частая причина — плагин `visitor_stats` блокирует запрос от валидатора, потому что его User‑Agent (`Sitemaps Generator`) отсутствует в белом списке ботов.  
  **Решение:** откройте файл `plugins/visitor_stats/lib/Fixtures/WhitelistBots.php`, найдите массив `getAllowed()` и добавьте в него строку `'Sitemaps Generator'` (если её там нет). После этого карта должна проходить проверку.

- **Индексный файл пуст или содержит неправильные ссылки.**  
  Очистите кеш и проверьте настройки `perpage` и `use_pretty_urls`. Индекс пересоздастся при следующем запросе.

---

## Лицензия

Распространяется под лицензией **BSD**.  
Свободное использование и модификация при сохранении копирайта.

---

## Ссылки

- [Репозиторий плагина](https://github.com/webitproff/sitemap-forums-xml-cotonti)
- [Поддержка на форуме](https://abuyfile.com/forums/cotonti/custom/plugs/)
- [Исходный код оригинального SiteMap (в составе Cotonti)](https://github.com/Cotonti/Cotonti)
- [Плагин URLEditor (необходим для красивых URL)](https://github.com/Cotonti/Cotonti/tree/main/plugins/urleditor)

---

Если у вас возникли вопросы - вы знаете что делать )
