<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Membership;
use App\Mail\MembershipCreated;
use App\Mail\SupportNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendMembershipEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public Membership $membership,
        public array $notificationData
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Send email to user
            Mail::to($this->user->email)->send(new MembershipCreated($this->user, $this->membership));
            Log::info('Membership email sent to user: ' . $this->user->email);
        } catch (\Exception $e) {
            Log::error('Failed to send membership email to user', [
                'email' => $this->user->email,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            // Send notification to support
            Mail::to(config('mail.from.address', 'soporte@pickntruck.com'))->send(
                new SupportNotification('Nuevo Registro de Membresía', $this->notificationData)
            );
            Log::info('Membership notification sent to support');
        } catch (\Exception $e) {
            Log::error('Failed to send membership notification to support', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
