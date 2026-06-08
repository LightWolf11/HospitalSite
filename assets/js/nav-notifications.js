(function () {
    'use strict';

    var btn = document.getElementById('navNotifBtn');
    if (!btn) {
        return;
    }

    var api = btn.getAttribute('data-api') || '';
    var panel = document.getElementById('navNotifPanel');
    var closeBtn = document.getElementById('navNotifClose');
    var listEl = document.getElementById('navNotifList');
    var badge = document.getElementById('navNotifBadge');
    var toastStack = document.getElementById('toastStack');

    var prevUnread = -1;
    var inited = false;
    var pollMs = 45000;

    function esc(s) {
        if (s === undefined || s === null) {
            return '';
        }
        var d = document.createElement('div');
        d.textContent = String(s);
        return d.innerHTML;
    }

    function showToast(title, body) {
        if (!toastStack) {
            return;
        }
        var el = document.createElement('div');
        el.className = 'toast-item';
        el.setAttribute('role', 'status');
        el.innerHTML =
            '<div class="toast-title">' + esc(title) + '</div>' +
            (body ? '<div class="toast-body">' + esc(body) + '</div>' : '');
        toastStack.appendChild(el);
        requestAnimationFrame(function () {
            el.classList.add('is-visible');
        });
        setTimeout(function () {
            el.classList.remove('is-visible');
            setTimeout(function () {
                el.remove();
            }, 320);
        }, 5200);
    }

    function renderList(rows) {
        if (!listEl) {
            return;
        }
        if (!rows || !rows.length) {
            listEl.innerHTML = '<div class="nav-notif-empty">Пока нет уведомлений</div>';
            return;
        }
        listEl.innerHTML = rows
            .slice(0, 12)
            .map(function (n) {
                var unread = !n.read_at;
                var t = (n.created_at || '').replace('T', ' ');
                if (t.length > 16) {
                    t = t.slice(0, 16);
                }
                return (
                    '<button type="button" class="nav-notif-item' +
                    (unread ? ' is-unread' : '') +
                    '" data-id="' +
                    esc(String(n.id)) +
                    '">' +
                    '<span class="nav-notif-item-title">' +
                    esc(n.title || '') +
                    '</span>' +
                    (n.body
                        ? '<span class="nav-notif-item-body">' + esc(n.body) + '</span>'
                        : '') +
                    '<span class="nav-notif-item-time">' +
                    esc(t) +
                    '</span>' +
                    '</button>'
                );
            })
            .join('');

        listEl.querySelectorAll('.nav-notif-item').forEach(function (b) {
            b.addEventListener('click', function () {
                var id = parseInt(b.getAttribute('data-id'), 10);
                if (!id) {
                    return;
                }
                fetch(api, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'notification_read', id: id }),
                }).then(function () {
                    loadNotifs(true);
                });
            });
        });
    }

    function loadNotifs(silent) {
        return fetch(api + '?action=notifications', { credentials: 'same-origin' })
            .then(function (r) {
                return r.json();
            })
            .then(function (data) {
                if (!data.ok) {
                    return;
                }
                var unread = data.unread_count || 0;
                if (badge) {
                    if (unread > 0) {
                        badge.hidden = false;
                        badge.textContent = unread > 99 ? '99+' : String(unread);
                    } else {
                        badge.hidden = true;
                    }
                }
                if (!inited) {
                    inited = true;
                } else if (!silent && unread > prevUnread && data.notifications && data.notifications.length) {
                    var newest = data.notifications[0];
                    if (newest && !newest.read_at) {
                        showToast(newest.title || 'Уведомление', newest.body || '');
                    }
                }
                prevUnread = unread;
                renderList(data.notifications || []);
            })
            .catch(function () {
                if (listEl) {
                    listEl.innerHTML = '<div class="nav-notif-empty">Не удалось загрузить</div>';
                }
            });
    }

    function closePanel() {
        if (panel && !panel.hasAttribute('hidden')) {
            panel.setAttribute('hidden', '');
            btn.setAttribute('aria-expanded', 'false');
        }
    }

    function openPanel() {
        if (!panel) {
            return;
        }
        panel.removeAttribute('hidden');
        btn.setAttribute('aria-expanded', 'true');
        loadNotifs(true);
    }

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        if (!panel) {
            return;
        }
        if (panel.hasAttribute('hidden')) {
            openPanel();
        } else {
            closePanel();
        }
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            closePanel();
        });
    }
    document.addEventListener('click', function (e) {
        if (!panel || panel.hasAttribute('hidden')) {
            return;
        }
        var t = e.target;
        if (btn && btn.contains && btn.contains(t)) {
            return;
        }
        if (panel && panel.contains && panel.contains(t)) {
            return;
        }
        closePanel();
    });
    if (panel) {
        panel.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closePanel();
        }
    });

    loadNotifs(true);
    setInterval(function () {
        loadNotifs(false);
    }, pollMs);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            loadNotifs(false);
        }
    });
})();
