<?php
declare(strict_types=1);
require __DIR__ . '/_auth.php';

$message = '';
$error = '';
$tab = $_GET['tab'] ?? 'feedback';
if ($tab !== 'feedback' && $tab !== 'team') {
    $tab = 'feedback';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'reply_feedback') {
            $id = (int) ($_POST['id'] ?? 0);
            $reply = trim((string) ($_POST['reply'] ?? ''));
            if ($id < 1 || strlen($reply) < 3) {
                throw new RuntimeException('Введите ответ');
            }
            $st = $pdo->prepare('SELECT name, email, message FROM feedback_messages WHERE id = ?');
            $st->execute([$id]);
            $row = $st->fetch();
            if (!$row) {
                throw new RuntimeException('Не найдено');
            }
            $pdo->prepare('UPDATE feedback_messages SET admin_reply = ?, replied_at = NOW(), is_read = 1 WHERE id = ?')->execute([$reply, $id]);
            $html = email_reply_template($row['name'], $reply, $config);
            send_html_mail($config, $row['email'], 'Ответ на ваше обращение — ' . ($config['mail']['from_name'] ?? 'Клиника'), $html);
            $message = 'Ответ отправлен на ' . $row['email'];
        } elseif ($action === 'reply_team') {
            $id = (int) ($_POST['id'] ?? 0);
            $reply = trim((string) ($_POST['reply'] ?? ''));
            if ($id < 1 || strlen($reply) < 3) {
                throw new RuntimeException('Введите ответ');
            }
            $st = $pdo->prepare('SELECT full_name, email FROM team_applications WHERE id = ?');
            $st->execute([$id]);
            $row = $st->fetch();
            if (!$row) {
                throw new RuntimeException('Не найдено');
            }
            $pdo->prepare('UPDATE team_applications SET admin_reply = ?, replied_at = NOW(), is_read = 1, status = ? WHERE id = ?')->execute([$reply, 'answered', $id]);
            $html = email_reply_template($row['full_name'], $reply, $config);
            send_html_mail($config, $row['email'], 'Ответ по анкете в команду — ' . ($config['mail']['from_name'] ?? 'Клиника'), $html);
            $message = 'Ответ отправлен';
        } elseif ($action === 'mark_read_fb' && ($id = (int) ($_POST['id'] ?? 0)) > 0) {
            $pdo->prepare('UPDATE feedback_messages SET is_read = 1 WHERE id = ?')->execute([$id]);
            header('Location: notifications.php?tab=feedback&view=' . $id);
            exit;
        } elseif ($action === 'mark_read_tm' && ($id = (int) ($_POST['id'] ?? 0)) > 0) {
            $pdo->prepare('UPDATE team_applications SET is_read = 1 WHERE id = ?')->execute([$id]);
            header('Location: notifications.php?tab=team&view=' . $id);
            exit;
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$feedbackList = $pdo->query('SELECT * FROM feedback_messages ORDER BY id DESC')->fetchAll();
$teamList = $pdo->query('SELECT * FROM team_applications ORDER BY id DESC')->fetchAll();
$viewId = isset($_GET['view']) ? (int) $_GET['view'] : 0;
$viewFeedback = null;
$viewTeam = null;
if ($viewId > 0 && $tab === 'feedback') {
    $st = $pdo->prepare('SELECT * FROM feedback_messages WHERE id = ?');
    $st->execute([$viewId]);
    $viewFeedback = $st->fetch();
    if ($viewFeedback && empty($viewFeedback['is_read'])) {
        $pdo->prepare('UPDATE feedback_messages SET is_read = 1 WHERE id = ?')->execute([$viewId]);
        $viewFeedback['is_read'] = 1;
    }
}
if ($viewId > 0 && $tab === 'team') {
    $st = $pdo->prepare('SELECT * FROM team_applications WHERE id = ?');
    $st->execute([$viewId]);
    $viewTeam = $st->fetch();
    if ($viewTeam && empty($viewTeam['is_read'])) {
        $pdo->prepare('UPDATE team_applications SET is_read = 1 WHERE id = ?')->execute([$viewId]);
        $viewTeam['is_read'] = 1;
    }
}

$pageTitle = 'Уведомления — админ-панель';
$NAV_BASE = '..';
$ASSETS = '../assets/';
$ADMIN_ACTIVE = 'notifications';
$extraCss = ['appointment.css'];
require dirname(__DIR__) . '/includes/partials/public_head.php';
require dirname(__DIR__) . '/includes/partials/public_nav.php';
require dirname(__DIR__) . '/includes/partials/admin_subnav.php';
?>
    <section class="app-page admin-panel">
        <div class="container" style="max-width: 900px;">
    <?php if ($message): ?><p class="app-msg ok"><?= h($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="app-msg err"><?= h($error) ?></p><?php endif; ?>

    <div class="tabs-admin">
        <a class="<?= $tab === 'feedback' ? 'is-active' : '' ?>" href="notifications.php?tab=feedback"><span class="admin-tag admin-tag--fb">Обратная связь</span> Обращения</a>
        <a class="<?= $tab === 'team' ? 'is-active' : '' ?>" href="notifications.php?tab=team"><span class="admin-tag admin-tag--tm">Анкета</span> В команду</a>
    </div>

    <?php if ($tab === 'feedback'): ?>
        <h2 class="section-title" style="font-size: 1.1rem;">Сообщения обратной связи</h2>
        <table class="admin-data">
            <thead><tr><th>Дата</th><th>Тег</th><th>От кого</th><th>Кратко</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($feedbackList as $r): ?>
                <tr class="<?= empty($r['is_read']) ? 'row-unread' : '' ?>">
                    <td><?= h($r['created_at']) ?></td>
                    <td><span class="admin-tag admin-tag--fb">обратная связь</span></td>
                    <td><?= h($r['name']) ?><br><small><?= h($r['email']) ?></small></td>
                    <td><?php
                        $prev = function_exists('mb_substr') ? mb_substr($r['message'], 0, 80) : substr($r['message'], 0, 80);
                        $more = function_exists('mb_strlen') ? (mb_strlen($r['message']) > 80) : (strlen($r['message']) > 80);
                        echo h($prev) . ($more ? '…' : '');
                    ?></td>
                    <td><a class="row-link" href="notifications.php?tab=feedback&view=<?= (int) $r['id'] ?>">Открыть</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($viewFeedback): ?>
            <div class="admin-detail">
                <p><span class="admin-tag admin-tag--fb">обратная связь</span> <strong><?= h($viewFeedback['name']) ?></strong> &lt;<?= h($viewFeedback['email']) ?>&gt;</p>
                <p style="white-space:pre-wrap;"><?= h($viewFeedback['message']) ?></p>
                <?php if (!empty($viewFeedback['admin_reply'])): ?>
                    <p><strong>Ответ (отправлен <?= h((string) $viewFeedback['replied_at']) ?>):</strong></p>
                    <p style="white-space:pre-wrap;"><?= h($viewFeedback['admin_reply']) ?></p>
                <?php else: ?>
                    <form method="post">
                        <input type="hidden" name="action" value="reply_feedback">
                        <input type="hidden" name="id" value="<?= (int) $viewFeedback['id'] ?>">
                        <label>Текст ответа (будет отправлен на email пользователя)</label>
                        <textarea name="reply" required placeholder="Ваш ответ..."></textarea>
                        <button type="submit" class="btn-admin">Ответить</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <h2 class="section-title" style="font-size: 1.1rem;">Анкеты «в команду»</h2>
        <table class="admin-data">
            <thead><tr><th>Дата</th><th>Тег</th><th>ФИО</th><th>Должность</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($teamList as $r): ?>
                <tr class="<?= empty($r['is_read']) ? 'row-unread' : '' ?>">
                    <td><?= h($r['created_at']) ?></td>
                    <td><span class="admin-tag admin-tag--tm">анкета</span></td>
                    <td><?= h($r['full_name']) ?><br><small><?= h($r['email']) ?></small></td>
                    <td><?= h($r['position']) ?></td>
                    <td><a class="row-link" href="notifications.php?tab=team&view=<?= (int) $r['id'] ?>">Открыть</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($viewTeam): ?>
            <div class="admin-detail">
                <p><span class="admin-tag admin-tag--tm">анкета</span> <strong><?= h($viewTeam['full_name']) ?></strong></p>
                <p>Email: <?= h($viewTeam['email']) ?>, тел: <?= h($viewTeam['phone']) ?></p>
                <p>Должность: <?= h($viewTeam['position']) ?></p>
                <p style="white-space:pre-wrap;"><strong>Опыт:</strong> <?= h($viewTeam['experience']) ?></p>
                <p style="white-space:pre-wrap;"><strong>Сообщение:</strong> <?= h($viewTeam['message']) ?></p>
                <?php if (!empty($viewTeam['cv_path'])): ?>
                    <p><a href="../<?= h($viewTeam['cv_path']) ?>" target="_blank" rel="noopener">Скачать резюме (PDF)</a></p>
                <?php endif; ?>
                <?php if (!empty($viewTeam['admin_reply'])): ?>
                    <p><strong>Ответ (<?= h((string) $viewTeam['replied_at']) ?>):</strong></p>
                    <p style="white-space:pre-wrap;"><?= h($viewTeam['admin_reply']) ?></p>
                <?php else: ?>
                    <form method="post">
                        <input type="hidden" name="action" value="reply_team">
                        <input type="hidden" name="id" value="<?= (int) $viewTeam['id'] ?>">
                        <label>Ответ на email кандидата</label>
                        <textarea name="reply" required></textarea>
                        <button type="submit" class="btn-admin">Ответить</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
        </div>
    </section>
<?php
require dirname(__DIR__) . '/includes/partials/public_footer.php';
?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
