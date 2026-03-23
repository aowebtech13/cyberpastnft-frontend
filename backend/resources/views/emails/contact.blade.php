<x-mail::message>
# New Contact Form Submission

You have received a new message from your website contact form.

**Name:** {{ $name }}  
**Email:** {{ $email }}  
**Phone:** {{ $phone }}  
**Subject:** {{ $subject }}

**Message:**  
{{ $messageBody }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
