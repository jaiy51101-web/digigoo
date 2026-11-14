<?php
/**
 * BlogRevision Model - Version history and autosave
 * Handles revision tracking, autosave, and version restoration
 */

class BlogRevision {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Create a new revision
     */
    public function createRevision(
        int $postId,
        string $title,
        string $summary,
        array $blocks,
        array $meta,
        int $authorId,
        string $type = 'manual',
        ?string $changeSummary = null,
        bool $isAutosave = false
    ): int {
        // Get next revision number
        $lastRevision = $this->db->fetch(
            "SELECT MAX(revision_number) as max_rev FROM blog_revisions WHERE post_id = ?",
            [$postId]
        );
        $nextRevision = ($lastRevision['max_rev'] ?? 0) + 1;

        $this->db->insert('blog_revisions', [
            'post_id' => $postId,
            'revision_number' => $nextRevision,
            'title' => $title,
            'summary' => $summary,
            'content_snapshot' => json_encode($blocks, JSON_UNESCAPED_SLASHES),
            'meta_snapshot' => json_encode($meta),
            'author_id' => $authorId,
            'revision_type' => $type,
            'change_summary' => $changeSummary,
            'is_autosave' => $isAutosave ? 1 : 0,
        ]);

        $revisionId = $this->db->lastInsertId();

        // Clean up old revisions if exceeds limit
        $maxRevisions = (int)getSetting('blog_max_revisions', 100);
        $this->cleanOldRevisions($postId, $maxRevisions);

        return $revisionId;
    }

    /**
     * Get revision by ID
     */
    public function getRevision(int $revisionId): ?array {
        $revision = $this->db->fetch(
            "SELECT * FROM blog_revisions WHERE id = ?",
            [$revisionId]
        );

        if ($revision) {
            $revision['content_snapshot'] = json_decode($revision['content_snapshot'], true);
            $revision['meta_snapshot'] = json_decode($revision['meta_snapshot'], true);
        }

        return $revision;
    }

    /**
     * Get all revisions for a post
     */
    public function getPostRevisions(int $postId, bool $excludeAutosave = false): array {
        $where = "post_id = ?";
        $params = [$postId];

        if ($excludeAutosave) {
            $where .= " AND is_autosave = 0";
        }

        $revisions = $this->db->fetchAll(
            "SELECT id, post_id, revision_number, title, author_id, revision_type, 
                    is_autosave, change_summary, created_at 
             FROM blog_revisions 
             WHERE $where 
             ORDER BY revision_number DESC",
            $params
        );

        return $revisions;
    }

    /**
     * Get latest autosave
     */
    public function getLatestAutosave(int $postId): ?array {
        $autosave = $this->db->fetch(
            "SELECT * FROM blog_revisions 
             WHERE post_id = ? AND is_autosave = 1 
             ORDER BY created_at DESC LIMIT 1",
            [$postId]
        );

        if ($autosave) {
            $autosave['content_snapshot'] = json_decode($autosave['content_snapshot'], true);
            $autosave['meta_snapshot'] = json_decode($autosave['meta_snapshot'], true);
        }

        return $autosave;
    }

    /**
     * Restore a revision to the post
     */
    public function restoreRevision(int $revisionId, int $currentAuthorId): bool {
        $revision = $this->getRevision($revisionId);
        if (!$revision) {
            return false;
        }

        $postId = $revision['post_id'];
        $blocks = $revision['content_snapshot'];
        $meta = $revision['meta_snapshot'];

        try {
            // Start transaction
            $this->db->query("START TRANSACTION");

            // Delete current blocks
            $this->db->delete('blog_post_blocks', ['post_id' => $postId]);

            // Restore blocks
            $blockModel = new BlogBlock();
            foreach ($blocks as $index => $block) {
                $blockModel->addBlock(
                    $postId,
                    $index,
                    $block['type'],
                    $block['data'] ?? [],
                    $block['attributes'] ?? null
                );
            }

            // Update post metadata
            if ($meta) {
                foreach ($meta as $key => $value) {
                    $this->setPostMeta($postId, $key, $value);
                }
            }

            // Update post
            $this->db->update('posts', [
                'title' => $revision['title'],
                'summary' => $revision['summary'],
                'updated_at' => date('Y-m-d H:i:s'),
            ], ['id' => $postId]);

            // Create a restoration revision
            $this->createRevision(
                $postId,
                $revision['title'],
                $revision['summary'],
                $blocks,
                $meta,
                $currentAuthorId,
                'manual',
                "Restored from revision #{$revision['revision_number']}"
            );

            $this->db->query("COMMIT");
            return true;
        } catch (Exception $e) {
            $this->db->query("ROLLBACK");
            return false;
        }
    }

