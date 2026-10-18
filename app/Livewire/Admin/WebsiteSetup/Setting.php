<?php

namespace App\Livewire\Admin\WebsiteSetup;

use Livewire\Component;
use App\Models\WebsiteSetup;

class Setting extends Component
{
    public $page_title = 'Setting';
    public $value = [];

    /**
     * Loaded once, not on every render: reloading in render() put the saved
     * values back over what the admin had typed whenever the component
     * re-rendered before the save (and over an invalid value it was about to
     * report).
     */
    public function mount()
    {
        foreach ([
            'enquiry_send_to_seller',
            'seller_enquiry_reply_time',
            'minimum_balance_for_enquiry',
            'order_token_amount',
            'customer_quality_check_visibility',
            'enquiry_send_to_transporter',
            'master_otp',
        ] as $key) {
            $this->value[$key] = websiteSetupValue($key);
        }
    }

    public function render()
    {
        return view('admin.website_setup.setting');
    }

    public function updateSetting()
    {
        // The reply time is the live bidding window for every new RFQ.
        $this->validate(
            ['value.seller_enquiry_reply_time' => ['required', 'integer', 'min:1', 'max:1440']],
            [
                'value.seller_enquiry_reply_time.required' => 'Enter the seller reply time in minutes.',
                'value.seller_enquiry_reply_time.integer'  => 'Enter whole minutes, for example 60.',
                'value.seller_enquiry_reply_time.min'      => 'The seller reply time must be at least 1 minute.',
                'value.seller_enquiry_reply_time.max'      => 'The seller reply time can be at most 1440 minutes (24 hours).',
            ]
        );

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
