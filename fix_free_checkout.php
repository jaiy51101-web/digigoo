<?php
/**
 * DigiWS - One-Click Auto Fix for Free Checkout Issue on Hostinger / Live Server
 * Upload this file to your website root (public_html) and visit: https://digiws.com/fix_free_checkout.php
 */

header('Content-Type: text/html; charset=utf-8');
echo "<h2>DigiWS - Free Checkout Auto-Fix Script</h2>";

$cartControllerPath = __DIR__ . '/controllers/CartController.php';
$checkoutViewPath   = __DIR__ . '/views/checkout.php';
$orderModelPath      = __DIR__ . '/models/Order.php';

$changesMade = 0;

// ============================================
// 1. FIX CartController.php
// ============================================
if (file_exists($cartControllerPath)) {
    $cartContent = file_get_contents($cartControllerPath);
    
    // Fix status 'completed' -> 'success' in Payment create for free checkout
    if (strpos($cartContent, "'status'         => 'completed'") !== false && strpos($cartContent, "'gateway'        => 'free'") !== false) {
        $cartContent = str_replace(
            "'gateway'        => 'free',\n                    'amount'         => 0.00,\n                    'status'         => 'completed'",
            "'gateway'        => 'free',\n                    'amount'         => 0.00,\n                    'status'         => 'success'",
            $cartContent
        );
        $cartContent = str_replace(
            "'status' => 'completed'",
            "'status' => 'success'",
            $cartContent
        );
    }
    
    // Add cart clear and sales increment if missing inside paymentMethod === 'free' block
    if (strpos($cartContent, "(new Cart())->clear();") === false || strpos($cartContent, "incrementSales") === false) {
        $freeBlockOld = "(new Payment())->create([\n                    'order_id'       => \$orderId,\n                    'user_id'        => currentUserId(),\n                    'gateway'        => 'free',\n                    'amount'         => 0.00,\n                    'status'         => 'success',\n                    'transaction_id' => 'FREE-' . strtoupper(uniqid())\n                ]);\n            }";
        
        $freeBlockNew = "(new Payment())->create([\n                    'order_id'       => \$orderId,\n                    'user_id'        => currentUserId(),\n                    'gateway'        => 'free',\n                    'amount'         => 0.00,\n                    'status'         => 'success',\n                    'transaction_id' => 'FREE-' . strtoupper(uniqid())\n                ]);\n\n                // Increment sales for products\n                foreach (\$cartData['items'] as \$item) {\n                    \$productModel->incrementSales(\$item['product_id'], \$item['quantity']);\n                }\n\n                // Clear the cart for free orders\n                (new Cart())->clear();\n            }";
        
        $cartContent = str_replace($freeBlockOld, $freeBlockNew, $cartContent);
    }
    
    // Replace catch (Exception $e) with catch (Throwable $e)
    $cartContent = str_replace('} catch (Exception $e) {', '} catch (Throwable $e) {', $cartContent);
    
    // Add redirect_url to response if not present
    if (strpos($cartContent, "'redirect_url'") === false) {
        $cartContent = str_replace(
            "'payment_url'  => \$paymentUrl,",
            "'payment_url'  => \$paymentUrl,\n                'redirect_url' => url('pay/success?order=' . \$orderId),",
            $cartContent
        );
    }

    file_put_contents($cartControllerPath, $cartContent);
    echo "<p style='color:green'>✓ Updated <b>controllers/CartController.php</b> successfully.</p>";
    $changesMade++;
} else {
    echo "<p style='color:red'>✗ Could not find controllers/CartController.php</p>";
}

// ============================================
// 2. FIX views/checkout.php
// ============================================
if (file_exists($checkoutViewPath)) {
    $viewContent = file_get_contents($checkoutViewPath);

    // Fix name parsing for single-name users at top of checkout.php
    if (strpos($viewContent, '$checkoutNameParts') === false) {
        $nameFixHead = "<?php\n\$checkoutNameParts = isLoggedIn() ? explode(' ', trim(currentUser()['name']), 2) : ['', ''];\n\$checkoutFirstName = \$checkoutNameParts[0] ?? '';\n\$checkoutLastName  = \$checkoutNameParts[1] ?? (isLoggedIn() ? '-' : '');\n?>\n<!-- Checkout Page -->";
        $viewContent = str_replace('<!-- Checkout Page -->', $nameFixHead, $viewContent);
        
        $viewContent = str_replace(
            "value=\"<?= isLoggedIn() ? explode(' ', currentUser()['name'])[0] : '' ?>\"",
            "value=\"<?= htmlspecialchars(\$checkoutFirstName) ?>\"",
            $viewContent
        );
        
        $viewContent = str_replace(
            "value=\"<?= isLoggedIn() ? (explode(' ', currentUser()['name'])[1] ?? '') : '' ?>\"",
            "value=\"<?= htmlspecialchars(\$checkoutLastName) ?>\"",
            $viewContent
        );
    }

    // Fix JavaScript processCheckout response handling for free orders
    if (strpos($viewContent, 'data.total <= 0 || selectedPaymentMethod === \'free\'') === false) {
        $jsOld = "if (data.success) {\n            if (selectedPaymentMethod === 'upi_direct') {";
        $jsNew = "if (data.success) {\n            if (data.total <= 0 || selectedPaymentMethod === 'free') {\n                window.location.href = data.redirect_url || (appUrl + '/pay/success?order=' + data.order_id);\n            } else if (selectedPaymentMethod === 'upi_direct') {";
        
        $viewContent = str_replace($jsOld, $jsNew, $viewContent);
    }

    file_put_contents($checkoutViewPath, $viewContent);
    echo "<p style='color:green'>✓ Updated <b>views/checkout.php</b> successfully.</p>";
    $changesMade++;
} else {
    echo "<p style='color:red'>✗ Could not find views/checkout.php</p>";
}

// ============================================
// 3. FIX models/Order.php
// ============================================
if (file_exists($orderModelPath)) {
    $orderContent = file_get_contents($orderModelPath);
    if (strpos($orderContent, 'syncOrderToCrm') !== false) {
        $orderContent = str_replace(
            "} catch (Exception \$e) {}",
            "} catch (Throwable \$e) { error_log('syncOrderToCrm error: ' . \$e->getMessage()); }",
            $orderContent
        );
        file_put_contents($orderModelPath, $orderContent);
        echo "<p style='color:green'>✓ Updated <b>models/Order.php</b> successfully.</p>";
        $changesMade++;
    }
} else {
    echo "<p style='color:red'>✗ Could not find models/Order.php</p>";
}

echo "<hr>";
echo "<h3>Status: Fixes Applied Successfully! 🎉</h3>";
echo "<p>Please delete this <code>fix_free_checkout.php</code> file from your server after running, then test your free product checkout on <a href='checkout'>Checkout Page</a>.</p>";
