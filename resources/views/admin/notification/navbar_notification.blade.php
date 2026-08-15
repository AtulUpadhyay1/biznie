<li class="nav-item dropdown">
    <a class="bz-icon-btn dropdown-toggle" href="#" id="notificationDropdown" title="Notifications"
        role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="bi bi-bell"></i>
        @if ($notifications->count() > 0)
            <span class="bz-notif-dot">{{ $notifications->count() > 9 ? '9+' : $notifications->count() }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end bz-notif-menu" aria-labelledby="notificationDropdown">
        <div class="bz-notif-menu__head">
            <span class="bz-notif-menu__title">
                Notifications
                @if ($notifications->count() > 0)
                    <span class="badge bg-danger">{{ $notifications->count() }}</span>
                @endif
            </span>
            @if ($notifications->count() > 0)
                <button type="button" class="bz-notif-menu__clear" wire:click="markAllAsRead()">Mark all read</button>
            @endif
        </div>

        <div class="bz-notif-menu__list">
            @forelse ($notifications->take(6) as $notification)
                <a href="javascript:;" class="dropdown-item bz-notif-item" wire:click="markAsRead({{ $notification->id }})">
                    <span class="bz-notif-item__icon"><i class="bi bi-bell"></i></span>
                    <span class="bz-notif-item__body">
                        <span class="bz-notif-item__title">{{ $notification->title }}</span>
                        <span class="bz-notif-item__time">{{ $notification->created_at->diffForHumans() }}</span>
                    </span>
                </a>
            @empty
                <div class="bz-notif-empty">
                    <i class="bi bi-bell-slash"></i>
                    <span>You're all caught up</span>
                </div>
            @endforelse
        </div>

        @if ($notifications->count() > 0)
            <audio id="notificationSound" src="{{ asset('admin_css/notification.wav') }}" preload="auto"></audio>
        @endif
    </div>
</li>
