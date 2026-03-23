<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Send contact form submission email.
     */
    public function send(Request $request)
    {
        // Simple honeypot check
        if ($request->filled('honeypot')) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message.'
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        try {
            // Send email to the application from address (as configured in .env)
            Mail::to(config('mail.from.address'))->send(new ContactMail($validated));

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message. We will get back to you soon!'
            ]);

        } catch (\Exception $e) {
            Log::error('Contact Form Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send your message. Please try again later.'
            ], 500);
        }
    }
}
