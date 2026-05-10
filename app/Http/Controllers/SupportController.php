<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    public function show()
    {
        return view('support');
    }

    public function kb()
    {
        return view('support-kb');
    }

    public function request()
    {
        return view('support-request');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'organization' => 'nullable|string|max:255',
            'plan'         => 'required|in:payg,starter,professional,enterprise,not_customer',
            'topic'        => 'required|in:billing,account_users,sending_documents,forms_templates,kiosk,hipaa_baa,technical_bug,other',
            'subject'      => 'required|string|max:255',
            'browser'      => 'nullable|string|max:255',
            'message'      => 'required|string|min:10|max:10000',
            'no_phi'       => 'accepted',
            'screenshot'   => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:5120',
        ]);

        $to           = config('mail.support_to', 'choskins@abirasign.com');
        $name         = $request->input('name');
        $email        = $request->input('email');
        $organization = $request->input('organization', '—');
        $plan         = $request->input('plan');
        $topic        = $request->input('topic');
        $subject      = $request->input('subject');
        $browser      = $request->input('browser', '—');
        $message      = $request->input('message');

        $planLabels = [
            'payg'         => 'Pay As You Go',
            'starter'      => 'Starter',
            'professional' => 'Professional',
            'enterprise'   => 'Enterprise',
            'not_customer' => 'Not yet a customer',
        ];

        $topicLabels = [
            'billing'           => 'Billing & subscription',
            'account_users'     => 'Account & users',
            'sending_documents' => 'Sending documents',
            'forms_templates'   => 'Forms & templates',
            'kiosk'             => 'Kiosk mode',
            'hipaa_baa'         => 'HIPAA & BAA',
            'technical_bug'     => 'Technical issue / bug',
            'other'             => 'Other',
        ];

        $planLabel  = $planLabels[$plan]   ?? ucfirst($plan);
        $topicLabel = $topicLabels[$topic] ?? ucfirst($topic);

        // Handle screenshot upload
        $attachmentPath = null;
        $attachmentName = null;
        if ($request->hasFile('screenshot') && $request->file('screenshot')->isValid()) {
            $file           = $request->file('screenshot');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = $file->store('support-uploads', 'local');
            $attachmentPath = storage_path('app/' . $attachmentPath);
        }

        $html = "
            <div style='font-family: sans-serif; max-width: 600px; color: #111827;'>
                <h2 style='color: #0E7490; margin-bottom: 4px;'>New support request</h2>
                <p style='color: #6B7280; font-size: 13px; margin-top: 0;'>Submitted via abirasign.com/support</p>
                <table style='width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px;'>
                    <tr><td style='padding: 8px 0; color: #6B7280; width: 140px;'>Name</td><td style='padding: 8px 0; font-weight: 600;'>" . e($name) . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Email</td><td style='padding: 8px 0;'><a href='mailto:" . e($email) . "' style='color: #0E7490;'>" . e($email) . "</a></td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Organization</td><td style='padding: 8px 0;'>" . e($organization) . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Plan</td><td style='padding: 8px 0;'>" . e($planLabel) . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Topic</td><td style='padding: 8px 0;'>" . e($topicLabel) . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Subject</td><td style='padding: 8px 0; font-weight: 600;'>" . e($subject) . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Browser / OS</td><td style='padding: 8px 0;'>" . e($browser) . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Screenshot</td><td style='padding: 8px 0;'>" . ($attachmentName ? e($attachmentName) . ' (attached)' : '—') . "</td></tr>
                </table>
                <div style='background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 16px; font-size: 14px; line-height: 1.7; color: #374151;'>
                    " . nl2br(e($message)) . "
                </div>
                <p style='font-size: 12px; color: #9CA3AF; margin-top: 24px;'>Reply directly to this email to respond to " . e($name) . ".</p>
            </div>
        ";

        try {
            Mail::html($html, function ($mail) use ($to, $name, $email, $topicLabel, $subject, $attachmentPath, $attachmentName) {
                $m = $mail->to($to)
                          ->replyTo($email, $name)
                          ->subject('[AbiraSign Support] ' . $topicLabel . ' — ' . $subject);
                if ($attachmentPath && file_exists($attachmentPath)) {
                    $m->attach($attachmentPath, ['as' => $attachmentName]);
                }
            });

            // Confirmation to submitter
            $confirmHtml = "
                <div style='font-family: sans-serif; max-width: 600px; color: #111827;'>
                    <div style='margin-bottom: 24px;'>
                        <span style='font-size: 20px; font-weight: 700; color: #111827;'>Abira<span style='color: #0E7490;'>Sign</span></span>
                    </div>
                    <h2 style='font-size: 20px; font-weight: 700; color: #111827; margin-bottom: 10px;'>We've received your support request, " . e($name) . "</h2>
                    <p style='font-size: 15px; color: #6B7280; line-height: 1.7; margin-bottom: 14px;'>Our team will review your request and get back to you within one business day.</p>
                    <div style='background: #F0FDFA; border: 1px solid #99F6E4; border-radius: 8px; padding: 20px 24px; margin-bottom: 24px; font-size: 14px; color: #134E4A; line-height: 1.65;'>
                        <strong>Topic:</strong> " . e($topicLabel) . "<br>
                        <strong>Subject:</strong> " . e($subject) . "
                    </div>
                    <hr style='border: none; border-top: 1px solid #E5E7EB; margin: 24px 0;'>
                    <p style='font-size: 12px; color: #9CA3AF; line-height: 1.6;'>Please do not reply to this email — replies are not monitored.</p>
                    <p style='font-size: 12px; color: #9CA3AF;'>© " . date('Y') . " BrightNet Technologies LLC, DBA AbiraSign · Georgia, United States</p>
                </div>
            ";

            Mail::html($confirmHtml, function ($mail) use ($name, $email) {
                $mail->to($email, $name)->subject('Support request received — AbiraSign');
            });

            // Clean up temp file
            if ($attachmentPath && file_exists($attachmentPath)) {
                @unlink($attachmentPath);
            }

            \Log::info('Support form submitted', ['to' => $to, 'from' => $email, 'topic' => $topicLabel]);
        } catch (\Exception $e) {
            \Log::error('Support form mail failed', ['error' => $e->getMessage()]);
        }

        session(['support_name' => $name]);
        return redirect()->route('support.thankyou');
    }

    public function thankYou()
    {
        if (!session('support_name')) {
            return redirect()->route('support');
        }
        return view('support-thank-you');
    }
}
