<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\RequiredDocument;
use App\Models\UserDocument;

class VerifyUser extends Command
{
    protected $signature = 'user:verify {user_id} {--force}';
    protected $description = 'Verifica un usuario si tiene los documentos requeridos (o fuerza verificación con --force)';

    public function handle(): int
    {
        $user = User::find($this->argument('user_id'));
        if (!$user) {
            $this->error('Usuario no encontrado.');
            return self::FAILURE;
        }

        $required = RequiredDocument::pluck('id')->all(); // asume 2 docs requeridos
        $uploaded = UserDocument::where('user_id', $user->id)
            ->whereIn('required_document_id', $required)
            ->pluck('required_document_id')->unique()->all();

        $missing = array_diff($required, $uploaded);

        if (!empty($missing) && !$this->option('force')) {
            $this->warn('Faltan documentos requeridos. Use --force para verificar de todos modos.');
            return self::INVALID;
        }

        // Aprueba todos los documentos del usuario (o solo pendientes, según tu preferencia)
        UserDocument::where('user_id', $user->id)
            ->where('status', '!=', 'aprobado')
            ->update(['status' => 'aprobado']);

        // Marca verificado
        $user->verified = 1;
        // Si manejas también user_verified, ponlo en 1:
        // $user->user_verified = 1;
        $user->save();

        $this->info("Usuario {$user->id} verificado y documentos aprobados.");
        return self::SUCCESS;
    }
}
