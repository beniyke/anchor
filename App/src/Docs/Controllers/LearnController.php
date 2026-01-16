<?php

declare(strict_types=1);

namespace App\Docs\Controllers;

use App\Core\BaseController;
use App\Docs\Services\DocsService;
use Helpers\Http\Response;

class LearnController extends BaseController
{
    public function index(DocsService $service, ?string $page = null): Response
    {
        $groupedDocs = $service->getGroupedDocs();
        $content = null;

        if ($page) {
            $page = str_replace(['..', '\\'], ['', '/'], $page);
            $page = trim($page, '/');

            $content = $service->getPageContent($page);

            if (!$content) {
                return $this->asView('404', compact('page'));
            }

            $titlePart = basename($page);
            $title = ucfirst(str_replace('-', ' ', $titlePart));
            $currentPage = $page;
            $toc = $service->getTableOfContents($page);
            $keywordMap = $service->getKeywordMap();
            $searchIndex = $service->getSearchIndex();

            return $this->asView('show', compact('groupedDocs', 'content', 'title', 'currentPage', 'toc', 'keywordMap', 'searchIndex'));
        }

        $keywordMap = $service->getKeywordMap();
        $searchIndex = $service->getSearchIndex();

        return $this->asView('docs', compact('groupedDocs', 'keywordMap', 'searchIndex'));
    }
}
