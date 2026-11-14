<?php
/**
 * Digiws - Admin Blog Tag Controller
 */

class AdminBlogTagController {

    public function __construct() {
        requireAdmin();
    }

    public function index(): void {
        $tags = (new BlogTag())->getAll();

        $pageTitle   = 'Blog Tags — Admin';
        $adminPage   = 'blog_tags';
        $contentView = VIEWS_PATH . '/admin/blog-tags.php';

        include VIEWS_PATH . '/admin/layout.php';
    }

    public function store(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/blog/tags'));
        }

        $name = sanitize($_POST['name'] ?? '');
        if (empty($name)) {
            setFlash('danger', 'Tag name is required.');
            redirect(url('admin/blog/tags'));
        }

        $slug = uniqueSlug($name, 'blog_tags');
        (new BlogTag())->create(['name' => $name, 'slug' => $slug]);
        setFlash('success', 'Tag created!');
        redirect(url('admin/blog/tags'));
    }

    public function update(int $id): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/blog/tags'));
        }

        $name = sanitize($_POST['name'] ?? '');
        if (empty($name)) {
            setFlash('danger', 'Tag name required.');
            redirect(url('admin/blog/tags'));
        }

        $slug = generateSlug($name);
        (new BlogTag())->update($id, ['name' => $name, 'slug' => $slug]);
        setFlash('success', 'Tag updated!');
        redirect(url('admin/blog/tags'));
    }

    public function bulkImport(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/blog/tags'));
        }
        $raw = sanitize($_POST['tags'] ?? '');
        if (empty($raw)) {
            setFlash('warning', 'No tags provided.');
            redirect(url('admin/blog/tags'));
        }
        $names = array_unique(array_filter(array_map('trim', explode(',', $raw))));
        $created = 0;
        $tagModel = new BlogTag();
        foreach ($names as $name) {
            $slug = generateSlug($name);
            $exists = $tagModel->findBySlug($slug);
            if (!$exists) {
                $tagModel->create(['name' => $name, 'slug' => uniqueSlug($slug, 'blog_tags')]);
                $created++;
            }
        }
        setFlash('success', "Imported {$created} new tag(s). " . (count($names) - $created) . ' already existed.');
        redirect(url('admin/blog/tags'));
    }

    public function delete(int $id): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/blog/tags'));
        }
        (new BlogTag())->delete($id);
        setFlash('success', 'Tag deleted.');
        redirect(url('admin/blog/tags'));
    }
}
