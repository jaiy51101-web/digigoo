<?php
/**
 * Digiws - Wishlist Controller
 */

class WishlistController {
    
    public function index(): void {
        $wishlistModel = new Wishlist();
        $products = $wishlistModel->getItems();
        
        $pageTitle = 'My Wishlist - ' . APP_NAME;
        
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/wishlist.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function toggle(): void {
        // Must be logged in
        if (!isLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'Please login to use wishlist.'], 401);
        }

        $data = getJsonBody();
        $productId = (int)($data['product_id'] ?? 0);

        if (!$productId) {
            jsonResponse(['success' => false, 'message' => 'Invalid product.'], 400);
        }

        $wishlistModel = new Wishlist();
        $result = $wishlistModel->toggle($productId);
        $count = $wishlistModel->getCount();

        jsonResponse([
            'success' => true, 
            'status'  => $result['status'], 
            'message' => $result['message'],
            'count'   => $count
        ]);
    }
}
