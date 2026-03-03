<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nombre')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
            <div class="form-text text-muted mt-1 text-gray-600 hover:text-gray-900 text-sm">Ingresa tu nombre completo.
            </div>
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <div class="form-text text-muted mt-1 text-gray-600 hover:text-gray-900 text-sm">
                Debe ser un correo válido y único. Ejemplo: usuario@email.com
            </div>
        </div>

        <!-- Phone Number -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Número de Teléfono')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')"
                required autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            <div class="form-text text-muted mt-1 text-gray-600 hover:text-gray-900 text-sm">
                Ingresa tu número de teléfono con código de país.
            </div>
        </div>

        <!-- Role Selection -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Tipo de cuenta')" />
            <div class="relative">
                <select id="role" name="role" class="form-select mt-1" required>
                    <option value="">Seleccione un tipo de cuenta</option>
                    <option value="{{ App\Models\User::ROLE_FORWARDER }}"
                        {{ old('role') == App\Models\User::ROLE_FORWARDER ? 'selected' : '' }}>FFD (Freight Forwarder)</option>
                    <option value="{{ App\Models\User::ROLE_CARRIER }}"
                        {{ old('role') == App\Models\User::ROLE_CARRIER ? 'selected' : '' }}>TC (Trucking Company)</option>
                </select>
                <style>
                    select#role {
                        color: #1f2937 !important;
                    }

                    select#role option {
                        background-color: white;
                        color: #1f2937;
                        padding: 0.5rem;
                    }
                </style>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
            <div class="form-text text-muted mt-1 text-gray-600 hover:text-gray-900 text-sm">
                Elige el tipo de cuenta que mejor se adapte a tu perfil.
            </div>
        </div>

        <!-- Company Information -->
        <div id="companyInformation">
            <div class="mt-4 p-4 border border-gray-300 rounded-lg bg-gray-50">
                <h3 class="text-lg font-semibold mb-3 text-gray-800">Company Information</h3>
                
                <!-- Company Name -->
                <div class="mb-3">
                    <x-input-label for="company_name" :value="__('Company Name')" />
                    <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" 
                        :value="old('company_name')" required autocomplete="organization" />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                    <div class="form-text text-muted mt-1 text-gray-600 hover:text-gray-900 text-sm">
                        Ingresa el nombre oficial de tu compañía.
                    </div>
                </div>

                <!-- Country -->
                <div class="mb-3">
                    <x-input-label for="country" :value="__('Country')" />
                    <x-text-input id="country" class="block mt-1 w-full" type="text" name="country" 
                        :value="old('country')" required autocomplete="country-name" />
                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                    <div class="form-text text-muted mt-1 text-gray-600 hover:text-gray-900 text-sm">
                        País donde opera tu compañía.
                    </div>
                </div>

                <!-- City -->
                <div class="mb-3">
                    <x-input-label for="city" :value="__('City')" />
                    <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" 
                        :value="old('city')" required autocomplete="address-level2" />
                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                    <div class="form-text text-muted mt-1 text-gray-600 hover:text-gray-900 text-sm">
                        Ciudad principal de operaciones.
                    </div>
                </div>
            </div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <div class="form-text text-muted mt-1 text-gray-600 hover:text-gray-900 text-sm">
                La contraseña debe tener al menos 8 caracteres.
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            <div class="form-text text-muted mt-1 text-gray-600 hover:text-gray-900 text-sm">
                Repite la contraseña exactamente igual para confirmar.
            </div>
        </div>

        <!-- Términos y Condiciones -->
        <div class="mt-5 px-1">
            <label class="flex items-center gap-3 cursor-pointer select-none group">
                <input type="checkbox" id="terms" name="terms" value="1"
                    class="w-4 h-4 rounded border-gray-500 text-indigo-500 shadow-sm bg-gray-700 focus:ring-indigo-400 cursor-not-allowed opacity-50"
                    {{ old('terms') ? 'checked' : '' }}
                    {{ old('terms') ? '' : 'disabled' }} />
                <span class="text-sm text-gray-300 leading-snug">
                    He leído y acepto los
                    <button type="button" id="open-terms-btn"
                        class="text-indigo-400 hover:text-indigo-200 underline underline-offset-2 font-medium transition-colors">
                        Términos y Condiciones
                    </button>
                </span>
            </label>
            @error('terms')
                <p class="text-sm text-red-400 mt-1 ml-7">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('¿Ya estás registrado?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Registrarse') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Modal Términos y Condiciones (dark, scroll-to-accept) -->
    <div id="terms-modal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.75);padding:1rem;">
        <div style="background:#111827;border-radius:0.5rem;box-shadow:0 25px 50px rgba(0,0,0,0.5);max-width:48rem;width:100%;max-height:85vh;display:flex;flex-direction:column;">

            <!-- Header -->
            <div style="display:flex;align-items:center;justify-content:space-between;padding:1.5rem;border-bottom:1px solid #374151;">
                <div>
                    <h2 style="font-size:1.5rem;font-weight:700;color:#fff;margin:0;">Términos y Condiciones</h2>
                    <p style="font-size:0.875rem;color:#9ca3af;margin:0.25rem 0 0;">Última actualización: 20 de noviembre de 2025</p>
                </div>
                <button type="button" id="close-terms-x" style="color:#9ca3af;background:none;border:none;cursor:pointer;font-size:1.5rem;line-height:1;padding:0.25rem;">✕</button>
            </div>

            <!-- Aviso de scroll -->
            <div id="scroll-warning" style="background:rgba(234,179,8,0.15);border-left:4px solid #eab308;padding:0.75rem 1.5rem;margin:1rem 1.5rem 0;border-radius:0.25rem;">
                <p style="color:#fde68a;font-size:0.875rem;font-weight:500;margin:0;">📜 Por favor desplázate hasta el final para continuar</p>
            </div>

            <!-- Contenido scrolleable -->
            <div id="terms-content" style="flex:1;overflow-y:auto;padding:1.5rem;color:#d1d5db;line-height:1.7;font-size:0.9rem;">
                <p style="margin-bottom:1rem;">Pick &amp; Truck (la "aplicación" propiedad de la empresa <strong style="color:#fff;">Makoto Global Logistics and Trade LLC</strong>) reconoce la importancia de la privacidad y la protección de datos en el entorno digital. Esta Política establece las prácticas aplicables al tratamiento de la información personal recopilada a través de nuestra plataforma. Al utilizarla, usted reconoce y acepta estas disposiciones.</p>

                <h3 style="color:#4ade80;font-size:1rem;font-weight:600;margin:1.25rem 0 0.5rem;">1. Información Recopilada</h3>
                <p>La Empresa podrá recopilar y procesar las siguientes categorías de información:<br><br>
                • Datos de identificación y contacto: nombre completo, razón social, dirección, correo electrónico, número de teléfono.<br>
                • Datos corporativos: licencias, permisos y otra documentación relacionada con la actividad empresarial.<br>
                • Datos financieros: información de facturación y métodos de pago (procesados a través de proveedores autorizados).<br>
                • Datos técnicos: dirección IP, tipo de dispositivo, sistema operativo, navegador, ubicación aproximada, registros de uso.<br>
                • Datos derivados de la interacción: historial de búsqueda, conexiones realizadas, cotizaciones solicitadas o enviadas.</p>

                <h3 style="color:#4ade80;font-size:1rem;font-weight:600;margin:1.25rem 0 0.5rem;">2. Finalidades del Tratamiento</h3>
                <p>Los datos recopilados se tratarán para los siguientes fines:<br><br>
                1. Provisión y mejora continua de los Servicios de la Plataforma.<br>
                2. Facilitación de la comunicación y conexión entre Usuarios.<br>
                3. Gestión de pagos, suscripciones y facturación.<br>
                4. Envío de notificaciones operativas y comunicaciones contractuales.<br>
                5. Cumplimiento de obligaciones legales y regulatorias aplicables en los Estados Unidos, particularmente en el Estado de Florida.<br>
                6. Prevención del fraude, actividades ilícitas o incumplimientos contractuales.</p>

                <h3 style="color:#4ade80;font-size:1rem;font-weight:600;margin:1.25rem 0 0.5rem;">3. Base Legal del Tratamiento</h3>
                <p>El tratamiento de datos personales se fundamenta en:<br><br>
                • La necesidad contractual derivada de la relación entre la Empresa y el Usuario.<br>
                • El consentimiento expreso otorgado por el Usuario al registrarse en la Plataforma.<br>
                • El interés legítimo de la Empresa en garantizar la seguridad e integridad de la Plataforma.</p>

                <h3 style="color:#4ade80;font-size:1rem;font-weight:600;margin:1.25rem 0 0.5rem;">4. Cesión y Transferencia de Datos</h3>
                <p>La Empresa podrá comunicar información personal a:<br><br>
                • Otros Usuarios de la Plataforma, cuando resulte necesario para establecer relaciones comerciales.<br>
                • Proveedores de servicios externos, incluyendo hosting, pasarelas de pago, sistemas de autenticación, seguridad y soporte técnico.<br>
                • Autoridades competentes en cumplimiento de requerimientos legales, regulatorios o judiciales.</p>

                <h3 style="color:#4ade80;font-size:1rem;font-weight:600;margin:1.25rem 0 0.5rem;">5. Cookies y Herramientas de Seguimiento</h3>
                <p>La Plataforma utiliza cookies y tecnologías similares para autenticar usuarios, recordar preferencias y analizar métricas de uso. Los usuarios pueden desactivar las cookies mediante su navegador; sin embargo, algunas funcionalidades pueden verse limitadas.</p>

                <h3 style="color:#4ade80;font-size:1rem;font-weight:600;margin:1.25rem 0 0.5rem;">6. Seguridad de la Información</h3>
                <p>La Empresa aplica medidas técnicas y organizativas de nivel empresarial para proteger los datos contra accesos no autorizados, pérdida, alteración o divulgación indebida. Sin embargo, el Usuario reconoce que ningún sistema de transmisión o almacenamiento electrónico es absolutamente seguro.</p>

                <h3 style="color:#4ade80;font-size:1rem;font-weight:600;margin:1.25rem 0 0.5rem;">7. Retención de Datos</h3>
                <p>La información personal se conservará durante el período estrictamente necesario para cumplir los fines descritos en esta Política, salvo que una disposición legal o contractual requiera un período más prolongado.</p>

                <h3 style="color:#4ade80;font-size:1rem;font-weight:600;margin:1.25rem 0 0.5rem;">8. Derechos del Usuario</h3>
                <p>Los usuarios pueden ejercer los siguientes derechos: acceso a los datos personales, rectificación de información inexacta, solicitud de eliminación, oposición al tratamiento y limitación del uso de datos.<br><br>
                Los residentes de California pueden ejercer los derechos previstos en la California Consumer Privacy Act (CCPA).<br><br>
                Las solicitudes deben enviarse a: <strong style="color:#fff;">soporte@pickntruck.com</strong></p>

                <h3 style="color:#4ade80;font-size:1rem;font-weight:600;margin:1.25rem 0 0.5rem;">9. Menores de Edad</h3>
                <p>La Plataforma no está destinada a menores de 18 años. La Empresa no recopila intencionalmente información personal de menores y, si se detecta, procederá a su eliminación inmediata.</p>

                <h3 style="color:#4ade80;font-size:1rem;font-weight:600;margin:1.25rem 0 0.5rem;">10. Modificaciones</h3>
                <p>La Empresa podrá modificar esta Política en cualquier momento. La versión vigente estará disponible en la Plataforma. El uso continuado de la Plataforma tras la publicación de cambios constituirá aceptación de la nueva Política.</p>

                <h3 style="color:#4ade80;font-size:1rem;font-weight:600;margin:1.25rem 0 0.5rem;">11. Contacto</h3>
                <p>Para ejercer derechos o presentar consultas relacionadas con esta Política:<br>
                <strong style="color:#fff;">soporte@pickntruck.com</strong></p>

                <div style="padding-top:1.5rem;border-top:1px solid #374151;margin-top:1.5rem;text-align:center;">
                    <p style="color:#6b7280;font-size:0.875rem;">© 2026 Pick &amp; Truck — Makoto Global Logistics and Trade LLC</p>
                </div>
            </div>

            <!-- Footer -->
            <div style="padding:1.5rem;border-top:1px solid #374151;display:flex;align-items:center;justify-content:space-between;">
                <button type="button" id="close-terms-footer" style="padding:0.5rem 1rem;color:#9ca3af;background:none;border:none;cursor:pointer;font-size:0.875rem;">
                    Cerrar
                </button>
                <button type="button" id="accept-terms-btn" disabled
                    style="padding:0.5rem 1.5rem;border-radius:0.5rem;font-weight:600;font-size:0.875rem;border:none;cursor:not-allowed;background:#374151;color:#6b7280;transition:background 0.2s,color 0.2s;">
                    He leído y acepto
                </button>
            </div>
        </div>
    </div>

    <script>
    (function () {
        var modal      = document.getElementById('terms-modal');
        var content    = document.getElementById('terms-content');
        var warning    = document.getElementById('scroll-warning');
        var acceptBtn  = document.getElementById('accept-terms-btn');
        var checkbox   = document.getElementById('terms');
        var scrolled   = false;

        function openModal() {
            modal.style.display = 'flex';
            scrolled = false;
            warning.style.display = 'block';
            acceptBtn.disabled = true;
            acceptBtn.style.background = '#374151';
            acceptBtn.style.color = '#6b7280';
            acceptBtn.style.cursor = 'not-allowed';
            content.scrollTop = 0;
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        document.getElementById('open-terms-btn').addEventListener('click', openModal);
        document.getElementById('close-terms-x').addEventListener('click', closeModal);
        document.getElementById('close-terms-footer').addEventListener('click', closeModal);

        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });

        content.addEventListener('scroll', function () {
            if (!scrolled && (content.scrollTop + content.clientHeight >= content.scrollHeight - 50)) {
                scrolled = true;
                warning.style.display = 'none';
                acceptBtn.disabled = false;
                acceptBtn.style.background = '#16a34a';
                acceptBtn.style.color = '#ffffff';
                acceptBtn.style.cursor = 'pointer';
            }
        });

        acceptBtn.addEventListener('click', function () {
            if (scrolled) {
                checkbox.disabled = false;
                checkbox.checked = true;
                checkbox.classList.remove('cursor-not-allowed', 'opacity-50');
                checkbox.classList.add('cursor-pointer');
                closeModal();
            }
        });
    })();
    </script>

