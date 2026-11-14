<?php
/**
 * Digiws - Admin Blog Category Controller
 */

class AdminBlogCategoryController {

    public function __construct() {
        requireAdmin();
    }

    public function index(): void {
        $categories = (new BlogCategory())->getAll(false);

        $pageTitle   = 'Blog Categories — Admin';
        $adminPage   = 'blog_categories';
        $contentView = VIEWS_PATH . '/admin/blog-categories.php';

        include VIEWS_PATH . '/admin/layout.php';
    }

    public function store(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/blog/categories'));
        }

        $name = sanitize($_POST['name'] ?? '');
        if (empty($name)) {
            setFlash('danger', 'Category name is required.');
            redirect(url('admin/blog/categories'));
        }

        $slug = uniqueSlug($name, 'blog_categories');

        $data = [
            'name'             => $name,
            'slug'             => sanitize($_POST['slug'] ?? $slug) ?: $slug,
            'description'      => sanitize($_POST['description'] ?? ''),
            'icon'             => sanitize($_POST['icon'] ?? 'bi-folder'),
            'color'            => sanitize($_POST['color'] ?? '#2563eb'),
            'sort_order'       => (int)($_POST['sort_order'] ?? 0),
            'status'           => sanitize($_POST['status'] ?? 'active'),
            'meta_title'       => sanitize($_POST['meta_title'] ?? ''),
            'meta_description' => sanitize($_POST['meta_description'] ?? ''),
        ];

        if (!empty($_FILES['image']['name'])) {
            $img = uploadFile($_FILES['image'], 'blog/categories');
            if ($img) $data['image'] = $img;
        }

        (new BlogCategory())->create($data);
        setFlash('success', 'Category created!');
        redirect(url('admin/blog/categories'));
    }

    public function update(int $id): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/blog/categories'));
        }

        $model    = new BlogCategory();
        $category = $model->find($id);
        if (!$category) {
            setFlash('danger', 'Category not found.');
            redirect(url('admin/blog/categories'));
        }

        $name = sanitize($_POST['name'] ?? '');
        if (empty($name)) {
            setFlash('danger', 'Name required.');
            redirect(url('admin/blog/categories'));
        }

        $data = [
            'name'             => $name,
            'description'      => sanitize($_POST['description'] ?? ''),
            'icon'             => sanitize($_POST['icon'] ?? 'bi-folder'),
            'color'            => sanitize($_POST['color'] ?? '#2563eb'),
            'sort_order'       => (int)($_POST['sort_order'] ?? 0),
            'status'           => sanitize($_POST['status'] ?? 'active'),
            'meta_title'       => sanitize($_POST['meta_title'] ?? ''),
            'meta_description' => sanitize($_POST['meta_description'] ?? ''),
        ];

        if (!empty($_FILES['image']['name'])) {
            $img = uploadFile($_FILES['image'], 'blog/categories');
            if ($img) {
                if (!empty($category['image'])) deleteFile($category['image']);
                $data['image'] = $img;
            }
        }

        $model->update($id, $data);
        setFlash('success', 'Category updated!');
        redirect(url('admin/blog/categories'));
    }

    public function delete(int $id): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/blog/categories'));
        }
        (new BlogCategory())->delete($id);
        setFlash('success', 'Category deleted.');
        redirect(url('admin/blog/categories'));
    }
}
