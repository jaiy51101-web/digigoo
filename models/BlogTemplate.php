<?php
/**
 * BlogTemplate Model - Article template management
 * Handle template creation, usage tracking, and content generation
 */

class BlogTemplate {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get template by ID
     */
    public function get(int $templateId): ?array {
        $template = $this->db->fetch(
            "SELECT * FROM blog_templates WHERE id = ?",
            [$templateId]
        );

        if ($template) {
            $template['blocks_template'] = json_decode($template['blocks_template'], true);
            $template['meta_template'] = json_decode($template['meta_template'], true);
        }

        return $template;
    }

    /**
     * Get all templates
     */
    public function getAll(bool $activeOnly = true): array {
        $where = $activeOnly ? "is_active = 1" : "1=1";
        $templates = $this->db->fetchAll(
            "SELECT * FROM blog_templates WHERE $where ORDER BY template_type ASC, name ASC"
        );

        foreach ($templates as &$template) {
            $template['blocks_template'] = json_decode($template['blocks_template'], true);
            $template['meta_template'] = json_decode($template['meta_template'], true);
        }

        return $templates;
    }

    /**
     * Get templates by type
     */
    public function getByType(string $type): array {
        $templates = $this->db->fetchAll(
            "SELECT * FROM blog_templates WHERE template_type = ? AND is_active = 1 ORDER BY name ASC",
            [$type]
        );

        foreach ($templates as &$template) {
            $template['blocks_template'] = json_decode($template['blocks_template'], true);
            $template['meta_template'] = json_decode($template['meta_template'], true);
        }

        return $templates;
    }

    /**
     * Create new template
     */
    public function create(array $data): int {
        $this->db->insert('blog_templates', [
            'name' => $data['name'],
            'slug' => $data['slug'] ?? slugify($data['name']),
            'description' => $data['description'] ?? null,
            'thumbnail' => $data['thumbnail'] ?? null,
            'template_type' => $data['template_type'],
            'blocks_template' => json_encode($data['blocks_template'] ?? []),
            'meta_template' => json_encode($data['meta_template'] ?? []),
            'ai_prompt' => $data['ai_prompt'] ?? null,
            'is_active' => $data['is_active'] ?? 1,
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Update template
     */
    public function update(int $templateId, array $data): bool {
        $updateData = [];
        
        if (isset($data['name'])) $updateData['name'] = $data['name'];
        if (isset($data['description'])) $updateData['description'] = $data['description'];
        if (isset($data['thumbnail'])) $updateData['thumbnail'] = $data['thumbnail'];
        if (isset($data['blocks_template'])) $updateData['blocks_template'] = json_encode($data['blocks_template']);
        if (isset($data['meta_template'])) $updateData['meta_template'] = json_encode($data['meta_template']);
        if (isset($data['ai_prompt'])) $updateData['ai_prompt'] = $data['ai_prompt'];
        if (isset($data['is_active'])) $updateData['is_active'] = $data['is_active'];

        return $this->db->update('blog_templates', $updateData, ['id' => $templateId]);
    }

    /**
     * Delete template
     */
    public function delete(int $templateId): bool {
        return $this->db->delete('blog_templates', ['id' => $templateId]);
    }

    /**
     * Apply template to new post
     */
    public function applyTemplate(int $templateId, int $postId): bool {
        $template = $this->get($templateId);
        if (!$template) {
            return false;
        }

        // Delete existing blocks
        $this->db->delete('blog_post_blocks', ['post_id' => $postId]);

        // Add template blocks
        $blockModel = new BlogBlock();
        foreach ($template['blocks_template'] as $index => $block) {
            $blockModel->addBlock(
                $postId,
                $index,
                $block['type'],
                $block['data'] ?? [],
                $block['attributes'] ?? null
            );
        }

        // Apply template metadata if exists
        if (!empty($template['meta_template'])) {
            $post = (new Post())->find($postId);
            if ($post) {
                $updateData = [];
                
                if (!empty($template['meta_template']['focus_keyword'])) {
                    $updateData['focus_keyword'] = $template['meta_template']['focus_keyword'];
                }
                if (!empty($template['meta_template']['slug_hint'])) {
                    $updateData['slug'] = $template['meta_template']['slug_hint'];
                }

                if (!empty($updateData)) {
                    (new Post())->update($postId, $updateData);
                }
            }
        }

        // Increment usage count
        $this->db->query(
            "UPDATE blog_templates SET usage_count = usage_count + 1 WHERE id = ?",
            [$templateId]
        );

        return true;
    }

    /**
     * Get popular templates
     */
    public function getPopular(int $limit = 5): array {
        $templates = $this->db->fetchAll(
            "SELECT * FROM blog_templates WHERE is_active = 1 ORDER BY usage_count DESC LIMIT ?",
            [$limit]
        );

        foreach ($templates as &$template) {
            $template['blocks_template'] = json_decode($template['blocks_template'], true);
            $template['meta_template'] = json_decode($template['meta_template'], true);
        }

        return $templates;
    }
}
