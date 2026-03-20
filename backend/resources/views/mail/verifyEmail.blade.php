@extends('mail.layout')

@section('content')
<p>Thank you for registering! Please verify your email address by clicking the link below.</p>
<p>This link expires in 60 minutes.</p>
<p><a href="{{ $verificationUrl }}">Verify Email Address</a></p>
<p>If you did not create an account, no further action is required.</p>
@endsection
