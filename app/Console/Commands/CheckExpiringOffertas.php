<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OfertaCarga;
use App\Models\OfertaRuta;
use App\Notifications\OfertaExpiringNotification;
use App\Notifications\OfertaExpiredNotification;
use Carbon\Carbon;

class CheckExpiringOffertas extends Command
{
    protected $signature = 'ofertas:check-expiring';
    protected $description = 'Verifica y notifica sobre ofertas que están por vencer o que ya han vencido.';

    public function handle()
    {
        $now = Carbon::now();

        // --- 1. Notificar ofertas que vencerán en 24h ---
        $this->info('Verificando ofertas POR VENCER...');
        $this->checkAndNotifyExpiring($now);

        // --- 2. Notificar ofertas que YA vencieron ---
        $this->info('Verificando ofertas VENCIDAS...');
        $this->checkAndNotifyExpired($now);

        $this->info('Proceso de verificación completado.');
        return 0;
    }

    private function checkAndNotifyExpiring(Carbon $now)
    {
        $expirationThreshold = $now->copy()->addHours(24);

        // Cargas por vencer
        $expiringCargas = OfertaCarga::with('user')
            ->where('fecha_inicio', '>', $now)
            ->where('fecha_inicio', '<=', $expirationThreshold)
            ->whereNull('expiry_notification_sent_at')
            ->whereDoesntHave('bids', fn($q) => $q->whereIn('estado', ['aceptado', 'terminado']))
            ->get();

        foreach ($expiringCargas as $oferta) {
            $oferta->user->notify(new OfertaExpiringNotification($oferta, 'carga'));
            $oferta->update(['expiry_notification_sent_at' => $now]);
            $this->line("Notificación de PRE-vencimiento enviada para OfertaCarga #{$oferta->id}");
        }

        // Rutas por vencer
        $expiringRutas = OfertaRuta::with('user')
            ->where('fecha_inicio', '>', $now)
            ->where('fecha_inicio', '<=', $expirationThreshold)
            ->whereNull('expiry_notification_sent_at')
            ->whereDoesntHave('bids', fn($q) => $q->whereIn('estado', ['aceptado', 'terminado']))
            ->get();

        foreach ($expiringRutas as $oferta) {
            $oferta->user->notify(new OfertaExpiringNotification($oferta, 'ruta'));
            $oferta->update(['expiry_notification_sent_at' => $now]);
            $this->line("Notificación de PRE-vencimiento enviada para OfertaRuta #{$oferta->id}");
        }
    }

    private function checkAndNotifyExpired(Carbon $now)
    {
        // Cargas vencidas
        $expiredCargas = OfertaCarga::with('user')
            ->where('fecha_inicio', '<', $now) // La fecha ya pasó
            ->whereNull('expired_notification_sent_at') // Aún no hemos notificado
            ->whereDoesntHave('bids', fn($q) => $q->whereIn('estado', ['aceptado', 'terminado']))
            ->get();

        foreach ($expiredCargas as $oferta) {
            $oferta->user->notify(new OfertaExpiredNotification($oferta, 'carga'));
            $oferta->update(['expired_notification_sent_at' => $now]);
            $this->line("Notificación de VENCIMIENTO enviada para OfertaCarga #{$oferta->id}");
        }

        // Rutas vencidas
        $expiredRutas = OfertaRuta::with('user')
            ->where('fecha_inicio', '<', $now)
            ->whereNull('expired_notification_sent_at')
            ->whereDoesntHave('bids', fn($q) => $q->whereIn('estado', ['aceptado', 'terminado']))
            ->get();

        foreach ($expiredRutas as $oferta) {
            $oferta->user->notify(new OfertaExpiredNotification($oferta, 'ruta'));
            $oferta->update(['expired_notification_sent_at' => $now]);
            $this->line("Notificación de VENCIMIENTO enviada para OfertaRuta #{$oferta->id}");
        }
    }
}