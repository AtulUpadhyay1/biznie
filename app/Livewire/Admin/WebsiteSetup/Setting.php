<?php

namespace App\Livewire\Admin\WebsiteSetup;

use Livewire\Component;
use App\Models\WebsiteSetup;

class Setting extends Component
{
    public $page_title = 'Setting';
    public $value = [];

    public function render()
    {
        $this->value['enquiry_send_to_seller'] = websiteSetupValue('enquiry_send_to_seller');
        $this->value['seller_enquiry_reply_time'] = websiteSetupValue('seller_enquiry_reply_time');
        $this->value['minimum_balance_for_enquiry'] = websiteSetupValue('minimum_balance_for_enquiry');
        $this->value['order_token_amount'] = websiteSetupValue('order_token_amount');
        $this->value['customer_quality_check_visibility'] = websiteSetupValue('customer_quality_check_visibility');
        $this->value['enquiry_send_to_transporter'] = websiteSetupValue('enquiry_send_to_transporter');
        $this->value['master_otp'] = websiteSetupValue('master_otp');
        return view('admin.website_setup.setting');
    }

    public function updateSetting()
    {
        // dd($this->value);
        try {
            foreach ($this->value as $key => $value) {
                WebsiteSetup::updateOrCreate(
                    ["key"      => $key],
                    [
                        "key"   => $key,
                        "value" => $this->value[$key],
                    ],
                );
            }

            $this->dispatch('alert',
                type: 'success',
                message: 'Setting has been updated successfully.'
            );

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }
}
