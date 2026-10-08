<x-mail::message>
# New Customer Support Message

You have received a new contact inquiry submitted via the Parts & Parcel marketplace website.

<x-mail::panel>
**From:** {{ $contactData['name'] }} ({{ $contactData['email'] }})  
@if (!empty($contactData['phone']))
**Phone / WhatsApp:** {{ $contactData['phone'] }}  
@endif
**Topic:** {{ $contactData['topic'] ?? 'General Inquiry' }}  
@if (!empty($contactData['reference_id']))
**Reference / Invoice / Offer #:** `{{ $contactData['reference_id'] }}`  
@endif
**Subject:** {{ $contactData['subject'] }}  
**Submitted At:** {{ now()->toDayDateTimeString() }}
</x-mail::panel>

### Message Content:
{{ $contactData['message'] }}

---

@if (!empty($contactData['is_registered_user']))
> **Registered Member:** This user's email matches an active account. A ticket conversation thread has been automatically created in their dashboard message center and in the [Admin Support Desk]({{ route('admin.support') }}).
@else
> **Guest Inquiry:** This inquiry was submitted by a guest visitor. You can reply directly to this email to contact the customer.
@endif

Thanks,<br>
{{ config('app.name') }} Automated Support Dispatch
</x-mail::message>
