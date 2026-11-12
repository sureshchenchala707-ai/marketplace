@auth
    <div class="dropdown ess-notification-dropdown">
        <button
            class="btn ess-notification-button position-relative"
            type="button"
            id="notificationDropdown"
            data-bs-toggle="dropdown"
            data-bs-auto-close="outside"
            aria-expanded="false"
            aria-label="Notifications"
            title="Notifications">

            <i class="fa-regular fa-bell"></i>

            <span
                id="notificationUnreadBadge"
                class="ess-notification-badge"
                style="{{ auth()->user()->unreadNotifications()->count() > 0 ? '' : 'display:none' }}">
                {{ auth()->user()->unreadNotifications()->count() }}
            </span>
        </button>

        <div class="dropdown-menu dropdown-menu-end ess-notification-menu"
             aria-labelledby="notificationDropdown">

            <div class="d-flex align-items-center justify-content-between px-3 py-3 border-bottom">
                <div>
                    <strong>Notifications</strong>
                    <div class="small text-muted" id="notificationUnreadLabel">
                        {{ auth()->user()->unreadNotifications()->count() }} unread
                    </div>
                </div>

                <button type="button"
                        class="btn btn-sm btn-link text-decoration-none"
                        id="markAllNotificationsRead">
                    Mark all read
                </button>
            </div>

            <div id="notificationDropdownList">
                <div class="text-center text-muted py-4">
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Loading notifications...
                </div>
            </div>

            <a href="{{ route('notifications.index') }}"
               class="d-block text-center py-3 border-top text-decoration-none small fw-semibold">
                View all notifications
            </a>
        </div>
    </div>

   <style>

/* Notification dropdown wrapper */
.ess-notification-dropdown {
    position: relative !important;
    z-index: 2000 !important;
}

/* Keep notification dropdown above other page elements */
.ess-notification-dropdown:has(.ess-notification-menu.show) {
    z-index: 2000 !important;
}

/* Notification button */
.ess-notification-button {
    position: relative;
    width: 42px;
    height: 42px;
    border: none;
    border-radius: 50%;
    background: #f3f2fb;
    color: #24213d;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    cursor: pointer;
}

.ess-notification-button:hover {
    background: #7657ff;
    color: white;
}

/* Notification badge */
.ess-notification-badge {
    position: absolute;
    top: -3px;
    right: -4px;
    min-width: 19px;
    height: 19px;
    padding: 0 5px;
    border: 2px solid white;
    border-radius: 20px;
    background: #e63946;
    color: white;
    font-size: 10px;
    line-height: 15px;
    font-weight: 800;
    text-align: center;
    z-index: 3;
}

/* Main notification panel */
.ess-notification-menu {
    width: 380px !important;
    max-width: calc(100vw - 24px);
    padding: 0 !important;
    margin-top: 12px !important;

    background: #ffffff !important;
    border: 1px solid #e5e2f0 !important;
    border-radius: 16px !important;

    box-shadow: 0 18px 55px rgba(25, 20, 65, .25) !important;

    overflow: hidden;
    z-index: 2001 !important;
}

/* Notification header */
.ess-notification-menu > div:first-child {
    background: #ffffff !important;
    padding: 16px !important;
}

/* Notification content */
#notificationDropdownList {
    max-height: 350px;
    overflow-y: auto;
    background: #ffffff !important;
}

.ess-notification-item {
    display: block;
    padding: 15px 18px;
    color: #24213d !important;
    text-decoration: none !important;
    background: #ffffff !important;
    border-bottom: 1px solid #eeedf4;
    white-space: normal;
}

.ess-notification-item:hover {
    background: #f5f2ff !important;
}

.ess-notification-item.unread {
    background: #f0ebff !important;
    border-left: 4px solid #7657ff;
}

.ess-notification-item strong {
    display: block;
    color: #211d38 !important;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 5px;
}

.ess-notification-item p {
    margin: 0 0 6px;
    color: #68677c !important;
    font-size: 12px;
    line-height: 1.6;
}

