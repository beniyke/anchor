<?php

declare(strict_types=1);

namespace App\Docs\Services;

use Helpers\File\Adapters\Interfaces\FileMetaInterface;
use Helpers\File\Adapters\Interfaces\FileReadWriteInterface;
use Helpers\File\Adapters\Interfaces\PathResolverInterface;
use Helpers\File\Contracts\CacheInterface;
use Parsedown;

class DocsService
{
    private string $docsPath;

    private Parsedown $parser;

    private CacheInterface $cache;

    private FileMetaInterface $fileMeta;

    private FileReadWriteInterface $fileReadWrite;

    public function __construct(Parsedown $parser, PathResolverInterface $path, FileMetaInterface $fileMeta, FileReadWriteInterface $fileReadWrite, CacheInterface $cache)
    {
        $this->fileMeta = $fileMeta;
        $this->fileReadWrite = $fileReadWrite;
        $this->docsPath = $path->basePath('docs');
        $this->parser = $parser;
        $this->parser->setSafeMode(true);
        $this->cache = $cache;
    }

    public function getKeywordMap(): array
    {
        return [
            // Database related
            'db' => ['database', 'query', 'sql', 'orm', 'migration', 'seeding'],
            'database' => ['db', 'query', 'sql', 'orm', 'connection'],
            'sql' => ['database', 'query', 'orm'],
            'model' => ['database', 'model', 'eloquent', 'query'],
            'migration' => ['database', 'schema', 'table'],
            'seed' => ['database', 'seeding', 'data'],

            // Arrays & Collections
            'array' => ['collection', 'list', 'iterate', 'map', 'filter'],
            'collection' => ['array', 'list', 'set', 'map'],

            // Authentication & Security
            'auth' => ['authentication', 'login', 'user', 'session', 'security'],
            'login' => ['authentication', 'auth', 'user', 'session'],
            'security' => ['authentication', 'csrf', 'encryption', 'firewall'],
            'csrf' => ['security', 'token', 'protection'],

            // Routing & Controllers
            'controller' => ['routing', 'request', 'response'],
            'middleware' => ['routing', 'filter', 'guard'],

            // Views & Templates
            'view' => ['template', 'blade', 'render', 'display'],
            'template' => ['view', 'render', 'display'],

            // Mail & Notifications
            'email' => ['mail', 'notification', 'send'],
            'mail' => ['email', 'notification', 'mailer'],
            'notify' => ['notification', 'alert', 'message'],

            // Queue & Jobs
            'queue' => ['job', 'worker', 'async', 'background'],
            'job' => ['queue', 'worker', 'task'],
            'worker' => ['queue', 'job', 'process'],

            // Testing
            'test' => ['testing', 'unit', 'pest', 'phpunit'],
            'testing' => ['test', 'unit', 'integration'],

            // CLI
            'command' => ['cli', 'console', 'terminal'],
            'cli' => ['command', 'console', 'terminal'],

            // Configuration
            'config' => ['configuration', 'settings', 'env'],
            'env' => ['environment', 'config', 'settings'],

            // Helpers & Utilities
            'helper' => ['function', 'utility', 'tool', 'helpers'],
            'function' => ['helper', 'utility', 'method'],
            'utility' => ['helper', 'function', 'tool'],

            // Performance
            'benchmark' => ['performance', 'timing', 'benchmark-helpers'],
            'performance' => ['benchmark', 'optimize', 'speed'],
            'timing' => ['benchmark', 'performance', 'measure'],

            // General Programming
            'search' => ['find', 'filter', 'where', 'query'],
            'find' => ['search', 'filter', 'locate'],
            'transform' => ['map', 'convert', 'change'],
            'convert' => ['transform', 'parse', 'change'],
        ];
    }

    /**
     * Get searchable metadata for all documents
     */
    public function getSearchIndex(): array
    {
        return $this->cache->remember('docs.search_index_v2', 3600 * 24, function () {
            $docs = $this->getAvailableDocs();
            $index = [];

            foreach ($docs as $doc) {
                $filePath = $this->resolvePagePath($doc['name']);

                if (! $this->fileMeta->exists($filePath)) {
                    continue;
                }

                $markdown = $this->fileReadWrite->get($filePath);
                $keywords = $this->extractKeywords($markdown);
                $description = $this->extractDescription($markdown);

                $index[] = [
                    'name' => $doc['name'],
                    'title' => $doc['title'],
                    'url' => $doc['url'],
                    'keywords' => $keywords,
                    'description' => $description,
                ];
            }

            return $index;
        });
    }

