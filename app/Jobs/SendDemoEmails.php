<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\DemoRequest;
use App\Mail\DemoRequestReceived;
use App\Mail\SupportNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDemoEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public DemoRequest $demoRequest,
        public string $role
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Configurar transporte SMTP explícito para garantizar entrega a emails externos (Gmail, etc.)
        $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
            'smtp.titan.email',
            587,
            false
        );
        $transport->setUsername(config('mail.from.address', 'soporte@pickntruck.com'));
        $transport->setPassword(env('MAIL_PASSWORD'));

        $laravelMailer = new \Illuminate\Mail\Mailer(
            'smtp',
            app('view'),
            $transport,
            app('events')
        );
        $laravelMailer->alwaysFrom(
            config('mail.from.address', 'soporte@pickntruck.com'),
            config('mail.from.name', 'Pick & Truck')
        );

        try {
            // Email al usuario via SMTP explícito
            $laravelMailer->to($this->user->email)->send(new DemoRequestReceived($this->user, $this->demoRequest));
            Log::info('Demo request email sent to user: ' . $this->user->email);
        } catch (\Exception $e) {
            Log::error('Failed to send demo request email to user', [
                'email' => $this->user->email,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            // Email a soporte via SMTP explícito
            $laravelMailer->to(config('mail.from.address', 'soporte@pickntruck.com'))
                ->send(new SupportNotification('Nueva Solicitud de Demo', [
                    'Nombre' => $this->user->name,
                    'Empresa' => $this->user->company_name,
                    'Email' => $this->user->email,
                    'Teléfono' => $this->user->phone ?? 'N/A',
                    'Tipo' => $this->role === 'forwarder' ? 'Forwarder' : 'Carrier',
                    'Información Adicional' => $this->demoRequest->additional_info ?? 'N/A',
                ]));
            Log::info('Demo request notification sent to support');
        } catch (\Exception $e) {
            Log::error('Failed to send demo request notification to support', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
