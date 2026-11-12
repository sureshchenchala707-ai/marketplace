@extends('layouts.app')

@section('title', 'My Notifications | ESS MARKETPLACE')

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Notifications</h3>
            <p class="text-muted mb-0">Stay updated on your marketplace activity.</p>
        </div>

        <button type="button" id="markAllReadPage" class="btn btn-outline-primary rounded-pill">
            <i class="fas fa-check-double me-2"></i>Mark all as read
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">

            @forelse($notifications as $notification)
                <div class="notification-row d-flex align-items-start gap-3 p-3 border-bottom
                    {{ $notification->read_at ? '' : 'notification-unread' }}"
                    id="notification-{{ $notification->id }}">

                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                    </div>

                    <div class="flex-grow-1 min-width-0">
                        <div class="fw-bold">
                            {{ $notification->data['title'] ?? 'Marketplace Notification' }}
                        </div>

                        <p class="text-muted small mb-1">
                            {{ $notification->data['message'] ?? '' }}
                        </p>

                        <small class="text-muted">
                            {{ $notification->created_at->diffForHumans() }}
                        </small>

                        @if(!$notification->read_at)
                            <div class="mt-2">
                                <button type="button"
                                        class="btn btn-sm btn-link p-0 mark-one-read"
                                        data-id="{{ $notification->id }}">
                                    Mark as read
                                </button>
                            </div>
                        @endif
                    </div>

                    <button type="button"
                            class="btn btn-sm btn-light text-danger delete-notification"
                            data-id="{{ $notification->id }}"
                            title="Delete notification">
                        <i class="fas fa-trash"></i>
                    </button>

                </div>
            @empty
                <div class="text-center py-5">
                    <i class="far fa-bell-slash fa-3x text-muted mb-3"></i>
                    <h5 class="fw-bold">No notifications yet</h5>
                    <p class="text-muted mb-0">Your updates will appear here.</p>
                </div>
            @endforelse

        </div>
    </div>

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>

</div>

<style>
    .notification-row { transition: background .2s ease; }
    .notification-unread { background: #f4f1ff; }
    .notification-icon {
        width: 42px;
        height: 42px;
        flex-shrink: 0;
        border-radius: 13px;
        display: grid;
        place-items: center;
        color: #7657ff;
        background: #ece8ff;
    }
    .min-width-0 { min-width: 0; }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const readUrl = @json(route('notifications.read', ['id' => '__ID__']));
    const deleteUrl = @json(route('notifications.destroy', ['id' => '__ID__']));
    const markAllUrl = @json(route('notifications.readAll'));

    function send(url, method) {
        return fetch(url, {
            method: method,
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            }
        }).then(response => {
            if (!response.ok) throw new Error('Request failed');
            return response.json();
        });
    }

    document.querySelectorAll('.mark-one-read').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;

            send(readUrl.replace('__ID__', encodeURIComponent(id)), 'PATCH')
                .then(() => window.location.reload())
                .catch(() => alert('Could not mark notification as read.'));
        });
    });

    document.querySelectorAll('.delete-notification').forEach(button => {
        button.addEventListener('click', function () {
            if (!confirm('Delete this notification?')) return;

            const id = this.dataset.id;

            send(deleteUrl.replace('__ID__', encodeURIComponent(id)), 'DELETE')
                .then(() => {
                    document.getElementById('notification-' + id)?.remove();
                })
                .catch(() => alert('Could not delete notification.'));
        });
    });

    document.getElementById('markAllReadPage')?.addEventListener('click', function () {
        send(markAllUrl, 'PATCH')
            .then(() => window.location.reload())
            .catch(() => alert('Could not mark notifications as read.'));
    });
});
</script>
@endpush