.ess-notification-item small {
    color: #9290a3 !important;
    font-size: 10px;
}

.ess-notification-empty {
    padding: 30px 15px;
    text-align: center;
    color: #85859a;
    background: #ffffff;
}

/* Footer */
.ess-notification-menu > a:last-child {
    background: #ffffff;
    color: #7657ff !important;
    font-weight: 600;
}

.ess-notification-menu > a:last-child:hover {
    background: #f5f2ff;
}

</style>
    <script>

        // Real-time notifications through Laravel Reverb

        document.addEventListener('DOMContentLoaded', function () {
            const recentUrl = @json(route('notifications.recent'));
            const markAllUrl = @json(route('notifications.readAll'));
            const readUrlTemplate = @json(route('notifications.read', ['id' => '__ID__']));
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            const badge = document.getElementById('notificationUnreadBadge');
            const unreadLabel = document.getElementById('notificationUnreadLabel');
            const list = document.getElementById('notificationDropdownList');
            const markAllButton = document.getElementById('markAllNotificationsRead');

            if (!badge || !list || !csrfToken) return;

            function updateCount(count) {
                count = Number(count) || 0;
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = count > 0 ? 'inline-block' : 'none';

                if (unreadLabel) {
                    unreadLabel.textContent = count + ' unread';
                }
            }

            function renderNotifications(items) {
                if (!items.length) {
                    list.innerHTML = '<div class="ess-notification-empty">' +
                        '<i class="fa-regular fa-bell-slash fa-2x mb-2"></i>' +
                        '<div>You are all caught up!</div></div>';
                    return;
                }

                list.innerHTML = '';

                items.forEach(function (item) {
                    const link = document.createElement('a');
                    link.className = 'ess-notification-item' + (item.read_at ? '' : ' unread');
                    link.href = item.url || @json(route('notifications.index'));

                    const title = document.createElement('strong');
                    title.textContent = item.title || 'Marketplace Notification';

                    const message = document.createElement('p');
                    message.textContent = item.message || '';

                    const time = document.createElement('small');
                    time.textContent = item.created_at || '';

                    link.append(title, message, time);

                    if (!item.read_at) {
                        link.addEventListener('click', function (event) {
                            event.preventDefault();

                            fetch(readUrlTemplate.replace('__ID__', encodeURIComponent(item.id)), {
                                method: 'PATCH',
                                credentials: 'same-origin',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                }
                            }).then(function (response) {
                                if (!response.ok) throw new Error('Could not mark notification as read.');
                                window.location.href = link.href;
                            }).catch(function () {
                                window.location.href = link.href;
                            });
                        });
                    }

                    list.appendChild(link);
                });
            }

            function loadNotifications() {
                fetch(recentUrl, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' }
                })
                .then(function (response) {
                    if (!response.ok) throw new Error('Unable to load notifications.');
                    return response.json();
                })
                .then(function (data) {
                    updateCount(data.unread_count);
                    renderNotifications(data.notifications || []);
                })
                .catch(function () {
                    list.innerHTML = '<div class="ess-notification-empty">Could not load notifications. Please refresh.</div>';
                });
            }

            // Real-time notifications through Laravel Reverb
            @if(auth()->check())
                if (window.Echo) {
                    window.Echo.private('App.Models.User.{{ auth()->id() }}')
                        .notification(function (notification) {
                            console.log('New real-time notification:', notification);
                            loadNotifications();
                        });
                } else {
                    console.error('Laravel Echo is not initialized.');
                }
            @endif

            markAllButton?.addEventListener('click', function () {
                fetch(markAllUrl, {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(function (response) {
                    if (!response.ok) throw new Error('Could not update notifications.');
                    loadNotifications();
                });
            });

            document.getElementById('notificationDropdown')
                ?.addEventListener('show.bs.dropdown', loadNotifications);

            loadNotifications();
            //setInterval(loadNotifications, 30000);
        });
    </script>
@endauth