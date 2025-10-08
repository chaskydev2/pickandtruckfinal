@php
  $user     = \Illuminate\Support\Facades\Auth::user();
  $excluded = request()->is('empresa*') || request()->is('profile*');
  $missing  = [];
  if ($user && method_exists($user, 'missingProfileFields')) { $missing = $user->missingProfileFields(); }
  $prettyMissing = array_map(fn($f)=> ucfirst(str_replace('empresa.', '', $f)), $missing);
  $show = $user && !$excluded && !empty($missing);

  $editUrl = \Illuminate\Support\Facades\Route::has('empresa.edit') ? route('empresa.edit')
          : (\Illuminate\Support\Facades\Route::has('empresas.edit') ? route('empresas.edit')
          : (\Illuminate\Support\Facades\Route::has('profile.edit') ? route('profile.edit') : '#'));
@endphp

@if($show)
<div class="position-fixed profile-alert" style="right:16px; bottom:16px; z-index:1080;">
  <div class="card shadow-lg border-0" style="width: 360px; border-radius: 12px;">
    <div class="card-body">
      <div class="d-flex">
        <div class="me-3 d-flex align-items-start">
          <div class="border rounded p-2">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-width="1.5" d="M12 9v3m0 4h.01M12 3l9 4.5v9L12 21 3 16.5v-9L12 3z"></path>
            </svg>
          </div>
        </div>
        <div class="flex-grow-1">
          <h6 class="mb-1">¡Completa tu perfil!</h6>
          <p class="mb-3 text-muted small">Te faltan: {{ implode(', ', $prettyMissing) }}.</p>
          <a href="{{ $editUrl }}" class="btn btn-primary btn-sm">Completar perfil</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endif
