<?php
/**
 * Digiws - Admin Category Controller
 */

class AdminCategoryController {
    public function __construct() { requireAdmin(); }

    public function index(): void {
        $categories = (new Category())->getWithProductCount();
        $pageTitle = 'Categories - Admin';
        $adminPage = 'categories';
        $contentView = VIEWS_PATH . '/admin/categories.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function store(): void {
        if (!verifyCsrf()) { setFlash('danger', 'Invalid request.'); redirect(url('admin/categories')); }
        $categoryModel = new Category();
        $data = [
            'name'        => sanitize($_POST['name'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'icon'        => sanitize($_POST['icon'] ?? ''),
            'sort_order'  => (int)($_POST['sort_order'] ?? 0),
            'status'      => $_POST['status'] ?? 'active'
        ];

        if (!empty($_FILES['image']['name'])) {
            $img = uploadFile($_FILES['image'], 'categories');
            if ($img) $data['image'] = $img;
        }

        $categoryModel->create($data);
        setFlash('success', 'Category created.');
        redirect(url('admin/categories'));
    }

    public function update(string $id): void {
        if (!verifyCsrf()) { setFlash('danger', 'Invalid request.'); redirect(url('admin/categories')); }
        $categoryModel = new Category();
        $data = [
            'name'        => sanitize($_POST['name'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'icon'        => sanitize($_POST['icon'] ?? ''),
            'sort_order'  => (int)($_POST['sort_order'] ?? 0),
            'status'      => $_POST['status'] ?? 'active'
        ];

        if (!empty($_FILES['image']['name'])) {
            $img = uploadFile($_FILES['image'], 'categories');
            if ($img) $data['image'] = $img;
        }

        $categoryModel->update((int)$id, $data);
        setFlash('success', 'Category updated.');
        redirect(url('admin/categories'));
    }

    public function delete(string $id): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/categories'));
        }
        (new Category())->delete((int)$id);
        setFlash('success', 'Category deleted.');
        redirect(url('admin/categories'));
    }
}
