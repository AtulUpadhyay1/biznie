<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown"
        role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="bi bi-bell fs-4"></i>
        @if ($notifications->count() > 0)
            <div class="indicator">
                <div class="circle"></div>
            </div>
        @endif
    </a>
    <div class="dropdown-menu p-0" aria-labelledby="notificationDropdown">
        <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
            <p>{{ $notifications->count() }} New Notifications</p>
            @if($notifications->count() > 0)
                <a href="javascript:;" class="text-muted ms-2" wire:click="markAllAsRead()">Clear all</a>
                <audio id="notificationSound" src="{{ asset('admin_css/notification.wav') }}" preload="auto"></audio>
                <script>
                    window.onload = function() {
                        document.getElementById('notificationDropdown').addEventListener('click', function() {
                            document.getElementById('notificationSound').play();
                        });
                    };
                </script>
            @endif
        </div>
        <div class="p-1">
            @forelse ($notifications->take(6) as $notification)
                <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2" wire:click="markAsRead({{ $notification->id }})">
                    <div class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                        <i class="bi bi-bell icon-sm text-white"></i>
                    </div>
                    <div class="flex-grow-1 me-2">
                        <p>{{ $notification->title }}</p>
                        <p class="tx-12 text-muted">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </a>
            @empty
                <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                    <div class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-secondary rounded-circle me-3">
                        <i class="bi bi-bell-slash icon-sm text-white"></i>
                    </div>
                    <div class="flex-grow-1 me-2">
                        <p>No notifications found</p>
                    </div>
                </a>
            @endforelse
        </div>
        <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
            <a href="javascript:;">View all</a>
        </div>
    </div>
</li>
