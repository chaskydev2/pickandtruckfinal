<?php

namespace App\Jobs;

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
        public DemoRequest $demoRequest
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
            // Email al solicitante via SMTP explícito
            $laravelMailer->to($this->demoRequest->email)->send(new DemoRequestReceived($this->demoRequest));
            Log::info('Demo request email sent to: ' . $this->demoRequest->email);
        } catch (\Exception $e) {
            Log::error('Failed to send demo request email to requester', [
                'email' => $this->demoRequest->email,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            // Email a soporte via SMTP explícito
            $laravelMailer->to(config('mail.from.address', 'soporte@pickntruck.com'))
                ->send(new SupportNotification('Nueva Solicitud de Demo', [
                    'Nombre'              => $this->demoRequest->name,
                    'Empresa'             => $this->demoRequest->company_name,
                    'Email'               => $this->demoRequest->email,
                    'Teléfono'            => $this->demoRequest->phone ?? 'N/A',
                    'Tipo'                => $this->demoRequest->company_type === 'forwarder' ? 'Forwarder' : 'Carrier',
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