</x-guest-layout>

<style>
    body {
        background-color: #1a2b4c !important;
        /* Azul oscuro */
    }

    .bg-gray-100 {
        background-color: #1a2b4c !important;
    }

    .text-gray-600,
    .text-gray-900 {
        color: #f0f0f0 !important;
    }

    /* Aumentar el contraste de los elementos para mejor legibilidad */
    .text-sm {
        color: #f0f0f0 !important;
    }

    /* Mantener el card del formulario con fondo claro para contraste */
    .bg-white {
        background-color: #ffffff !important;
    }

    /* Hacer que los enlaces sean más visibles en el fondo oscuro */
    a {
        color: #4299e1 !important;
    }

    a:hover {
        color: #63b3ed !important;
    }

    /* Asegurar que el texto dentro del formulario sea negro */
    form label {
        color: #000000 !important;
    }

    /* Asegurar que los inputs tengan fondo blanco */
    input[type="email"],
    input[type="password"],
    input[type="text"] {
        background-color: #ffffff !important;
        color: #000000 !important;
    }

    /* Restaurar color de texto en el formulario */
    .w-full.sm\:max-w-md .text-gray-600 {
        color: #4a5568 !important;
    }

    /* Asegurar que los textos en el card sean negros */
    .w-full.sm\:max-w-md label,
    .w-full.sm\:max-w-md span:not(.text-white),
    .w-full.sm\:max-w-md p:not(.text-white) {
        color: #000000 !important;
    }

    /* Forzar colores visibles dentro del modal de T&C */
    #terms-modal, #terms-modal * {
        color: inherit;
    }
    #terms-modal h2 { color: #ffffff !important; }
    #terms-modal h3 { color: #4ade80 !important; }
    #terms-modal p, #terms-modal li, #terms-modal span:not(.terms-badge) { color: #d1d5db !important; }
    #terms-modal strong { color: #ffffff !important; }
    #terms-modal #scroll-warning p { color: #fde68a !important; }
    #terms-modal #close-terms-x, #terms-modal #close-terms-footer { color: #9ca3af !important; }
    #terms-modal label, #terms-modal form label { color: #d1d5db !important; }

    /* Estilo específico para botones de registro */
    .ml-3 button,
    button.ml-3,
    .ml-3 .bg-gray-800,
    .ml-4 button,
    button.ml-4,
    .inline-flex,
    .bg-gray-800,
    .bg-indigo-600,
    button[type="submit"] {
        background-color: #1a2b4c !important;
        color: #ffffff !important;
        border-color: #1a2b4c !important;
        transition: background-color 0.3s ease;
    }

    .ml-3 button:hover,
    button.ml-3:hover,
    .ml-4 button:hover,
    button.ml-4:hover,
    .inline-flex:hover,
    .bg-gray-800:hover,
    .bg-indigo-600:hover,
    button[type="submit"]:hover {
        background-color: #2a3b5c !important;
        color: #ffffff !important;
    }

    /* Estilos para x-primary-button */
    .bg-gray-800,
    .bg-indigo-600 {
        background-color: #1a2b4c !important;
        color: #ffffff !important;
    }

    /* Estilos para el bloque de Company Information */
    #companyInformation {
        transition: all 0.3s ease-in-out;
    }

    #companyInformation .bg-gray-50 {
        background-color: #f9fafb !important;
    }

    #companyInformation h3 {
        color: #1f2937 !important;
    }
</style>

<script>
    // Company Information is now always visible for both roles
    // No need for toggle logic
</script>
