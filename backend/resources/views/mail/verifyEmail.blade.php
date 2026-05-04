@extends('mail.layout')

@section('preheader', 'Verify your email address to activate your SQL Designer account.')

@section('content')

<p style="margin:0 0 8px;font-family:Arial,Helvetica,sans-serif;font-size:13px;font-weight:600;color:#1c1c1c;letter-spacing:0.04em;text-transform:uppercase;">
    Confirm your email
</p>

<p style="margin:0 0 24px;font-family:Arial,Helvetica,sans-serif;font-size:15px;color:#444444;line-height:1.6;">
    Thanks for signing up! Click the button below to verify your email address and activate your account.
</p>

<table cellpadding="0" cellspacing="0" border="0" role="presentation" style="margin-bottom:28px;">
    <tr>
        <td style="border-radius:5px;background:#2e5c45;">
            <a href="{{ $verificationUrl }}"
               style="display:inline-block;padding:13px 28px;font-family:Arial,Helvetica,sans-serif;font-size:13px;font-weight:600;color:#ffffff;text-decoration:none;letter-spacing:0.04em;border-radius:5px;">
                Verify Email Address
            </a>
        </td>
    </tr>
</table>

<p style="margin:0 0 6px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#888888;line-height:1.5;">
    This link expires in <strong style="color:#555555;">60 minutes</strong>.
    If you did not create an account, you can safely ignore this email.
</p>

<hr style="border:none;border-top:1px solid #eeeeee;margin:28px 0;">

<p style="margin:0;font-family:'Courier New',Courier,monospace;font-size:11px;color:#bbbbbb;word-break:break-all;line-height:1.6;">
    Or copy this link into your browser:<br>
    <a href="{{ $verificationUrl }}" style="color:#8f2f2f;text-decoration:none;">{{ $verificationUrl }}</a>
</p>

@endsection
