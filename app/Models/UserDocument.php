<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class UserDocument extends Model
{
    protected $fillable = [
        'user_id',
        'required_document_id',
        'file_path',
        'status',
        'comments'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function requiredDocument(): BelongsTo
    {
        return $this->belongsTo(RequiredDocument::class);
    }

    /**
     * Obtiene la URL completa del documento
     * Maneja automáticamente las URLs tanto en producción como en desarrollo local
     *
     * @return string
     */
    public function getDocumentUrlAttribute()
    {
        // Si la ruta ya es una URL completa (http:// o https://), la devolvemos tal cual
        if (filter_var($this->file_path, FILTER_VALIDATE_URL)) {
            return $this->file_path;
        }
        
        // Si la ruta comienza con 'documents/' o 'storage/documents/', asumimos que es una ruta de almacenamiento
        if (str_starts_with($this->file_path, 'documents/') || str_starts_with($this->file_path, 'storage/documents/')) {
            return Storage::url($this->file_path);
        }
        
        // Si la ruta es relativa, asumimos que está en la carpeta documents del usuario
        if (!empty($this->file_path)) {
            return Storage::url('documents/' . $this->user_id . '/' . $this->file_path);
        }
        
        // Si no hay ruta, devolvemos null
        return null;
    }
}
