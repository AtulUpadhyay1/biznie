<?php

namespace App\Livewire\Admin\Seller;

use App\Models\User;
use App\Models\GstType;
use App\Models\Business;
use Livewire\Component;
use App\Models\SellerType;
use App\Models\UserDetail;
use App\Models\BusinessType;
use App\Models\SellerKycDetail;
use App\Models\BusinessCategory;
use App\Models\UserPromotionHistory;
use Illuminate\Validation\Rule;

class Create extends Component
{
    public $page_title = 'Create Seller';

    public $user_name;
    public $email;
    public $phone;
    public $password;
    public $company_name;
    public $about;
    public $category = [];
    public $type = [];
    public $seller_type = [];
    public $pan_number;
    public $gst_type;
    public $gst_number;
    public $address;
    public $address_line_one;
    public $address_line_two;
    public $pin_code;
    public $city;
    public $state;
    public $country = 'India';
    public $credit_duration;
    public $credit_duration_day;

    public function mount()
    {
        $this->authorize('seller-list');
    }

    public function render()
    {
        $business_categories = BusinessCategory::active()->orderBy('name')->get();
        $business_types = BusinessType::active()->orderBy('name')->get();
        $seller_types = SellerType::active()->orderBy('name')->get();
        $gst_types = GstType::active()->orderBy('name')->get();

        return view('admin.seller.form', compact(
            'business_categories',
            'business_types',
            'seller_types',
            'gst_types'
        ));
    }

    public function save()
    {
        $existingUser = User::where('phone', $this->phone)->first();

        $this->validate([
            'user_name'     => 'required',
            'company_name'  => 'required',
            'gst_number'    => 'required',
            'phone'         => [
                'required',
                'numeric',
                'digits:10',
                function ($attribute, $value, $fail) {
                    $user = User::where('phone', $value)->first();
                    if ($user && $user->type === 'seller') {
                        $fail('This phone number is already registered as a seller.');
                    }
                },
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($existingUser?->id),
            ],
            'password' => 'nullable|string|min:8',
        ]);

        if ($existingUser) {
            $user = $existingUser;
            $oldType = $user->type ?? 'customer';
        } else {
            $user = new User;
            $user->phone = $this->phone;
            if ($this->password) {
                $user->password = $this->password;
            }
            $oldType = 'customer';
        }

        $user->name = $this->user_name;
        $user->email = $this->email;
        $user->type = 'seller';
        $user->save();

        $user_log_history = new UserPromotionHistory;
        $user_log_history->user_id = $user->id;
        $user_log_history->old_type = $oldType;
        $user_log_history->new_type = 'seller';
        $user_log_history->save();

        $business = Business::where('user_id', $user->id)->first();
        if (! $business) {
            $business = new Business;
            $business->user_id = $user->id;
        }
        $business->name = $this->company_name;
        $business->about = $this->about;
        $business->category = $this->category ?: null;
        $business->type = $this->type ?: null;
        $business->seller_type = $this->seller_type ?: null;
        $business->save();

        $data = SellerKycDetail::where('user_id', $user->id)->first();
        if (! $data) {
            $data = new SellerKycDetail;
            $data->user_id = $user->id;
        }
        $data->identity_type = 'pan';
        $data->identity_number = $this->pan_number;
        $data->gst_type = $this->gst_type;
        $data->gst_number = $this->gst_number;
        $data->address = $this->address;
        $data->address_line_one = $this->address_line_one;
        $data->address_line_two = $this->address_line_two;
        $data->postal_code = $this->pin_code;
        $data->city = $this->city;
        $data->state = $this->state;
        $data->country = $this->country;
        $data->save();

        $user_detail = UserDetail::where('user_id', $user->id)->first();
        if (! $user_detail) {
            $user_detail = new UserDetail;
            $user_detail->user_id = $user->id;
        }
        $user_detail->credit_duration = $this->credit_duration;
        $user_detail->credit_duration_day = $this->credit_duration_day;
        $user_detail->save();

        session()->flash('success', 'Seller created successfully.');
        return $this->redirectRoute('admin.seller.index', navigate: true);
    }
}
