<?php

namespace App\Livewire\Marketplace;

use App\Mail\ContactMessage;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Contact Customer Support & Hubs — Parts & Parcel')]
class Contact extends Component
{
    use WithFileUploads;

    public string $topic = 'Order & Delivery';
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $reference_id = '';
    public string $subject = '';
    public string $message = '';
    public $attachment = null;

    public bool $submitted = false;
    public bool $isRegistered = false;

    public const TOPICS = [
        'Order & Delivery',
        'Escrow & Payment',
        'Seller / Subscription',
        'Dispute / Return',
        'General Inquiry',
    ];

    public function mount(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->name = $user->name ?? '';
            $this->email = $user->email ?? '';
            $this->phone = $user->phone ?? '';
        }

        if (request()->has('topic') && in_array(request()->query('topic'), self::TOPICS, true)) {
            $this->topic = (string) request()->query('topic');
        }

        if (request()->has('ref')) {
            $this->reference_id = (string) request()->query('ref');
        }
    }

    public function setTopic(string $topic): void
    {
        if (in_array($topic, self::TOPICS, true)) {
            $this->topic = $topic;
        }
    }

    public function resetSubmission(): void
    {
        $this->submitted = false;
        $this->isRegistered = false;
    }

    protected function rules(): array
    {
        return [
            'topic' => ['required', 'string', 'in:' . implode(',', self::TOPICS)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'reference_id' => ['nullable', 'string', 'max:100'],
            'subject' => ['required', 'string', 'min:3', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf'],
        ];
    }

    public function sendMessage(): void
    {
        $this->validate();

        $normalizedEmail = strtolower(trim($this->email));

        // Check if the user exists in users table
        $existingUser = User::where('email', $normalizedEmail)->first()
            ?? (Auth::check() && strtolower(Auth::user()->email) === $normalizedEmail ? Auth::user() : null);

        $this->isRegistered = (bool) $existingUser;

        $contactData = [
            'topic' => $this->topic,
            'name' => trim($this->name),
            'email' => $normalizedEmail,
            'phone' => trim($this->phone),
            'reference_id' => trim($this->reference_id),
            'subject' => trim($this->subject),
            'message' => trim($this->message),
            'is_registered_user' => (bool) $existingUser,
        ];

        // 1. Immediately forward the message to support email [support@partsandparcel.com]
        $supportEmail = 'support@partsandparcel.com';
        try {
            Mail::to($supportEmail)->send(new ContactMessage($contactData));
        } catch (\Throwable $e) {
            Log::warning("Failed to forward contact message to {$supportEmail}: " . $e->getMessage());
        }

        // 2. If email exists in user table, create conversation message & participant
        if ($existingUser) {
            $this->createSupportConversationThread($existingUser, $contactData);
        }

        $this->submitted = true;

        session()->flash('status', 'Your message has been sent successfully.');

        // Reset form inputs except identity
        $this->reset(['subject', 'message', 'reference_id', 'attachment']);
    }

    protected function createSupportConversationThread(User $user, array $contactData): void
    {
        try {
            // Pick an admin user that has support role as participant
            $supportAdmin = User::whereHas('role', function ($q) {
                $q->where('slug', 'customer_support')
                    ->orWhereJsonContains('permissions->manage_support', true);
            })->where('email', 'support@partsandparcel.com')->first()
            ?? User::whereHas('role', function ($q) {
                $q->where('slug', 'customer_support')
                    ->orWhereJsonContains('permissions->manage_support', true);
            })->first()
            ?? User::getSupportUser();

            // Create a support conversation with null contextable fields
            $conversation = Conversation::create([
                'contextable_type' => null,
                'contextable_id' => null,
                'created_by' => $user->id,
            ]);

            // Ensure participants: the registered user and the support admin
            ConversationParticipant::firstOrCreate(
                ['conversation_id' => $conversation->id, 'user_id' => $user->id],
                ['joined_at' => now()]
            );

            ConversationParticipant::firstOrCreate(
                ['conversation_id' => $conversation->id, 'user_id' => $supportAdmin->id],
                ['joined_at' => now()]
            );

            // Format body with contact metadata
            $bodyLines = [
                "📌 [Contact Ticket: {$contactData['topic']}] {$contactData['subject']}",
            ];
            if (! empty($contactData['reference_id'])) {
                $bodyLines[] = "Reference #: {$contactData['reference_id']}";
            }
            if (! empty($contactData['phone'])) {
                $bodyLines[] = "Phone: {$contactData['phone']}";
            }
            $bodyLines[] = "";
            $bodyLines[] = $contactData['message'];

            $messageBody = implode("\n", $bodyLines);

            $attachmentPath = null;
            if ($this->attachment) {
                try {
                    $attachmentPath = $this->attachment->store('support_attachments', 'public');
                } catch (\Throwable $e) {
                    Log::warning("Failed to store support attachment: " . $e->getMessage());
                }
            }

            ConversationMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'body' => $messageBody,
                'attachments' => $attachmentPath ? json_encode([$attachmentPath]) : null,
                'read_at' => null,
            ]);

            $conversation->touch();
        } catch (\Throwable $e) {
            Log::warning("Failed to record support conversation thread for user {$user->id}: " . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.marketplace.contact', [
            'topics' => self::TOPICS,
        ]);
    }
}