    /**
     * Compare two revisions
     */
    public function compareRevisions(int $revisionId1, int $revisionId2): array {
        $rev1 = $this->getRevision($revisionId1);
        $rev2 = $this->getRevision($revisionId2);

        if (!$rev1 || !$rev2) {
            return [];
        }

        return [
            'revision1' => [
                'number' => $rev1['revision_number'],
                'title' => $rev1['title'],
                'created_at' => $rev1['created_at'],
                'blocks' => $rev1['content_snapshot'],
            ],
            'revision2' => [
                'number' => $rev2['revision_number'],
                'title' => $rev2['title'],
                'created_at' => $rev2['created_at'],
                'blocks' => $rev2['content_snapshot'],
            ],
            'differences' => $this->calculateDifferences($rev1['content_snapshot'], $rev2['content_snapshot']),
        ];
    }

    /**
     * Calculate differences between revisions
     */
    private function calculateDifferences(array $blocks1, array $blocks2): array {
        $differences = [
            'blocks_added' => [],
            'blocks_removed' => [],
            'blocks_modified' => [],
        ];

        // Simple comparison - can be enhanced with more detailed diff
        if (count($blocks1) > count($blocks2)) {
            $differences['blocks_removed'] = array_slice($blocks1, count($blocks2));
        } elseif (count($blocks2) > count($blocks1)) {
            $differences['blocks_added'] = array_slice($blocks2, count($blocks1));
        }

        // Check for modifications
        for ($i = 0; $i < min(count($blocks1), count($blocks2)); $i++) {
            if (json_encode($blocks1[$i]) !== json_encode($blocks2[$i])) {
                $differences['blocks_modified'][] = $i;
            }
        }

        return $differences;
    }

    /**
     * Clean old revisions (keep only X most recent)
     */
    private function cleanOldRevisions(int $postId, int $maxCount): bool {
        $revisions = $this->db->fetchAll(
            "SELECT id FROM blog_revisions WHERE post_id = ? ORDER BY created_at DESC",
            [$postId]
        );

        if (count($revisions) <= $maxCount) {
            return true;
        }

        $idsToDelete = array_slice($revisions, $maxCount);
        foreach ($idsToDelete as $rev) {
            $this->db->delete('blog_revisions', ['id' => $rev['id']]);
        }

        return true;
    }

    /**
     * Delete old autosaves (keep last 10)
     */
    public function cleanOldAutosaves(int $postId, int $keepCount = 10): bool {
        $autosaves = $this->db->fetchAll(
            "SELECT id FROM blog_revisions WHERE post_id = ? AND is_autosave = 1 
             ORDER BY created_at DESC",
            [$postId]
        );

        if (count($autosaves) <= $keepCount) {
            return true;
        }

        $idsToDelete = array_slice($autosaves, $keepCount);
        foreach ($idsToDelete as $autosave) {
            $this->db->delete('blog_revisions', ['id' => $autosave['id']]);
        }

        return true;
    }

    /**
     * Get revision history timeline
     */
    public function getTimeline(int $postId, int $limit = 20): array {
        return $this->db->fetchAll(
            "SELECT r.id, r.revision_number, r.title, r.author_id, u.name as author_name, 
                    r.revision_type, r.is_autosave, r.change_summary, r.created_at,
                    (SELECT COUNT(*) FROM blog_post_blocks WHERE post_id = ?) as block_count
             FROM blog_revisions r
             LEFT JOIN users u ON r.author_id = u.id
             WHERE r.post_id = ?
             ORDER BY r.created_at DESC
             LIMIT ?",
            [$postId, $postId, $limit]
        );
    }

    // Post Meta helpers
    private function setPostMeta(int $postId, string $key, $value): void {
        $existing = $this->db->fetch(
            "SELECT id FROM blog_post_meta WHERE post_id = ? AND meta_key = ?",
            [$postId, $key]
        );

        if ($existing) {
            $this->db->update('blog_post_meta', ['meta_value' => serialize($value)], 
                ['post_id' => $postId, 'meta_key' => $key]);
        } else {
            $this->db->insert('blog_post_meta', [
                'post_id' => $postId,
                'meta_key' => $key,
                'meta_value' => serialize($value),
            ]);
        }
    }
}
