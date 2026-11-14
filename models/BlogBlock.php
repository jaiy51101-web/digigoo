<?php
/**
 * BlogBlock Model - Manage individual post blocks (Gutenberg-like)
 * Handles CRUD operations for post blocks
 */

class BlogBlock {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Add a new block to a post
     */
    public function addBlock(int $postId, int $index, string $type, array $data, ?array $attributes = null): int {
        $this->db->insert('blog_post_blocks', [
            'post_id' => $postId,
            'block_index' => $index,
            'block_type' => $type,
            'block_data' => json_encode($data, JSON_UNESCAPED_SLASHES),
            'attributes' => $attributes ? json_encode($attributes) : null,
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Get all blocks for a post
     */
    public function getPostBlocks(int $postId): array {
        $blocks = $this->db->fetchAll(
            "SELECT * FROM blog_post_blocks WHERE post_id = ? ORDER BY block_index ASC",
            [$postId]
        );
        
        foreach ($blocks as &$block) {
            $block['block_data'] = json_decode($block['block_data'], true);
            $block['attributes'] = json_decode($block['attributes'], true);
        }
        
        return $blocks;
    }

    /**
     * Update a block
     */
    public function updateBlock(int $blockId, array $data, ?array $attributes = null): bool {
        return $this->db->update('blog_post_blocks', [
            'block_data' => json_encode($data, JSON_UNESCAPED_SLASHES),
            'attributes' => $attributes ? json_encode($attributes) : null,
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $blockId]);
    }

    /**
     * Reorder blocks (bulk update)
     */
    public function reorderBlocks(int $postId, array $blockOrder): bool {
        foreach ($blockOrder as $index => $blockId) {
            $this->db->update(
                'blog_post_blocks',
                ['block_index' => $index],
                ['id' => $blockId, 'post_id' => $postId]
            );
        }
        return true;
    }

    /**
     * Delete a block
     */
    public function deleteBlock(int $blockId): bool {
        return $this->db->delete('blog_post_blocks', ['id' => $blockId]);
    }

    /**
     * Duplicate a block
     */
    public function duplicateBlock(int $blockId, int $targetIndex): int {
        $block = $this->db->fetch(
            "SELECT * FROM blog_post_blocks WHERE id = ?",
            [$blockId]
        );
        
        if (!$block) return 0;

        return $this->addBlock(
            $block['post_id'],
            $targetIndex,
            $block['block_type'],
            json_decode($block['block_data'], true),
            $block['attributes'] ? json_decode($block['attributes'], true) : null
        );
    }

    /**
     * Toggle block visibility
     */
    public function toggleBlockVisibility(int $blockId): bool {
        $block = $this->db->fetch("SELECT is_hidden FROM blog_post_blocks WHERE id = ?", [$blockId]);
        if (!$block) return false;

        return $this->db->update(
            'blog_post_blocks',
            ['is_hidden' => !$block['is_hidden']],
            ['id' => $blockId]
        );
    }

    /**
     * Convert a block type to another type
     */
    public function convertBlockType(int $blockId, string $newType): bool {
        return $this->db->update(
            'blog_post_blocks',
            ['block_type' => $newType],
            ['id' => $blockId]
        );
    }

    /**
     * Get block content as plain text
     */
    public function getBlockText(int $blockId): string {
        $block = $this->db->fetch(
            "SELECT block_type, block_data FROM blog_post_blocks WHERE id = ?",
            [$blockId]
        );
        
        if (!$block) return '';

        $data = json_decode($block['block_data'], true);
        
        return match($block['block_type']) {
            'paragraph' => $data['content'] ?? '',
            'heading', 'heading2', 'heading3', 'heading4', 'heading5', 'heading6' => $data['content'] ?? '',
            'quote' => $data['content'] ?? '',
            'image' => $data['alt'] ?? $data['caption'] ?? '',
            'video' => $data['caption'] ?? '',
            'button' => $data['text'] ?? '',
            default => ''
        };
    }

    /**
     * Extract all text from post blocks for analysis
     */
    public function getFullPostText(int $postId): string {
        $blocks = $this->getPostBlocks($postId);
        $text = [];
        
        foreach ($blocks as $block) {
            $text[] = $this->getBlockText($block['id']);
        }
        
        return implode("\n", array_filter($text));
    }

    /**
     * Calculate word count from blocks
     */
    public function calculateWordCount(int $postId): int {
        $text = $this->getFullPostText($postId);
        return str_word_count($text);
    }

    /**
     * Get block statistics for a post
     */
    public function getPostBlockStats(int $postId): array {
        $blocks = $this->getPostBlocks($postId);
        $stats = [
            'total_blocks' => count($blocks),
            'by_type' => [],
            'paragraph_blocks' => 0,
            'heading_blocks' => 0,
            'image_blocks' => 0,
            'video_blocks' => 0,
        ];
        
        foreach ($blocks as $block) {
            $type = $block['block_type'];
            $stats['by_type'][$type] = ($stats['by_type'][$type] ?? 0) + 1;
            
            if (str_starts_with($type, 'heading')) {
                $stats['heading_blocks']++;
            } elseif ($type === 'paragraph') {
                $stats['paragraph_blocks']++;
            } elseif ($type === 'image' || $type === 'gallery') {
                $stats['image_blocks']++;
            } elseif ($type === 'video' || $type === 'youtube') {
                $stats['video_blocks']++;
            }
        }
        
        return $stats;
    }
}
