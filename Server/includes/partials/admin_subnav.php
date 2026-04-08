<?php
declare(strict_types=1);

if (!isset($ADMIN_ACTIVE)) {
    $ADMIN_ACTIVE = '';
}
if (!isset($adminNoteBadge)) {
    $adminNoteBadge = 0;
}
?>
<nav class="admin-subnav" aria-label="Админ-меню">
    <div class="container">
        <a href="index.php" class="admin-subnav-link <?= $ADMIN_ACTIVE === 'home' ? 'is-active' : '' ?>">Обзор</a>
        <a href="doctors.php" class="admin-subnav-link <?= $ADMIN_ACTIVE === 'doctors' ? 'is-active' : '' ?>">Врачи</a>
        <a href="services.php" class="admin-subnav-link <?= $ADMIN_ACTIVE === 'services' ? 'is-active' : '' ?>">Услуги</a>
        <a href="notifications.php" class="admin-subnav-link <?= $ADMIN_ACTIVE === 'notifications' ? 'is-active' : '' ?>">Уведомления<?php if ($adminNoteBadge > 0): ?><span class="admin-badge" aria-label="Новых"><?= (int) $adminNoteBadge ?></span><?php endif; ?></a>
        <?php if (function_exists('is_superadmin') && is_superadmin($user ?? null)): ?>
            <a href="users.php" class="admin-subnav-link <?= $ADMIN_ACTIVE === 'users' ? 'is-active' : '' ?>">Пользователи</a>
        <?php endif; ?>
        <span class="admin-subnav-spacer"></span>
        <a href="../index.php" class="admin-subnav-link admin-subnav-link--ghost">На сайт</a>
        <a href="logout.php" class="admin-subnav-link admin-subnav-link--ghost">Выход</a>
    </div>
</nav>
