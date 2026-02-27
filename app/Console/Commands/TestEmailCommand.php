<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\DemoRequestReceived;
use App\Models\User;
use App\Models\DemoRequest;

class TestEmailCommand extends Command
{
    protected $signature = 'test:email {email?}';
    protected $description = 'Test email configuration by sending a test email';

    public function handle()
    {
        $email = $this->argument('email') ?? 'rogerponce761@gmail.com';
        
        $this->info("Testing email configuration...");
        $this->info("Sending test email to: {$email}");
        $this->info("SMTP Settings:");
        $this->line("  Host: " . config('mail.mailers.smtp.host'));
        $this->line("  Port: " . config('mail.mailers.smtp.port'));
        $this->line("  Encryption: " . config('mail.mailers.smtp.encryption'));
        $this->line("  Username: " . config('mail.mailers.smtp.username'));
        $this->line("");
        
        try {
            // Create a test user and demo request
            $testUser = new User([
                'name' => 'Test User',
                'email' => $email,
                'phone' => '77991640',
                'company_name' => 'Test Company',
                'role' => 'forwarder',
            ]);
            
            $testDemoRequest = new DemoRequest([
                'additional_info' => 'This is a test email',
                'status' => 'pending',
                'requested_at' => now(),
            ]);
            
            Mail::to($email)->send(new DemoRequestReceived($testUser, $testDemoRequest));
            
            $this->info("✓ Email sent successfully!");
            $this->info("Check inbox at: {$email}");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("✗ Failed to send email");
            $this->error("Error: " . $e->getMessage());
            
            if ($this->option('verbose')) {
                $this->error($e->getTraceAsString());
            }
            
            return 1;
        }
    }
}
