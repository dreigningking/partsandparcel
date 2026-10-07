<?php

namespace App\Observers;

use App\Models\Moderation;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // For regular marketplace users, initialize a welcome support conversation thread
        if ($user->role_id === null) {
            $this->createWelcomeSupportConversation($user);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Check if govt_id_number or govt_id_expiry was changed
        if ($user->isDirty(['govt_id_number', 'govt_id_expiry'])) {
            $this->createModerationRecord($user, 'updated');
        }
    }

    /**
     * Create a moderation record for the user.
     */
    protected function createModerationRecord(User $user, string $action): void
    {
        Moderation::create([
            'moderatable_type' => User::class,
            'moderatable_id' => $user->id,
            'action' => $action,
            'status' => 'pending',
            'reason' => null,
            'moderated_by' => null,
        ]);
    }

    /**
     * Create an onboarding support conversation between Customer Support and the new user.
     */
    protected function createWelcomeSupportConversation(User $user): void
    {
        try {
            $supportUser = User::getSupportUser();

            // Avoid creating duplicate support conversations
            $morphClass = $user->getMorphClass();
            $existing = \App\Models\Conversation::whereIn('contextable_type', ['user', User::class, $morphClass])
                ->where('contextable_id', $user->id)
                ->first();

            if ($existing) {
                return;
            }

            $conversation = \App\Models\Conversation::create([
                'contextable_type' => $morphClass,
                'contextable_id' => $user->id,
                'created_by' => $supportUser->id,
            ]);

            \App\Models\ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $supportUser->id,
                'joined_at' => now(),
            ]);

            \App\Models\ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'joined_at' => now(),
            ]);

            $welcomeText = "Welcome to Parts & Parcel, {$user->name}! 👋\n\nWe're thrilled to have you on board. Whether you're searching for hard-to-find components, listing your inventory, managing invoices, or tracking escrow orders, our Customer Support team is here for you 24/7.\n\nReply directly to this conversation anytime you need assistance!";

            \App\Models\ConversationMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $supportUser->id,
                'body' => $welcomeText,
                'read_at' => null,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to create welcome support conversation for user {$user->id}: " . $e->getMessage());
        }
    }
}
