<div wire:poll.keep-alive>
    <!-- Notification Modal -->
    <div class="modal fade" id="notificationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header with Icon -->
                <div class="modal-header border-0 text-center d-flex flex-column align-items-center">
                    <div class="notification-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px;">
                        <i class="bi bi-bell"></i> <!-- Bootstrap Icons -->
                    </div>
                    <h5 class="modal-title mt-3" id="notificationModalLabel">New Enquiry</h5>
                </div>

                <!-- Modal Body -->
                <div class="modal-body text-center">
                    <p>You have {{ $notification_count }} enquiry. Please check it now.</p>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer d-flex justify-content-center border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="markAsRead()">Dismiss</button>
                <a href="{{ route('admin.commodity-product-enquiry.index') }}" class="btn btn-primary">View Enquiry</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        var notification_count = "{{ $notification_count }}";
        function checkNotification() {
            @this.updateCount();
            if (notification_count > 0) {
                $('#notificationModal').modal('show');
            }
        }
        setInterval(checkNotification, 1000);
    </script>

    {{-- <audio id="notificationSound" src="{{ asset('admin_css/notification.wav') }}" preload="auto"></audio>

    <script>
        window.onload = function() {
            // Existing notification dropdown logic
            document.getElementById('notificationDropdown').addEventListener('click', function() {
                document.getElementById('notificationSound').play();
            });

            // Add event listener to play sound when modal is shown
            $('#notificationModal').on('shown.bs.modal', function () {
                // Play sound after interaction
                if (document.getElementById('notificationSound').paused) {
                    document.getElementById('notificationSound').play().catch(function(error) {
                        console.log('Sound play failed: ', error);
                    });
                }
            });
        };

        var $notification_count = "{{ $notification_count }}";

        function checkNotification() {
            @this.updateCount();
            if ($notification_count > 0) {
                $('#notificationModal').modal('show');
            }
        }

        setInterval(checkNotification, 1000);
    </script> --}}


</div>
