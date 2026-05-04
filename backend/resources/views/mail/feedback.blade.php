@extends('mail.layout')

@section('preheader', $subject)

@section('content')

<p style="margin:0 0 8px;font-family:Arial,Helvetica,sans-serif;font-size:13px;font-weight:600;color:#1c1c1c;letter-spacing:0.04em;text-transform:uppercase;">
    User Feedback
</p>

<hr style="border:none;border-top:1px solid #eeeeee;margin:0 0 24px;">

@if ($senderEmail)
<table cellpadding="0" cellspacing="0" border="0" role="presentation"
       style="margin-bottom:20px;background:#f7f7f7;border-radius:5px;border-left:3px solid #8f2f2f;width:100%;">
    <tr>
        <td style="padding:12px 16px;">
            <span style="font-family:'Courier New',Courier,monospace;font-size:11px;color:#888888;letter-spacing:0.04em;text-transform:uppercase;">From</span><br>
            <a href="mailto:{{ $senderEmail }}"
               style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#1c1c1c;text-decoration:none;font-weight:600;">
                {{ $senderEmail }}
            </a>
        </td>
    </tr>
</table>
@else
<p style="margin:0 0 20px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#aaaaaa;font-style:italic;">
    Sent anonymously
</p>
@endif

<div style="font-family:Arial,Helvetica,sans-serif;font-size:15px;color:#333333;line-height:1.7;white-space:pre-line;">{{ $body }}</div>

@endsection
