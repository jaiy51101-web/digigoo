<?php
/**
 * Digiws - Home Controller
 */

class HomeController {
    
    public function index(): void {
        $productModel = new Product();
        $categoryModel = new Category();
        $settingModel = new Setting();

        $featured = $productModel->getFeatured(8);
        $productModel->loadFeatures($featured);
        $productModel->loadRatings($featured);
        
        $bestsellers = $productModel->getBestsellers(8);
        $productModel->loadFeatures($bestsellers);
        $productModel->loadRatings($bestsellers);
        
        $newProducts = $productModel->getNew(8);
        $productModel->loadFeatures($newProducts);
        $productModel->loadRatings($newProducts);
        $categories = $categoryModel->getWithProductCount();
        $siteName = $settingModel->get('site_name', 'Digiws');
        
        $pageTitle = 'Digiws - Premium Digital Products & Web Services';
        $metaDesc = 'Buy premium digital products, website templates, PHP scripts, themes and plugins. Professional web development services.';

        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/home.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function about(): void {
        $pageTitle = 'About Us - ' . APP_NAME;
        
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/about-us.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function terms(): void {
        $pageTitle = 'Terms & Conditions - ' . APP_NAME;
        
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/terms.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function refundPolicy(): void {
        $pageTitle = 'Refund Policy - ' . APP_NAME;
        
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/refund-policy.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function disclaimer(): void {
        $pageTitle = 'Disclaimer - ' . APP_NAME;
        
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/disclaimer.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function contact(): void {
        $pageTitle = 'Contact Us - ' . APP_NAME;
        $settingModel = new Setting();
        $siteEmail = $settingModel->get('site_email', env('SMTP_FROM_EMAIL', ''));
        $siteAddress = $settingModel->get('site_address', '');
        $whatsappNumber = $settingModel->get('whatsapp_number', env('WHATSAPP_NUMBER', ''));
        
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/contact.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function services(): void {
        $pageTitle = 'Our Services - ' . APP_NAME;
        
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/services.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function contactSubmit(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('contact'));
        }

        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $subject = sanitize($_POST['subject'] ?? '');
        $message = sanitize($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            setFlash('danger', 'Please fill in all required fields.');
            redirect(url('contact'));
        }

        Database::getInstance()->insert('contact_messages', [
            'name'    => $name,
            'email'   => $email,
            'subject' => $subject,
            'message' => $message
        ]);

        setFlash('success', 'Your message has been sent successfully! We will get back to you soon.');
        redirect(url('contact'));
    }

    public function page(string $slug): void {
        $page = Database::getInstance()->fetch(
            "SELECT * FROM pages WHERE slug = ? AND status = 'active'",
            [$slug]
        );

        if (!$page) {
            http_response_code(404);
            include VIEWS_PATH . '/404.php';
            return;
        }

        $pageTitle = ($page['meta_title'] ?: $page['title']) . ' - ' . APP_NAME;
        $metaDesc = $page['meta_description'] ?? '';
        $pageContent = $page['content'];

        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/page.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function newsletterSubscribe(): void {
        $email = sanitize($_POST['email'] ?? '');
        $source = sanitize($_POST['source'] ?? 'footer');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(['success' => false, 'message' => 'Please enter a valid email address.']);
        }

        $subModel = new MarketingSubscriber();
        
        // Prevent duplicate subscriptions by checking the DB before inserting
        $existing = $subModel->findByEmail($email);
        if ($existing && (int)$existing['is_subscriber'] === 1) {
            jsonResponse(['success' => false, 'message' => 'This email address is already subscribed.']);
        }
        
        // Also check the legacy table
        try {
            $db = Database::getInstance();
            $legacy = $db->fetch("SELECT id FROM newsletter_subscribers WHERE email = ? AND status = 'active'", [strtolower(trim($email))]);
            if ($legacy) {
                jsonResponse(['success' => false, 'message' => 'This email address is already subscribed.']);
            }
        } catch (Exception $e) {}

        $subModel->subscribe($email, $source);
        jsonResponse(['success' => true, 'message' => 'Thank you for subscribing!']);
    }

    public function sitemapXml(): void {
        header("Content-Type: application/xml; charset=utf-8");
        
        $db = Database::getInstance();
        
        // Base site URL (using url helper)
        $baseUrl = url('');
        if (substr($baseUrl, -1) !== '/') {
            $baseUrl .= '/';
        }
        
        // Fetch static page endpoints
        $pages = [
            ['loc' => $baseUrl, 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => $baseUrl . 'about', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => $baseUrl . 'contact', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => $baseUrl . 'blog', 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => $baseUrl . 'products', 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => $baseUrl . 'terms-and-conditions', 'changefreq' => 'yearly', 'priority' => '0.5'],
            ['loc' => $baseUrl . 'refund-policy', 'changefreq' => 'yearly', 'priority' => '0.5'],
            ['loc' => $baseUrl . 'disclaimer', 'changefreq' => 'yearly', 'priority' => '0.5']
        ];
        
        // Fetch custom active pages from DB
        try {
            $dbPages = $db->fetchAll("SELECT slug, updated_at FROM pages WHERE status = 'active'");
            foreach ($dbPages as $p) {
                $lastmod = !empty($p['updated_at']) && $p['updated_at'] !== '0000-00-00 00:00:00' ? date('Y-m-d', strtotime($p['updated_at'])) : date('Y-m-d');
                $pages[] = [
                    'loc' => $baseUrl . 'page/' . $p['slug'],
                    'lastmod' => $lastmod,
                    'changefreq' => 'monthly',
                    'priority' => '0.7'
                ];
            }
        } catch (Exception $e) {}

        // Fetch categories from DB
        try {
            $dbCategories = $db->fetchAll("SELECT slug FROM categories WHERE status = 'active'");
            foreach ($dbCategories as $cat) {
                $pages[] = [
                    'loc' => $baseUrl . 'products/category/' . $cat['slug'],
                    'changefreq' => 'weekly',
                    'priority' => '0.8'
                ];
            }
        } catch (Exception $e) {}

        // Fetch blog categories from DB
        try {
            $dbBlogCategories = $db->fetchAll("SELECT slug FROM blog_categories WHERE status = 'active'");
            foreach ($dbBlogCategories as $bcat) {
                $pages[] = [
                    'loc' => $baseUrl . 'blog/category/' . $bcat['slug'],
                    'changefreq' => 'weekly',
                    'priority' => '0.7'
                ];
            }
        } catch (Exception $e) {}
        
        // Fetch active products from DB
        try {
            $dbProducts = $db->fetchAll("SELECT slug, updated_at FROM products WHERE status = 'active'");
            foreach ($dbProducts as $prod) {
                $lastmod = !empty($prod['updated_at']) && $prod['updated_at'] !== '0000-00-00 00:00:00' ? date('Y-m-d', strtotime($prod['updated_at'])) : date('Y-m-d');
                $pages[] = [
                    'loc' => $baseUrl . 'product/' . $prod['slug'],
                    'lastmod' => $lastmod,
                    'changefreq' => 'weekly',
                    'priority' => '0.8'
                ];
            }
        } catch (Exception $e) {}
        
        // Fetch published blog posts from DB
        try {
            $dbPosts = $db->fetchAll("SELECT slug, updated_at FROM posts WHERE status = 'published'");
            foreach ($dbPosts as $post) {
                $lastmod = !empty($post['updated_at']) && $post['updated_at'] !== '0000-00-00 00:00:00' ? date('Y-m-d', strtotime($post['updated_at'])) : date('Y-m-d');
                $pages[] = [
                    'loc' => $baseUrl . 'blog/' . $post['slug'],
                    'lastmod' => $lastmod,
                    'changefreq' => 'weekly',
                    'priority' => '0.8'
                ];
            }
        } catch (Exception $e) {}
        
        // Generate XML output
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($pages as $page) {
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($page['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
            if (isset($page['lastmod'])) {
                echo "    <lastmod>" . $page['lastmod'] . "</lastmod>\n";
            } else {
                echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
            }
            echo "    <changefreq>" . $page['changefreq'] . "</changefreq>\n";
            echo "    <priority>" . $page['priority'] . "</priority>\n";
            echo "  </url>\n";
        }
        
        echo '</urlset>' . "\n";
        exit;
    }

    public function sitemapHtml(): void {
        $db = Database::getInstance();
        
        // Fetch standard pages
        $standardPages = [
            ['title' => 'Home', 'url' => url('')],
            ['title' => 'Products Marketplace', 'url' => url('products')],
            ['title' => 'Blog / Articles', 'url' => url('blog')],
            ['title' => 'About Us', 'url' => url('about')],
            ['title' => 'Contact Us', 'url' => url('contact')],
            ['title' => 'Terms & Conditions', 'url' => url('terms-and-conditions')],
            ['title' => 'Refund Policy', 'url' => url('refund-policy')],
            ['title' => 'Disclaimer', 'url' => url('disclaimer')]
        ];

        // Fetch custom active pages
        $customPages = [];
        try {
            $dbPages = $db->fetchAll("SELECT title, slug FROM pages WHERE status = 'active' ORDER BY title ASC");
            foreach ($dbPages as $p) {
                $customPages[] = [
                    'title' => $p['title'],
                    'url' => url('page/' . $p['slug'])
                ];
            }
        } catch (Exception $e) {}

        // Fetch categories
        $categories = [];
        try {
            $dbCategories = $db->fetchAll("SELECT name, slug FROM categories ORDER BY name ASC");
            foreach ($dbCategories as $cat) {
                $categories[] = [
                    'title' => $cat['name'],
                    'url' => url('products/category/' . $cat['slug'])
                ];
            }
        } catch (Exception $e) {}

        // Fetch blog categories
        $blogCategories = [];
        try {
            $dbBlogCategories = $db->fetchAll("SELECT name, slug FROM blog_categories ORDER BY name ASC");
            foreach ($dbBlogCategories as $bcat) {
                $blogCategories[] = [
                    'title' => $bcat['name'],
                    'url' => url('blog/category/' . $bcat['slug'])
                ];
            }
        } catch (Exception $e) {}

        // Fetch active products
        $products = [];
        try {
            $dbProducts = $db->fetchAll("SELECT title, slug FROM products WHERE status = 'active' ORDER BY created_at DESC");
            foreach ($dbProducts as $prod) {
                $products[] = [
                    'title' => $prod['title'],
                    'url' => url('product/' . $prod['slug'])
                ];
            }
        } catch (Exception $e) {}

        // Fetch published blog posts
        $blogPosts = [];
        try {
            $dbPosts = $db->fetchAll("SELECT title, slug FROM posts WHERE status = 'published' ORDER BY created_at DESC");
            foreach ($dbPosts as $post) {
                $blogPosts[] = [
                    'title' => $post['title'],
                    'url' => url('blog/' . $post['slug'])
                ];
            }
        } catch (Exception $e) {}

        $pageTitle = 'Sitemap - ' . APP_NAME;
        $metaDesc = 'Browse all sections, product downloads, categories, and articles on the ' . APP_NAME . ' sitemap.';

        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/sitemap.php';
        include TEMPLATES_PATH . '/footer.php';
    }
}
