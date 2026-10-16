<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use App\Rules\IndianMobile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            // Mobile is how the team calls back; email is a courtesy. The form
            // marked email optional while this rule required it, so the error
            // only surfaced after submit (B20).
            'email'   => ['nullable', 'email', 'max:160'],
            'phone'   => ['required', 'string', new IndianMobile],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $contact = new ContactUs();
        $contact->name    = $data['name'];
        $contact->email   = $data['email'] ?? null;
        $contact->phone   = $data['phone'];
        $contact->message = $data['message'];
        $contact->save();

        sendAdminNotification(
            'New website query',
            $contact->name.' sent a message from the contact form',
            'contact_us',
            ['id' => $contact->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Thanks! Our team will get back to you shortly.',
        ], 201);
    }
}
