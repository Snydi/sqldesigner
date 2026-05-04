@extends('mail.layout')

@section('preheader', $emailSubject)

@section('content')

<p style="margin:0 0 8px;font-family:Arial,Helvetica,sans-serif;font-size:13px;font-weight:600;color:#1c1c1c;letter-spacing:0.04em;text-transform:uppercase;">
    {{ $emailSubject }}
</p>

<hr style="border:none;border-top:1px solid #eeeeee;margin:0 0 24px;">

<div style="font-family:Arial,Helvetica,sans-serif;font-size:15px;color:#333333;line-height:1.7;white-space:pre-line;">{{ $body }}</div>

<hr style="border:none;border-top:1px solid #eeeeee;margin:28px 0 20px;">

<p style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#888888;line-height:1.5;">
    Open your diagrams at
    <a href="https://sql-designer.com/diagrams" style="color:#2e5c45;text-decoration:none;">sql-designer.com/diagrams</a>.
</p>

@endsection
