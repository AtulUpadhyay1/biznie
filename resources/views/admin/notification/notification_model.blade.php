<div>
    <div wire:poll.5s="fetchNotifications"> <!-- Poll every 5 seconds -->
        <div class="modal fade" id="notificationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="notificationModalLabel" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Modal Header with Icon -->
                    <div class="modal-header border-0 text-center d-flex flex-column align-items-center">
                        <div class="notification-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 60px; height: 60px; font-size: 24px;">
                            <i class="bi bi-bell"></i>
                        </div>
                        <h5 class="modal-title mt-3" id="notificationModalLabel">New Enquiry</h5>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body text-center">
                        <p>You have {{ $notification_count }} new enquiry. Please check it now.</p>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer d-flex justify-content-center border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            wire:click="markAsRead()">Dismiss</button>
                        <a href="{{ route('admin.commodity-product-enquiry.index') }}" class="btn btn-primary">View
                            Enquiry</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Add this script for modal handling -->
    <script>
        window.addEventListener('notification-modal',({detail:{modal}})=>{
            if(modal == true){
                $('#notificationModal').modal('show');
            }else if(modal == false){
                $('#notificationModal').modal('hide');
            }
        })
    </script>
</div>

