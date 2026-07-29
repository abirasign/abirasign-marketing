<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'subject' => 'required|in:general,sales,support,hipaa,billing,other',
            'message' => 'required|string|min:10|max:5000',
        ]);

        $to      = config('mail.contact_to', 'hello@abirasign.com');
        $name    = $request->input('name');
        $email   = $request->input('email');
        $phone   = $request->input('phone', '—');
        $company = $request->input('company', '—');
        $subject = $request->input('subject');
        $message = $request->input('message');

        $html = "
            <div style='font-family: sans-serif; max-width: 600px; color: #111827;'>
                <h2 style='color: #0E7490; margin-bottom: 4px;'>New contact form submission</h2>
                <p style='color: #6B7280; font-size: 13px; margin-top: 0;'>Submitted via abirasign.com/contact</p>
                <table style='width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px;'>
                    <tr><td style='padding: 8px 0; color: #6B7280; width: 120px;'>Name</td><td style='padding: 8px 0; font-weight: 600;'>" . e($name) . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Email</td><td style='padding: 8px 0;'><a href='mailto:" . e($email) . "' style='color: #0E7490;'>" . e($email) . "</a></td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Phone</td><td style='padding: 8px 0;'>" . e($phone) . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Company</td><td style='padding: 8px 0;'>" . e($company) . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Subject</td><td style='padding: 8px 0;'>" . e(ucfirst($subject)) . "</td></tr>
                </table>
                <div style='background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 16px; font-size: 14px; line-height: 1.7; color: #374151;'>
                    " . nl2br(e($message)) . "
                </div>
                <p style='font-size: 12px; color: #9CA3AF; margin-top: 24px;'>Reply directly to this email to respond to " . e($name) . ".</p>
            </div>
        ";

        try {
            // Email to AbiraSign
            Mail::html($html, function ($mail) use ($to, $name, $email, $subject) {
                $mail->to($to)
                     ->replyTo($email, $name)
                     ->subject('[AbiraSign Contact] ' . ucfirst($subject) . ' — ' . $name);
            });

            // Confirmation email to sender
            $helloEmail = config('company.hello_email');
            $confirmHtml = "
                <div style='font-family: sans-serif; max-width: 600px; color: #111827;'>
                    <div style='margin-bottom: 24px;'>
                        <span style='font-size: 20px; font-weight: 700; color: #111827;'>Abira<span style='color: #0E7490;'>Sign</span></span>
                    </div>
                    <h2 style='font-size: 20px; font-weight: 700; color: #111827; margin-bottom: 10px;'>Thanks for reaching out, " . e($name) . "</h2>
                    <p style='font-size: 15px; color: #6B7280; line-height: 1.7; margin-bottom: 14px;'>We've received your message and someone from our team will get back to you within one business day.</p>
                    <p style='font-size: 15px; color: #6B7280; line-height: 1.7; margin-bottom: 24px;'>In the meantime, feel free to explore our <a href='https://abirasign.com/pricing' style='color: #0E7490;'>pricing page</a> or learn more about our <a href='https://abirasign.com/#hipaa' style='color: #0E7490;'>HIPAA compliance add-on</a>.</p>
                    <div style='background: #F0FDFA; border: 1px solid #99F6E4; border-radius: 8px; padding: 20px 24px; margin-bottom: 24px;'>
                        <p style='font-size: 14px; color: #134E4A; margin: 0; line-height: 1.65;'><strong>Your message has been received.</strong> We take every inquiry seriously and will respond promptly. If your matter is urgent, you can also reach us directly at <a href='mailto:" . e($helloEmail) . "' style='color: #0E7490;'>" . e($helloEmail) . "</a>.</p>
                    </div>
                    <hr style='border: none; border-top: 1px solid #E5E7EB; margin: 24px 0;'>
                    <p style='font-size: 12px; color: #9CA3AF; line-height: 1.6;'>This is an automated confirmation. Please do not reply to this email — replies are not monitored. To reach us directly, email <a href='mailto:" . e($helloEmail) . "' style='color: #0E7490;'>" . e($helloEmail) . "</a>.</p>
                    <p style='font-size: 12px; color: #9CA3AF;'>© " . date('Y') . " BrightNet Technologies LLC, DBA AbiraSign · Georgia, United States</p>
                </div>
            ";

            Mail::html($confirmHtml, function ($mail) use ($name, $email) {
                $mail->to($email, $name)
                     ->subject('We received your message — AbiraSign');
            });

            \Log::info('Contact form mail sent', ['to' => $to, 'from' => $email]);
        } catch (\Exception $e) {
            \Log::error('Contact form mail failed', ['error' => $e->getMessage()]);
        }

        session(['contact_name' => $name]);
        return redirect()->route('contact.thankyou');
    }

    public function thankYou()
    {
        if (!session('contact_name')) {
            return redirect()->route('contact');
        }
        return view('contact-thank-you');
    }
}