    public function getAvailableDocs(): array
    {
        return $this->cache->remember('docs.available_v2', 3600 * 24, function () {
            $docs = [];

            // Get files from root docs directory
            $files = glob($this->docsPath . '/*.md');
            foreach ($files as $file) {
                $name = basename($file, '.md');
                $docs[] = [
                    'name' => $name,
                    'title' => ucwords(str_replace('-', ' ', $name)),
                    'url' => '/docs/learn/' . $name,
                ];
            }

            // Get files from subdirectories (e.g., helpers/)
            $subdirs = glob($this->docsPath . '/*', GLOB_ONLYDIR);
            foreach ($subdirs as $subdir) {
                $subdirName = basename($subdir);
                $subFiles = glob($subdir . '/*.md');

                foreach ($subFiles as $file) {
                    $name = basename($file, '.md');
                    $title = ucwords(str_replace('-', ' ', $name));

                    if ($subdirName === 'helpers') {
                        $title .= ' Helpers';
                    }

                    // Generate flat URL: /docs/learn/array-helpers instead of /docs/learn/helpers/array
                    $flatName = $name . '-' . rtrim($subdirName, 's');

                    $docs[] = [
                        'name' => $flatName,
                        'title' => $title,
                        'url' => '/docs/learn/' . $flatName,
                    ];
                }
            }

            sort($docs);

            return $docs;
        });
    }

    public function getGroupedDocs(): array
    {
        return $this->cache->remember('docs.grouped_v2', 3600 * 24, function () {
            $docs = $this->getAvailableDocs();

            $helperDocs = [];
            foreach ($docs as $doc) {
                if (str_ends_with($doc['name'], '-helpers') || str_ends_with($doc['name'], '-helper')) {
                    $helperDocs[] = $doc['name'];
                }
            }

            $groups = [
                'Getting Started' => ['README', 'introduction', 'configuration', 'directory-structure', 'lifecycle'],
                'Core Concepts' => ['architecture', 'kernel', 'container', 'providers', 'services', 'events'],
                'Basics' => ['routing', 'controllers', 'middleware', 'requests', 'responses', 'views', 'view-models'],
                'Database' => ['database', 'query-builder', 'models', 'migrations', 'seeding'],
                'Security' => ['authentication', 'security', 'csrf', 'encryption', 'firewall'],
                'Features' => ['mail', 'notifications', 'defer', 'queues', 'package-management'],
                'Packages' => ['bridge', 'workflow', 'watcher', 'tenancy', 'vault', 'slot', 'tokit', 'money', 'verify', 'wallet', 'pay', 'release', 'flow', 'wave', 'ghost', 'support', 'refer', 'export', 'import', 'media', 'geo', 'audit', 'rollout', 'permit', 'client', 'forge', 'ally', 'link', 'hub', 'scribe', 'proof', 'guide', 'pulse', 'blish', 'stack', 'scout', 'onboard', 'metric', 'shield'],
                'Testing & Quality' => ['testing', 'code-quality', 'troubleshooting', 'debugger'],
                'Console' => array_merge(['cli', 'dock-command']),
                'Helpers' => array_merge(['functions', 'helpers'], $helperDocs),
                'Deployment' => ['deployment', 'best-practices'],
            ];

            $docsMap = array_column($docs, null, 'name');

            $grouped = [];
            $ungrouped = [];

            foreach ($groups as $groupName => $groupDocNames) {
                $grouped[$groupName] = [];
                foreach ($groupDocNames as $docName) {
                    if (isset($docsMap[$docName])) {
                        $grouped[$groupName][] = $docsMap[$docName];
                        unset($docsMap[$docName]);
                    }
                }
            }

            if (!empty($docsMap)) {
                $grouped['Other'] = array_values($docsMap);
            }

            return $this->filterTitles($grouped);
        });
    }

    public function getPageContent(string $page): ?string
    {
        $filePath = $this->resolvePagePath($page);

        if (! $this->fileMeta->exists($filePath)) {
            return null;
        }

        $markdown = $this->fileReadWrite->get($filePath);
        $html = $this->parser->text($markdown);

        $html = preg_replace_callback('/<h([23])>(.*?)<\/h\1>/i', function ($matches) {
            $level = $matches[1];
            $title = strip_tags($matches[2]);

            $title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5);
            $title = str_replace('&amp;', '&', $title);

            $anchor = $this->generateAnchor($title);

            return "<h{$level} id=\"{$anchor}\">{$matches[2]}</h{$level}>";
        }, $html);

        $html = preg_replace('/href="([^"]+)\.md"/', 'href="$1"', $html);
        $html = preg_replace('/href="([^"]+)\.md#([^"]+)"/', 'href="$1#$2"', $html);

