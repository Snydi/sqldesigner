@extends('mail.layout')

@section('content')
<p><strong>From:</strong> {{ $senderEmail ?? 'Anonymous' }}</p>
<p><strong>Message:</strong></p>
<p>{{ $body }}</p>
@endsection
