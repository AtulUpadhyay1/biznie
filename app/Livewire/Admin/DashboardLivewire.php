<?php

namespace App\Livewire\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Brand;
use Livewire\Component;
use App\Mail\EnquiryMail;
use Livewire\WithPagination;
use App\Models\GeneralEnquiry;
use App\Models\ProductEnquiry;
use App\Models\CommodityProduct;
use App\Models\SellerOnboardingDetail;
use Illuminate\Support\Facades\Mail;
use App\Models\CommodityProductOrder;

class DashboardLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search, $status;
    public $rejectionReasons = [];
    public $pendingRejectionUserId = null;

    protected $queryString = [
        'search'        => ['except' => ''],
    ];

    // public function mount()
    // {
    //     $this->authorize('dashboard');
    // }

    public function render()
    {
        $total_customer = User::where('type', 'customer')->count();
        $total_seller = User::where('type', 'seller')->count();
        $total_transporter = User::where('type', 'transporter')->count();
        $total_brand = Brand::count();
        $total_enquiry = ProductEnquiry::count();
        $total_order = CommodityProductOrder::count();
        $total_commodity_product = CommodityProduct::where('status', 'active')->count();

        $today = today();
        $today_enquiry = ProductEnquiry::whereDate('created_at', $today)->count();
        $today_order = CommodityProductOrder::whereDate('created_at', $today)->count();
        $today_order_amount = CommodityProductOrder::whereDate('created_at', $today)->sum('total_amount');

        // "Tell us your requirement" leads from the website. The pending count is
        // what makes a new one visible without opening the list.
        $today_general_enquiry = GeneralEnquiry::whereDate('created_at', $today)->count();
        $pending_general_enquiry = GeneralEnquiry::where('status', 'pending')->count();

        // Buyers waiting to be promoted to sellers.
        $pending_seller_request = SellerOnboardingDetail::where('request_status', 'pending_review')->count();

        $last_7_days_customer = [];
        $last_7_days_seller = [];

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');

            $customer_count = User::where('type', 'customer')->whereDate('created_at', $date)->count();
            $seller_count = User::where('type', 'seller')->whereDate('created_at', $date)->count();

            $last_7_days_customer[] = ['date' => $date, 'count' => $customer_count];
            $last_7_days_seller[] = ['date' => $date, 'count' => $seller_count];
        }

        $customer_list = User::search($this->search)
            ->where('type', 'customer')
            ->whereIn('kyc_status', ['pending', 'rejected'])
            ->latest()
            ->with(['getUserDetail'])
            ->where('is_staff', 0)
            ->paginate(getPaginate(5));

        return view('admin.dashboard', compact(
            'total_customer',
            'total_seller',
            'total_transporter',
            'total_brand',
            'total_enquiry',
            'total_order',
            'last_7_days_customer',
            'last_7_days_seller',
            'today_enquiry',
            'today_order',
            'today_order_amount',
            'total_commodity_product',
            'today_general_enquiry',
            'pending_general_enquiry',
            'pending_seller_request',
            'customer_list'
        ), ['page_title' => 'Admin Dashboard']);
    }

    public function notificationTest()
    {
        Mail::to('techuptechnologies1@gmail.com')->send(new EnquiryMail());
        $this->dispatch(
            'alert',
            type: 'success',
            message: 'Notification sent successfully.'
        );
    }

    public function changeKycStatus($user_id, $status)
    {
        // If status is rejected, store the user ID and don't save yet
        if ($status == 'rejected') {
            $this->pendingRejectionUserId = $user_id;
            $this->dispatch('showRejectionModal', userId: $user_id);
            return;
        }

        $user = User::find($user_id);
        if ($user) {
            $user->kyc_status = $status;
            if ($status == 'approved') {
                $user->kyc_verified_at = now();
            } else {
                $user->kyc_verified_at = null;
            }
            $user->save();

            $this->dispatch(
                'alert',
                type: 'success',
                message: 'KYC status updated successfully.'
            );
        } else {
            $this->dispatch(
                'alert',
                type: 'error',
                message: 'User not found.'
            );
        }
    }

    public function submitRejection($user_id)
    {
        $rejectionReason = $this->rejectionReasons[$user_id] ?? null;

        if (empty($rejectionReason)) {
            $this->dispatch(
                'alert',
                type: 'error',
                message: 'Please provide a rejection reason.'
            );
            return;
        }

        $user = User::find($user_id);
        if ($user) {
            $user->kyc_status = 'rejected';
            $user->kyc_verified_at = null;
            $user->kyc_description = $rejectionReason;
            $user->save();

            // Clear the rejection reason after saving
            unset($this->rejectionReasons[$user_id]);
            $this->pendingRejectionUserId = null;

            $this->dispatch(
                'alert',
                type: 'success',
                message: 'KYC status rejected with reason.'
            );
        } else {
            $this->dispatch(
                'alert',
                type: 'error',
                message: 'User not found.'
            );
        }
    }

    public function cancelRejection()
    {
        $this->pendingRejectionUserId = null;
        // Reset select dropdown to previous value
        $this->dispatch('resetSelectValue');
    }
}