        return $html;
    }

    public function getTableOfContents(string $page): array
    {
        $filePath = $this->resolvePagePath($page);

        if (! $this->fileMeta->exists($filePath)) {
            return [];
        }

        $markdown = $this->fileReadWrite->get($filePath);
        $toc = [];

        preg_match_all('/^(#{2,3})\s+(.+)$/m', $markdown, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $level = strlen($match[1]);
            $title = trim($match[2]);
            $anchor = $this->generateAnchor($title);

            $toc[] = [
                'level' => $level,
                'title' => $this->removeHelpersWordsFromTitle($title),
                'anchor' => $anchor,
            ];
        }

        return $toc;
    }

    private function generateAnchor(string $title): string
    {
        $anchor = strtolower($title);
        $anchor = preg_replace('/[^a-z0-9]+/', '-', $anchor);
        $anchor = trim($anchor, '-');

        return $anchor;
    }

    private function removeHelpersWordsFromTitle(string $text): string
    {
        return preg_replace('/\s+helpers$/i', '', trim($text));
    }

    private function filterTitles(array $data): array
    {
        foreach ($data as $section => $items) {
            foreach ($items as $index => $item) {
                if (isset($item['title'])) {
                    $data[$section][$index]['title'] = $this->removeHelpersWordsFromTitle($item['title']);
                }
            }
        }

        return $data;
    }

    /**
     * Resolve a flat URL page name to its actual file path
     * Examples:
     *   'array-helpers' -> 'docs/helpers/array.md'
     *   'introduction' -> 'docs/introduction.md'
     */
    private function resolvePagePath(string $page): string
    {
        $directPath = $this->docsPath . '/' . $page . '.md';
        if ($this->fileMeta->exists($directPath)) {
            return $directPath;
        }

        $subdirs = glob($this->docsPath . '/*', GLOB_ONLYDIR);
        foreach ($subdirs as $subdir) {
            $subdirName = basename($subdir);
            $singularSubdir = rtrim($subdirName, 's');

            if (str_ends_with($page, '-' . $singularSubdir)) {
                $baseName = substr($page, 0, -strlen('-' . $singularSubdir));
                $subdirPath = $this->docsPath . '/' . $subdirName . '/' . $baseName . '.md';

                if ($this->fileMeta->exists($subdirPath)) {
                    return $subdirPath;
                }
            }
        }

        return $this->docsPath . '/' . $page . '.md';
    }

    /**
     * Extract searchable keywords from markdown content
     */
    private function extractKeywords(string $markdown): array
    {
        $text = preg_replace('/```[\s\S]*?```/', '', $markdown);
        $text = preg_replace('/`[^`]+`/', '', $text);

        preg_match_all('/^#{1,3}\s+(.+)$/m', $markdown, $headings);

        $headingWords = implode(' ', $headings[1] ?? []);

        preg_match_all('/\*\*([^*]+)\*\*|\*([^*]+)\*/', $text, $emphasized);

        $emphasizedWords = implode(' ', array_merge($emphasized[1] ?? [], $emphasized[2] ?? []));

        $allText = $headingWords . ' ' . $emphasizedWords . ' ' . $text;
        $allText = strtolower($allText);
        $allText = preg_replace('/[^a-z0-9\s-]/', ' ', $allText);
        preg_match_all('/\b[a-z0-9][a-z0-9-]{2,}\b/', $allText, $words);

        $keywords = array_unique($words[0] ?? []);

        $stopWords = ['the', 'and', 'for', 'are', 'but', 'not', 'you', 'all', 'can', 'has', 'was', 'one', 'our', 'out', 'use', 'get', 'set', 'this', 'that', 'with', 'from', 'they', 'will', 'what', 'when', 'make', 'like', 'time', 'just', 'know', 'take', 'into', 'year', 'your', 'some', 'could', 'them', 'see', 'other', 'than', 'then', 'now', 'look', 'only', 'come', 'its', 'over', 'think', 'also', 'back', 'after', 'two', 'how', 'work', 'first', 'well', 'way', 'even', 'new', 'want', 'because', 'any', 'these', 'give', 'day', 'most', 'her'];
        $keywords = array_diff($keywords, $stopWords);

        return array_values($keywords);
    }

    /**
     * Extract description from markdown (first paragraph)
     */
    private function extractDescription(string $markdown): string
    {
        $text = preg_replace('/^#+\s+.+$/m', '', $markdown);
        $text = preg_replace('/```[\s\S]*?```/', '', $text);
        $text = preg_replace('/^>\s+.*$/m', '', $text);

        preg_match('/^(?!#|\s*$)(.+?)(?=\n\n|\n#|$)/s', trim($text), $match);

        $description = $match[1] ?? '';

        $description = strip_tags($description);
        $description = preg_replace('/`[^`]+`/', '', $description);
        $description = preg_replace('/\s+/', ' ', $description);
        $description = trim($description);

        if (strlen($description) > 150) {
            $description = substr($description, 0, 147) . '...';
        }

        return $description;
    }
}
