<?php

namespace App\Livewire\Seller;

use Livewire\Component;
use App\Models\Business;
use App\Models\SellerKycDetail;

class KycDetailLivewire extends Component
{
    public $business_section = false;
    public $business_name, $business_about, $selected_category, $selected_type, $selected_seller_type;

    public $address_section = false;
    public $address, $postal_code, $city, $state, $country;

    protected $queryString = [
        'business_section'     => ['except' => ''],
        'address_section'      => ['except' => ''],
    ];

    public function render()
    {
        if ($this->business_section) {
            $this->showBusinessData();
        }elseif($this->address_section){
            $this->showAddressData();
        }else{
            $this->business_section = true;
            $this->showBusinessData();
        }
        return view('seller.kyc_detail', ['page_title' => 'Seller Kyc Detail'])->layout('seller.layouts.app');
    }

    public function showBusinessData()
    {
        $business = Business::where('user_id', auth()->id())->first();
        $this->business_name = $business->name;
        $this->business_about = $business->about;

    }

    public function showAddressData()
    {
        $address_data = SellerKycDetail::where('user_id', auth()->id())->first();
        $this->address = $address_data->address;
        $this->postal_code = $address_data->postal_code;
        $this->city = $address_data->city;
        $this->state = $address_data->state;
        $this->country = $address_data->country;
    }

}
