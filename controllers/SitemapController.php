<?php
/**
 * DigiWS - Sitemap Controller
 */

class SitemapController {
    private SitemapService $sitemapService;

    public function __construct() {
        $this->sitemapService = new SitemapService();
    }

    /**
     * Serves the main sitemap index (/sitemap.xml or /sitemap-index.xml)
     */
    public function index(): void {
        $this->sitemapService->serve('sitemap');
    }

    /**
     * Serves sub-sitemaps (e.g. sitemap-products.xml, sitemap-blog.xml, etc.)
     */
    public function show(string $type): void {
        // Sanitize type parameter
        $type = preg_replace('/[^a-zA-Z0-9\-]/', '', $type);
        $this->sitemapService->serve('sitemap-' . $type);
    }

    /**
     * Serves dynamic robots.txt
     */
    public function robotsTxt(): void {
        $this->sitemapService->serveRobots();
    }
}
