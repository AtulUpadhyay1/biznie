<?php

namespace App\Livewire\Admin\WebsiteSetup;

use App\Models\WebsiteSetup;
use App\Rules\Gstin;
use App\Rules\IndianMobile;
use App\Support\CompanyProfile;
use Livewire\Component;

class General extends Component
{
    public $page_title = "General Setup";
    public $value = [];

    /** Every key this screen edits. The legal block feeds the footer, About, Terms and Privacy (4.2). */
    private const KEYS = [
        'phone1', 'phone2', 'whatsapp', 'email', 'short_about', 'address',
        'facebook', 'twitter', 'instagram', 'youtube',
        'legal_name', 'company_llpin', 'company_gstin', 'jurisdiction', 'booking_policy',
        'grievance_officer_name', 'grievance_officer_email', 'grievance_officer_phone', 'grievance_response_time',
    ];

    /**
     * Loaded once. This used to reload every value from the database on each
     * render, so a Livewire round trip could put back what was just typed over.
     */
    public function mount(): void
    {
        foreach (self::KEYS as $key) {
            $this->value[$key] = (string) websiteSetupValue($key);
        }
    }

    public function render()
    {
        return view('admin.website_setup.general');
    }

    protected function rules(): array
    {
        return [
            'value.email'                   => ['nullable', 'email'],
            'value.phone1'                  => ['nullable', new IndianMobile],
            'value.whatsapp'                => ['nullable', new IndianMobile],
            'value.legal_name'              => ['nullable', 'string', 'max:150'],
            // LLPIN: three letters, a hyphen, four digits (e.g. AAB-1234).
            'value.company_llpin'           => ['nullable', 'regex:/^[A-Z]{3}-\d{4}$/'],
            'value.company_gstin'           => ['nullable', new Gstin],
            'value.jurisdiction'            => ['nullable', 'string', 'max:150'],
            'value.booking_policy'          => ['nullable', 'string', 'max:600'],
            'value.grievance_officer_name'  => ['nullable', 'string', 'max:100'],
            'value.grievance_officer_email' => ['nullable', 'email'],
            'value.grievance_officer_phone' => ['nullable', new IndianMobile],
            'value.grievance_response_time' => ['nullable', 'string', 'max:60'],
        ];
    }

    protected function messages(): array
    {
        return [
            'value.company_llpin.regex' => 'LLPIN looks like AAB-1234',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'value.email'                   => 'email',
            'value.grievance_officer_email' => 'grievance officer email',
            'value.grievance_officer_name'  => 'grievance officer name',
            'value.legal_name'              => 'registered name',
        ];
    }

    public function update()
    {
        foreach (['company_llpin', 'company_gstin'] as $key) {
            $this->value[$key] = strtoupper(trim((string) ($this->value[$key] ?? '')));
        }

        $this->validate();

        try {
            foreach (self::KEYS as $key) {
                WebsiteSetup::updateOrCreate(
                    ['key' => $key],
                    ['key' => $key, 'value' => trim((string) ($this->value[$key] ?? ''))],
                );
            }
            CompanyProfile::flush();

            $this->dispatch('alert',
                type: 'success',
                message: 'Settings have been updated successfully.'
            );
        } catch (\Throwable $th) {
            report($th);
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }
}
