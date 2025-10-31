// Funciones para manejar los selectores de ubicación en cascada
class LocationSelector {
    constructor(prefix) {
        this.prefix = prefix;
        this.countrySelect = document.getElementById(`${prefix}_pais`);
        this.stateSelect = document.getElementById(`${prefix}_departamento`);
        this.citySelect = document.getElementById(`${prefix}_ciudad`);
        this.hiddenInput = document.getElementById(prefix);
        
        this.init();
    }
    
    init() {
        // Poblar países
        this.populateCountries();
        
        // Event listeners
        this.countrySelect.addEventListener('change', () => this.onCountryChange());
        this.stateSelect.addEventListener('change', () => this.onStateChange());
        this.citySelect.addEventListener('change', () => this.updateHiddenInput());
    }
    
    populateCountries() {
        this.countrySelect.innerHTML = '<option value="">País</option>';
        Object.keys(locationData).forEach(country => {
            const option = document.createElement('option');
            option.value = country;
            option.textContent = country;
            this.countrySelect.appendChild(option);
        });
    }
    
    onCountryChange() {
        const country = this.countrySelect.value;
        
        // Limpiar selectores dependientes
        this.stateSelect.innerHTML = '<option value="">Departamento/Región</option>';
        this.citySelect.innerHTML = '<option value="">Ciudad</option>';
        this.stateSelect.disabled = !country;
        this.citySelect.disabled = true;
        this.updateHiddenInput();
        
        if (!country) return;
        
        // Poblar departamentos/regiones
        const states = locationData[country];
        Object.keys(states).forEach(state => {
            const option = document.createElement('option');
            option.value = state;
            option.textContent = state;
            this.stateSelect.appendChild(option);
        });
    }
    
    onStateChange() {
        const country = this.countrySelect.value;
        const state = this.stateSelect.value;
        
        // Limpiar selector de ciudades
        this.citySelect.innerHTML = '<option value="">Ciudad</option>';
        this.citySelect.disabled = !state;
        this.updateHiddenInput();
        
        if (!country || !state) return;
        
        // Poblar ciudades
        const cities = locationData[country][state];
        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            this.citySelect.appendChild(option);
        });
    }
    
    updateHiddenInput() {
        const country = this.countrySelect.value;
        const state = this.stateSelect.value;
        const city = this.citySelect.value;
        
        let location = '';
        if (city) {
            location = `${city}, ${state}, ${country}`;
        } else if (state) {
            location = `${state}, ${country}`;
        } else if (country) {
            location = country;
        }
        
        this.hiddenInput.value = location;
    }
}

// Inicializar los selectores cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si existen los elementos necesarios
    if (document.getElementById('origen_pais')) {
        new LocationSelector('origen');
    }
    if (document.getElementById('destino_pais')) {
        new LocationSelector('destino');
    }
});
