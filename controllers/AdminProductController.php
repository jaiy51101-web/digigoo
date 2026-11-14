<?php
/**
 * Digiws - Admin Product Controller
 */

class AdminProductController {

    public function __construct() {
        requireAdmin();
    }

    public function index(): void {
        $productModel = new Product();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $filters = ['all_status' => true, 'search' => $_GET['q'] ?? '', 'sort' => $_GET['sort'] ?? 'newest'];
        if (!empty($_GET['status'])) $filters['status'] = $_GET['status'];

        $result = $productModel->getAll($page, 15, $filters);
        $products = $result['products'];
        $totalProducts = $result['total'];

        $pageTitle = 'Products - Admin';
        $adminPage = 'products';
        $contentView = VIEWS_PATH . '/admin/products.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function create(): void {
        $categories = (new Category())->getAll();
        $product = null;

        $pageTitle = 'Add Product - Admin';
        $adminPage = 'products';
        $contentView = VIEWS_PATH . '/admin/product-form.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            setFlash('danger', 'The uploaded ZIP file exceeds the maximum upload limits of your hosting server. Please use the "External Download URL" option to deliver the product, or increase "upload_max_filesize" and "post_max_size" in your server\'s php.ini configuration.');
            redirect(url('admin/products/create'));
        }

        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/products'));
        }

        $productModel = new Product();

        $data = [
            'title'            => sanitize($_POST['title'] ?? ''),
            'digiws_product_id' => sanitize($_POST['digiws_product_id'] ?? ''),
            'short_description' => sanitize($_POST['short_description'] ?? ''),
            'description'      => $_POST['description'] ?? '',
            'category_ids'      => $_POST['category_ids'] ?? [],
            'video_url'        => sanitize($_POST['video_url'] ?? ''),
            'product_type'     => $_POST['product_type'] ?? 'other',
            'mrp'              => (float)($_POST['mrp'] ?? 0),
            'sale_price'       => (float)($_POST['sale_price'] ?? 0),
            'gst_percent'      => (float)($_POST['gst_percent'] ?? 18),
            'setup_option'     => isset($_POST['setup_option']) ? 1 : 0,
            'setup_price'      => (float)($_POST['setup_price'] ?? 0),
            'support_6_months' => (float)($_POST['support_6_months'] ?? 0),
            'support_12_months' => (float)($_POST['support_12_months'] ?? 0),
            'preview_url'      => sanitize($_POST['preview_url'] ?? ''),
            'external_url'     => sanitize($_POST['external_url'] ?? ''),
            'tags'             => sanitize($_POST['tags'] ?? ''),
            'is_featured'      => isset($_POST['is_featured']) ? 1 : 0,
            'is_bestseller'    => isset($_POST['is_bestseller']) ? 1 : 0,
            'is_new'           => isset($_POST['is_new']) ? 1 : 0,
            'is_exclusive'     => isset($_POST['is_exclusive']) ? 1 : 0,
            'status'           => $_POST['status'] ?? 'draft'
        ];

        if (empty($data['title'])) {
            setFlash('danger', 'Product title is required.');
            redirect(url('admin/products/create'));
        }

        // Upload thumbnail
        if (!empty($_FILES['thumbnail']['name'])) {
            $thumb = uploadFile($_FILES['thumbnail'], 'products/thumbnails');
            if ($thumb) $data['thumbnail'] = $thumb;
        }

        // Download handle (Multiple Platforms & Files)
        $finalResources = [];
        if (isset($_POST['resources']) && is_array($_POST['resources'])) {
            foreach ($_POST['resources'] as $index => $res) {
                $platform = sanitize($res['platform'] ?? 'other');
                if ($platform === 'server') {
                    $fileKey = "resource_file_{$index}";
                    if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                        $download = uploadFile($_FILES[$fileKey], 'products/files', [
                            'application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-rar-compressed', 'application/gzip',
                            'application/x-tar', 'application/x-7z-compressed', 'application/octet-stream'
                        ]);
                        if ($download) {
                            $finalResources[] = [
                                'platform' => 'server',
                                'url'      => $download
                            ];
                        }
                    }
                } else {
                    $url = sanitize($res['url'] ?? '');
                    if (!empty($url)) {
                        $finalResources[] = [
                            'platform' => $platform,
                            'url'      => $url
                        ];
                    }
                }
            }
        }
        $data['download_file'] = !empty($finalResources) ? json_encode($finalResources) : null;

        try {
            $productId = $productModel->create($data);

            // Trigger new_product_published automation if product status is published
            if ($data['status'] === 'published') {
                MarketingSubscriber::triggerAutomation('new_product_published', [
                    'product' => array_merge(['id' => $productId], $data)
                ]);
            }
        } catch (\Throwable $e) {
            error_log('DigiWS product creation failed: ' . $e->getMessage());
            setFlash('danger', 'Product could not be saved. Please run the database migration, then try again.');
            redirect(url('admin/products/create'));
        }

        try {
            // Upload screenshots
            if (!empty($_FILES['screenshots']['name'][0])) {
                foreach ($_FILES['screenshots']['name'] as $i => $name) {
                    if ($_FILES['screenshots']['error'][$i] !== UPLOAD_ERR_OK) continue;
                    $file = [
                        'name'     => $_FILES['screenshots']['name'][$i],
                        'type'     => $_FILES['screenshots']['type'][$i],
                        'tmp_name' => $_FILES['screenshots']['tmp_name'][$i],
                        'error'    => $_FILES['screenshots']['error'][$i],
                        'size'     => $_FILES['screenshots']['size'][$i]
                    ];
                    $path = uploadFile($file, 'products/screenshots');
                    if ($path) {
                        $productModel->addImage($productId, $path, $i);
                    }
                }
            }

            // Save features
            if (!empty($_POST['features'])) {
                $features = is_array($_POST['features']) ? $_POST['features'] : explode("\n", $_POST['features']);
                $productModel->syncFeatures($productId, $features);
            }
        } catch (\Throwable $e) {
            // The product itself was saved. Do not turn a recoverable media or
            // feature issue into an HTTP 500 response.
            error_log('DigiWS product post-save processing failed: ' . $e->getMessage());
            setFlash('warning', 'Product was created, but one or more screenshots or features could not be saved.');
            redirect(url('admin/products/edit/' . $productId));
        }

        setFlash('success', 'Product created successfully.');
        redirect(url('admin/products'));
    }

    public function edit(string $id): void {
        $productModel = new Product();
        $product = $productModel->find((int)$id);

        if (!$product) {
            setFlash('danger', 'Product not found.');
            redirect(url('admin/products'));
        }

        $categories = (new Category())->getAll();

        $pageTitle = 'Edit Product - Admin';
        $adminPage = 'products';
        $contentView = VIEWS_PATH . '/admin/product-form.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function update(string $id): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            setFlash('danger', 'The uploaded ZIP file exceeds the maximum upload limits of your hosting server. Please use the "External Download URL" option to deliver the product, or increase "upload_max_filesize" and "post_max_size" in your server\'s php.ini configuration.');
            redirect(url('admin/products/edit/' . $id));
        }

        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/products'));
        }

        $productId = (int)$id;
        $productModel = new Product();
        $existing = $productModel->find($productId);

        if (!$existing) {
            setFlash('danger', 'Product not found.');
            redirect(url('admin/products'));
        }

        $data = [
            'title'            => sanitize($_POST['title'] ?? ''),
            'digiws_product_id' => sanitize($_POST['digiws_product_id'] ?? ''),
            'short_description' => sanitize($_POST['short_description'] ?? ''),
            'description'      => $_POST['description'] ?? '',
            'category_ids'      => $_POST['category_ids'] ?? [],
            'video_url'        => sanitize($_POST['video_url'] ?? ''),
            'product_type'     => $_POST['product_type'] ?? 'other',
            'mrp'              => (float)($_POST['mrp'] ?? 0),
            'sale_price'       => (float)($_POST['sale_price'] ?? 0),
            'gst_percent'      => (float)($_POST['gst_percent'] ?? 18),
            'setup_option'     => isset($_POST['setup_option']) ? 1 : 0,
            'setup_price'      => (float)($_POST['setup_price'] ?? 0),
            'support_6_months' => (float)($_POST['support_6_months'] ?? 0),
            'support_12_months' => (float)($_POST['support_12_months'] ?? 0),
            'preview_url'      => sanitize($_POST['preview_url'] ?? ''),
            'external_url'     => sanitize($_POST['external_url'] ?? ''),
            'tags'             => sanitize($_POST['tags'] ?? ''),
            'is_featured'      => isset($_POST['is_featured']) ? 1 : 0,
            'is_bestseller'    => isset($_POST['is_bestseller']) ? 1 : 0,
            'is_new'           => isset($_POST['is_new']) ? 1 : 0,
            'is_exclusive'     => isset($_POST['is_exclusive']) ? 1 : 0,
            'status'           => $_POST['status'] ?? 'draft'
        ];

        // Upload new thumbnail
        if (!empty($_FILES['thumbnail']['name'])) {
            $thumb = uploadFile($_FILES['thumbnail'], 'products/thumbnails');
            if ($thumb) {
                if ($existing['thumbnail']) deleteFile($existing['thumbnail']);
                $data['thumbnail'] = $thumb;
            }
        } elseif (isset($_POST['delete_thumbnail']) && $existing['thumbnail']) {
            deleteFile($existing['thumbnail']);
            $data['thumbnail'] = null;
        }

        // Download handle (Multiple Platforms & Files)
        $finalResources = [];
        if (isset($_POST['resources']) && is_array($_POST['resources'])) {
            foreach ($_POST['resources'] as $index => $res) {
                $platform = sanitize($res['platform'] ?? 'other');
                if ($platform === 'server') {
                    $fileKey = "resource_file_{$index}";
                    if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                        $download = uploadFile($_FILES[$fileKey], 'products/files', [
                            'application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-rar-compressed', 'application/gzip',
                            'application/x-tar', 'application/x-7z-compressed', 'application/octet-stream'
                        ]);
                        if ($download) {
                            $finalResources[] = [
                                'platform' => 'server',
                                'url'      => $download
                            ];
                        }
                    } elseif (!empty($res['existing_file'])) {
                        $finalResources[] = [
                            'platform' => 'server',
                            'url'      => sanitize($res['existing_file'])
                        ];
                    }
                } else {
                    $url = sanitize($res['url'] ?? '');
                    if (!empty($url)) {
                        $finalResources[] = [
                            'platform' => $platform,
                            'url'      => $url
                        ];
                    }
                }
            }
        }
        $data['download_file'] = !empty($finalResources) ? json_encode($finalResources) : null;

        // Garbage collection of old server files that are deleted or switched
        if ($existing['download_file']) {
            $oldFiles = [];
            $decodedOld = json_decode($existing['download_file'], true);
            if (is_array($decodedOld)) {
                foreach ($decodedOld as $item) {
                    if (is_array($item) && isset($item['url']) && $item['platform'] === 'server') {
                        $oldFiles[] = $item['url'];
                    } elseif (is_string($item) && strpos($item, 'http') === false) {
                        $oldFiles[] = $item;
                    }
                }
            } else {
                $lines = array_filter(explode("\n", str_replace("\r", "", $existing['download_file'])));
                foreach ($lines as $line) {
                    if (strpos($line, 'http') === false) $oldFiles[] = $line;
                }
            }

            $newFiles = [];
            foreach ($finalResources as $res) {
                if ($res['platform'] === 'server') {
                    $newFiles[] = $res['url'];
                }
            }

            foreach ($oldFiles as $oldFile) {
                if (!in_array($oldFile, $newFiles)) {
                    deleteFile($oldFile);
                }
            }
        }

        try {
            $productModel->update($productId, $data);

            // Trigger new_product_published automation if product transitioned to published
            if ($data['status'] === 'published' && $existing['status'] !== 'published') {
                MarketingSubscriber::triggerAutomation('new_product_published', [
                    'product' => array_merge(['id' => $productId], $data)
                ]);
            }
        } catch (\Exception $e) {
            setFlash('danger', 'Database Update Error: ' . $e->getMessage());
            redirect(url('admin/products/edit/' . $productId));
        }

        // Additional screenshots
        if (!empty($_FILES['screenshots']['name'][0])) {
            $existingImages = $productModel->getImages($productId);
            $sortStart = count($existingImages);
            foreach ($_FILES['screenshots']['name'] as $i => $name) {
                if ($_FILES['screenshots']['error'][$i] !== UPLOAD_ERR_OK) continue;
                $file = [
                    'name'     => $_FILES['screenshots']['name'][$i],
                    'type'     => $_FILES['screenshots']['type'][$i],
                    'tmp_name' => $_FILES['screenshots']['tmp_name'][$i],
                    'error'    => $_FILES['screenshots']['error'][$i],
                    'size'     => $_FILES['screenshots']['size'][$i]
                ];
                $path = uploadFile($file, 'products/screenshots');
                if ($path) {
                    $productModel->addImage($productId, $path, $sortStart + $i);
                }
            }
        }

        // Delete selected images
        if (!empty($_POST['delete_images'])) {
            foreach ($_POST['delete_images'] as $imgId) {
                $productModel->deleteImage((int)$imgId);
            }
        }

        // Update features
        if (isset($_POST['features'])) {
            $features = is_array($_POST['features']) ? $_POST['features'] : explode("\n", $_POST['features']);
            $productModel->syncFeatures($productId, $features);
        }

        setFlash('success', 'Product updated successfully.');
        redirect(url('admin/products/edit/' . $productId));
    }

    public function delete(string $id): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/products'));
        }
        $productModel = new Product();
        $productModel->delete((int)$id);
        setFlash('success', 'Product deleted.');
        redirect(url('admin/products'));
    }
}
