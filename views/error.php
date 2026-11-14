<!-- Global Error / 404 Page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $errorCode ?? '404' ?> - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body style="display:flex;align-items:center;justify-content:center;min-height:100vh;background:var(--bg-primary);">
    <div class="text-center p-4">
        <div style="font-size:120px;font-weight:900;line-height:1;background:var(--gradient-primary);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:20px;">
            <?= $errorCode ?? '404' ?>
        </div>
        <h2 style="font-weight:800;color:var(--text-primary);margin-bottom:15px;"><?= $errorMessage ?? 'Oops! Page Not Found' ?></h2>
        <p style="color:var(--text-muted);font-size:1.1rem;margin-bottom:30px;max-width:500px;margin-left:auto;margin-right:auto;">
            The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
        </p>
        <a href="<?= url() ?>" class="btn-dc-primary px-4 py-2" style="border-radius:30px;">
            <i class="bi bi-house-door me-2"></i>Return Home
        </a>
    </div>
</body>
</html>
