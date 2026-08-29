<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'email'   => ['required', 'email', 'max:160'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $contact = new ContactUs();
        $contact->name    = $data['name'];
        $contact->email   = $data['email'];
        $contact->phone   = $data['phone'] ?? null;
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
