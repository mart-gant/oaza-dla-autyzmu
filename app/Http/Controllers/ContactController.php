<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class ContactController extends Controller
{
    public function showForm()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $recipient = config('mail.contact.to.address');

        try {
            Mail::to($recipient)->send(new ContactMail($validated));
        } catch (\Throwable $exception) {
            Log::error('Contact form email delivery failed', [
                'recipient' => $recipient,
                'error' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['message' => 'Nie udało się wysłać wiadomości. Spróbuj ponownie później.']);
        }

        return redirect()->route('contact')->with('success', 'Wiadomość została wysłana!');
    }
}
