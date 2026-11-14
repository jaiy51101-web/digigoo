<?php
/**
 * BlogSEO Model - SEO analysis and optimization
 * Handles SEO scoring, keyword analysis, and optimization recommendations
 */

class BlogSEO {
    private Database $db;
    private BlogBlock $blockModel;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->blockModel = new BlogBlock();
    }

    /**
     * Analyze post for SEO
     */
    public function analyzePost(int $postId): array {
        $post = (new Post())->find($postId);
        if (!$post) return [];

        $blocks = $this->blockModel->getPostBlocks($postId);
        $text = $this->blockModel->getFullPostText($postId);
        $wordCount = str_word_count($text);
        $readingTime = ceil($wordCount / 200); // Average 200 words per minute

        $analysis = [
            'seo_score' => 0,
            'issues' => [],
            'suggestions' => [],
            'keyword_analysis' => [],
            'readability' => [],
            'google_discover' => [],
        ];

        // 1. Title Analysis
        $analysis = $this->analyzeTitleSEO($analysis, $post);

        // 2. Meta Description Analysis
        $analysis = $this->analyzeMetaDescription($analysis, $post);

        // 3. Content Structure
        $analysis = $this->analyzeContentStructure($analysis, $blocks, $text);

        // 4. Keyword Analysis
        $analysis = $this->analyzeKeywords($analysis, $post, $text);

        // 5. Readability Score
        $analysis = $this->analyzeReadability($analysis, $text);

        // 6. Image and Media
        $analysis = $this->analyzeMedia($analysis, $blocks);

        // 7. Internal Links
        $analysis = $this->analyzeLinks($analysis, $postId, $text);

        // 8. Schema Markup
        $analysis = $this->analyzeSchema($analysis, $postId);

        // 9. Google Discover Optimization
        $analysis = $this->analyzeGoogleDiscover($analysis, $post, $blocks);

        // Calculate overall SEO score
        $analysis['seo_score'] = $this->calculateSEOScore($analysis);

        // Save report
        $this->saveSEOReport($postId, $analysis, $wordCount, $readingTime);

        return $analysis;
    }

    /**
     * Analyze title for SEO
     */
    private function analyzeTitleSEO(array $analysis, array $post): array {
        $title = $post['title'] ?? '';
        $titleLength = strlen($title);

        if (empty($title)) {
            $analysis['issues'][] = [
                'type' => 'critical',
                'message' => 'Title is missing',
                'field' => 'title',
            ];
        } elseif ($titleLength < 20) {
            $analysis['issues'][] = [
                'type' => 'warning',
                'message' => 'Title is too short (should be 20-60 characters)',
                'field' => 'title',
            ];
        } elseif ($titleLength > 60) {
            $analysis['issues'][] = [
                'type' => 'warning',
                'message' => 'Title is too long (should be 20-60 characters)',
                'field' => 'title',
            ];
        } else {
            $analysis['seo_score'] += 10;
        }

        // Check for focus keyword in title
        if (!empty($post['focus_keyword'])) {
            if (stripos($title, $post['focus_keyword']) === false) {
                $analysis['suggestions'][] = [
                    'type' => 'keyword',
                    'message' => "Consider including focus keyword '{$post['focus_keyword']}' in title",
                ];
            } else {
                $analysis['seo_score'] += 15;
            }
        }

        return $analysis;
    }

    /**
     * Analyze meta description for SEO
     */
    private function analyzeMetaDescription(array $analysis, array $post): array {
        $metaDesc = $post['meta_description'] ?? '';
        $descLength = strlen($metaDesc);

        if (empty($metaDesc)) {
            $analysis['issues'][] = [
                'type' => 'critical',
                'message' => 'Meta description is missing',
                'field' => 'meta_description',
            ];
        } elseif ($descLength < 50) {
            $analysis['issues'][] = [
                'type' => 'warning',
                'message' => 'Meta description is too short (should be 50-160 characters)',
                'field' => 'meta_description',
            ];
        } elseif ($descLength > 160) {
            $analysis['issues'][] = [
                'type' => 'warning',
                'message' => 'Meta description is too long (should be 50-160 characters)',
                'field' => 'meta_description',
            ];
        } else {
            $analysis['seo_score'] += 10;
        }

        return $analysis;
    }

    /**
     * Analyze content structure
     */
    private function analyzeContentStructure(array $analysis, array $blocks, string $text): array {
        $headingCount = 0;
        $paragraphCount = 0;
        $h1Count = 0;
        $headingHierarchy = [];

        foreach ($blocks as $block) {
            if ($block['block_type'] === 'heading1') {
                $h1Count++;
            } elseif (str_starts_with($block['block_type'], 'heading')) {
                $headingCount++;
                $headingHierarchy[] = $block['block_type'];
            } elseif ($block['block_type'] === 'paragraph') {
                $paragraphCount++;
            }
        }

        // Check H1 count
        if ($h1Count === 0) {
            $analysis['issues'][] = [
                'type' => 'critical',
                'message' => 'No H1 heading found. Add exactly one H1 heading.',
                'field' => 'content',
            ];
        } elseif ($h1Count > 1) {
            $analysis['issues'][] = [
                'type' => 'warning',
                'message' => "Multiple H1 headings found ($h1Count). Use only one H1 per page.",
                'field' => 'content',
            ];
        } else {
            $analysis['seo_score'] += 10;
        }

        // Check heading hierarchy
        if (empty($headingCount)) {
            $analysis['issues'][] = [
                'type' => 'warning',
                'message' => 'No subheadings found. Use H2, H3 headings to structure content.',
                'field' => 'content',
            ];
        } else {
            $analysis['seo_score'] += 10;
        }

        $analysis['content_structure'] = [
            'h1_count' => $h1Count,
            'heading_count' => $headingCount,
            'paragraph_count' => $paragraphCount,
        ];

        return $analysis;
    }

    /**
     * Analyze keywords
     */
    private function analyzeKeywords(array $analysis, array $post, string $text): array {
        if (empty($post['focus_keyword'])) {
            $analysis['suggestions'][] = [
                'type' => 'keyword',
                'message' => 'Set a focus keyword for better SEO targeting',
                'field' => 'focus_keyword',
            ];
            return $analysis;
        }

        $keyword = strtolower($post['focus_keyword']);
        $textLower = strtolower($text);
        
        // Calculate keyword density
        $keywordOccurrences = substr_count($textLower, $keyword);
        $wordCount = str_word_count($text);
        $keywordDensity = ($keywordOccurrences / max($wordCount, 1)) * 100;

        $analysis['keyword_analysis'] = [
            'focus_keyword' => $keyword,
            'occurrences' => $keywordOccurrences,
            'density' => round($keywordDensity, 2),
        ];

        if ($keywordOccurrences === 0) {
            $analysis['issues'][] = [
                'type' => 'warning',
                'message' => "Focus keyword not found in content",
                'field' => 'content',
            ];
        } elseif ($keywordDensity < 0.5) {
            $analysis['suggestions'][] = [
                'type' => 'keyword',
                'message' => "Keyword density is low. Try to include '$keyword' more naturally.",
            ];
        } elseif ($keywordDensity > 3) {
            $analysis['issues'][] = [
                'type' => 'warning',
                'message' => "Keyword density is too high ($keywordDensity%). Avoid keyword stuffing.",
                'field' => 'content',
            ];
        } else {
            $analysis['seo_score'] += 15;
        }

        return $analysis;
    }

    /**
     * Analyze readability
     */
    private function analyzeReadability(array $analysis, string $text): array {
        $wordCount = str_word_count($text);
        $sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $sentenceCount = count($sentences);
        $paragraphs = preg_split('/\n\n+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $paragraphCount = count($paragraphs);

        // Flesch Reading Ease Score (simplified)
        $avgWordsPerSentence = $sentenceCount > 0 ? $wordCount / $sentenceCount : 0;
        $avgSyllablesPerWord = 1.5; // Simplified
        $fleshScore = 206.835 - (1.015 * $avgWordsPerSentence) - (84.6 * $avgSyllablesPerWord);
        $fleshScore = max(0, min(100, $fleshScore));

        $analysis['readability'] = [
            'word_count' => $wordCount,
            'sentence_count' => $sentenceCount,
            'paragraph_count' => $paragraphCount,
            'avg_words_per_sentence' => round($avgWordsPerSentence, 2),
            'flesch_score' => round($fleshScore, 1),
            'reading_time' => ceil($wordCount / 200),
        ];

        if ($wordCount < 300) {
            $analysis['issues'][] = [
                'type' => 'warning',
                'message' => "Content is too short ($wordCount words). Aim for at least 300 words.",
                'field' => 'content',
            ];
        } else {
            $analysis['seo_score'] += 10;
        }

        return $analysis;
    }

    /**
     * Analyze media (images, videos)
     */
    private function analyzeMedia(array $analysis, array $blocks): array {
        $imageCount = 0;
        $imagesWithoutAlt = 0;

        foreach ($blocks as $block) {
            if (in_array($block['block_type'], ['image', 'gallery', 'youtube', 'video'])) {
                $imageCount++;
                
                if ($block['block_type'] === 'image' && empty($block['block_data']['alt'])) {
                    $imagesWithoutAlt++;
                }
            }
        }

        if ($imageCount === 0) {
            $analysis['suggestions'][] = [
                'type' => 'media',
                'message' => 'Add images to make content more engaging and improve SEO',
            ];
        } else {
            $analysis['seo_score'] += 10;
        }

        if ($imagesWithoutAlt > 0) {
            $analysis['issues'][] = [
                'type' => 'warning',
                'message' => "$imagesWithoutAlt image(s) missing ALT text. Add descriptive alt text.",
            ];
        }

        return $analysis;
    }

    /**
     * Analyze internal and external links
     */
    private function analyzeLinks(array $analysis, int $postId, string $text): array {
        $internalLinks = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM blog_internal_links WHERE from_post_id = ?",
            [$postId]
        );

        if ($internalLinks === 0) {
            $analysis['suggestions'][] = [
                'type' => 'links',
                'message' => 'Add internal links to related posts for better SEO',
            ];
        } else {
            $analysis['seo_score'] += 10;
        }

        return $analysis;
    }

    /**
     * Analyze schema markup
     */
    private function analyzeSchema(array $analysis, int $postId): array {
        $schema = $this->db->fetch(
            "SELECT * FROM blog_schemas WHERE post_id = ? LIMIT 1",
            [$postId]
        );

        if (!$schema) {
            $analysis['suggestions'][] = [
                'type' => 'schema',
                'message' => 'Add schema markup for better rich snippets in search results',
            ];
        } else {
            $analysis['seo_score'] += 15;
        }

        return $analysis;
    }

    /**
     * Analyze Google Discover optimization
     */
    private function analyzeGoogleDiscover(array $analysis, array $post, array $blocks): array {
        $discover = [
            'featured_image_ok' => false,
            'headline_quality' => false,
            'freshness_ok' => false,
            'mobile_friendly' => true,
        ];

        // Check featured image
        if (!empty($post['og_image'])) {
            $discover['featured_image_ok'] = true;
            $analysis['seo_score'] += 10;
        } else {
            $analysis['suggestions'][] = [
                'type' => 'discover',
                'message' => 'Add a high-quality featured image for Google Discover',
            ];
        }

        // Check headline quality (compelling title)
        if (strlen($post['title']) >= 20 && strlen($post['title']) <= 60) {
            $discover['headline_quality'] = true;
            $analysis['seo_score'] += 5;
        }

        // Check freshness (published within last 7 days)
        if (!empty($post['published_at'])) {
            $daysSincePublish = (time() - strtotime($post['published_at'])) / (60 * 60 * 24);
            $discover['freshness_ok'] = $daysSincePublish < 30;
            if ($discover['freshness_ok']) $analysis['seo_score'] += 5;
        }

        $analysis['google_discover'] = $discover;
        return $analysis;
    }

    /**
     * Calculate final SEO score
     */
    private function calculateSEOScore(array $analysis): int {
        $score = $analysis['seo_score'] ?? 0;
        
        // Deduct points for issues
        foreach ($analysis['issues'] as $issue) {
            if ($issue['type'] === 'critical') {
                $score -= 10;
            } elseif ($issue['type'] === 'warning') {
                $score -= 5;
            }
        }

        return max(0, min(100, $score + 25)); // Ensure 0-100 range
    }

    /**
     * Save SEO report to database
     */
    private function saveSEOReport(int $postId, array $analysis, int $wordCount, int $readingTime): void {
        $this->db->insert('blog_seo_reports', [
            'post_id' => $postId,
            'report_date' => date('Y-m-d'),
            'seo_score' => $analysis['seo_score'],
            'readability_score' => $analysis['readability']['flesch_score'] ?? 0,
            'word_count' => $wordCount,
            'reading_time' => $readingTime,
            'keyword_density' => json_encode($analysis['keyword_analysis']),
            'issues' => json_encode($analysis['issues']),
            'suggestions' => json_encode($analysis['suggestions']),
        ], true);

        // Update post SEO score
        (new Post())->update($postId, ['seo_score' => $analysis['seo_score']]);
    }

    /**
     * Get latest SEO report for post
     */
    public function getLatestReport(int $postId): ?array {
        return $this->db->fetch(
            "SELECT * FROM blog_seo_reports WHERE post_id = ? ORDER BY report_date DESC LIMIT 1",
            [$postId]
        );
    }
}
