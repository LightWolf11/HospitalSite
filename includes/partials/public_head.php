<?php
declare(strict_types=1);
if (!isset($extraCss)) {
    $extraCss = [];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= h($ASSETS) ?>css/variables.css">
    <link rel="stylesheet" href="<?= h($ASSETS) ?>css/animations.css">
    <link rel="stylesheet" href="<?= h($ASSETS) ?>css/navbar.css">
    <link rel="stylesheet" href="<?= h($ASSETS) ?>css/buttons.css">
    <?php foreach ($extraCss as $css): ?>
    <link rel="stylesheet" href="<?= h($ASSETS) ?>css/<?= h($css) ?>">
    <?php endforeach; ?>
    <link rel="stylesheet" href="<?= h($ASSETS) ?>css/footer.css">
    <link rel="stylesheet" href="<?= h($ASSETS) ?>css/responsive.css">
    <link rel="stylesheet" href="<?= h($ASSETS) ?>css/theme.css">
    <link rel="stylesheet" href="<?= h($ASSETS) ?>css/app-shell.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="<?= h($ASSETS) ?>images/logo.png" type="image/png">
</head>
<body>
