<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '../paths.php';


if (!isset($NAV_BASE)) {
    $NAV_BASE = '';
}

$cu = current_user($pdo);
$home = public_href('index.php', $NAV_BASE);
$services = public_href('../pages/services.php', $NAV_BASE);
$faq = public_href('../pages/faq.php', $NAV_BASE);
$about = public_href('../pages/about.php', $NAV_BASE);
$cabinetLogin = public_href('cabinet/login.php', $NAV_BASE);
$cabinetIdx = public_href('cabinet/index.php', $NAV_BASE);
$cabinetReg = public_href('cabinet/register.php', $NAV_BASE);
$cabinetOut = public_href('cabinet/logout.php', $NAV_BASE);
$doctorCab = public_href('doctor/index.php', $NAV_BASE);
$adminIn = public_href('admin/index.php', $NAV_BASE);
$adminNotifications = public_href('admin/notifications.php', $NAV_BASE);
$apiRoot = public_href('api/index.php', $NAV_BASE);
?>
<nav class="navbar">
    <div class="container">
        <div class="navbar-brand">
            <a href="<?= h($home) ?>" class="logo">
                <img src="<?= h($ASSETS) ?>images/logo.png" width="44" height="44" alt="" class="logo-img">
                <span class="logo-text">
                    <span class="logo-title">Больница</span>
                    <span class="logo-sub">«В последний путь»</span>
                </span>
            </a>
            <button class="hamburger" id="hamburgerBtn" type="button" aria-label="Меню">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
        <ul class="nav-menu" id="navMenu">
            <li class="nav-dropdown">
                <button type="button" class="nav-dropdown-toggle" id="navHomeBtn" aria-expanded="false" aria-controls="navHomeMenu" aria-haspopup="true">
                    Главная
                    <span class="nav-dropdown-caret" aria-hidden="true">▾</span>
                </button>
                <ul class="nav-dropdown-menu" id="navHomeMenu" role="menu">
                    <li role="none"><a role="menuitem" href="<?= h($home) ?>#home" class="nav-link nav-dropdown-link">К началу</a></li>
                    <li role="none"><a role="menuitem" href="<?= h($services) ?>" class="nav-link nav-dropdown-link">Услуги</a></li>
                    <li role="none"><a role="menuitem" href="<?= h($home) ?>#doctors" class="nav-link nav-dropdown-link">Врачи</a></li>
                    <li role="none"><a role="menuitem" href="<?= h($home) ?>#contact" class="nav-link nav-dropdown-link">Контакты</a></li>
                    <li role="none"><a role="menuitem" href="<?= h($home) ?>#appointment" class="nav-link nav-dropdown-link">Запись на приём</a></li>
                </ul>
            </li>
            <li><a href="<?= h($faq) ?>" class="nav-link">FAQ</a></li>
            <li><a href="<?= h($about) ?>" class="nav-link">О нас</a></li>
            <?php if ($cu): ?>
                <li class="nav-notif-li">
                    <div class="nav-notif-wrap">
                        <button type="button" class="nav-notif-btn" id="navNotifBtn" aria-expanded="false" aria-haspopup="true" aria-controls="navNotifPanel" data-api="<?= h($apiRoot) ?>">
                            <i class="fas fa-bell" aria-hidden="true"></i>
                            <span class="nav-notif-badge" id="navNotifBadge" hidden>0</span>
                        </button>
                        <div class="nav-notif-panel" id="navNotifPanel" role="menu" hidden>
                            <div class="nav-notif-head">
                                <span>Уведомления</span>
                                <button type="button" class="nav-notif-close" id="navNotifClose" aria-label="Закрыть">&times;</button>
                            </div>
                            <div class="nav-notif-list" id="navNotifList">
                                <div class="nav-notif-empty">Загрузка…</div>
                            </div>
                            <div class="nav-notif-foot">
                                <?php if (user_can_access_patient_area($cu)): ?>
                                    <a href="<?= h($cabinetIdx) ?>#cabinet-notifications">Все в кабинете</a>
                                <?php elseif (user_can_access_admin($cu)): ?>
                                    <a href="<?= h($adminNotifications) ?>">Все уведомления</a>
                                <?php else: ?>
                                    <a href="<?= h($doctorCab) ?>">Кабинет врача</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-account">
                    <span class="nav-account-label" title="<?= h($cu['email']) ?>">
                        <i class="fas fa-user-circle" aria-hidden="true"></i>
                        <?= h($cu['full_name'] ?: $cu['email']) ?>
                    </span>
                </li>
                <?php if (($cu['role'] ?? '') === 'doctor'): ?>
                    <li><a href="<?= h($doctorCab) ?>" class="nav-link">Кабинет врача</a></li>
                <?php endif; ?>
                <?php if (user_can_access_patient_area($cu)): ?>
                    <li>
                        <a href="<?= h($cabinetIdx) ?>" class="nav-link nav-link--cabinet" aria-label="Личный кабинет" title="Личный кабинет"><span aria-hidden="true">👤</span></a>
                    </li>
                <?php endif; ?>
                <?php if (user_can_access_admin($cu)): ?>
                    <li><a href="<?= h($adminIn) ?>" class="nav-link">Админ-панель</a></li>
                <?php endif; ?>
                <li><a href="<?= h($cabinetOut) ?>" class="nav-link">Выход</a></li>
            <?php else: ?>
                <li><a href="<?= h($cabinetLogin) ?>" class="nav-link">Вход</a></li>
                <li><a href="<?= h($cabinetReg) ?>" class="nav-link">Регистрация</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>