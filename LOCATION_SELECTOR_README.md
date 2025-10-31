# Sistema de Selectores de Ubicación

## Descripción
Se ha implementado un sistema de selectores en cascada para los campos de Origen y Destino en los formularios de "Publicar Ruta" y "Publicar Carga".

## Características
- **Selectores en cascada**: País → Departamento/Región → Ciudad
- **Países incluidos**: Bolivia, Chile, Perú y Argentina
- **Datos completos**: Departamentos/regiones y principales ciudades de cada país

## Archivos Creados

### 1. `/public/js/location-data.js`
Contiene los datos estructurados de:
- Bolivia: 9 departamentos con sus principales ciudades
- Chile: 13 regiones con sus principales ciudades
- Perú: 20 departamentos con sus principales ciudades
- Argentina: 23 provincias con sus principales ciudades

### 2. `/public/js/location-selector.js`
Clase JavaScript que maneja la lógica de los selectores en cascada:
- Inicializa los selectores
- Maneja eventos de cambio
- Actualiza el campo hidden con el formato: "Ciudad, Departamento/Región, País"

### 3. `/public/css/location-selector.css`
Estilos para los selectores:
- Diseño responsive
- Estados disabled
- Validación visual
- Compatibilidad con Bootstrap

## Archivos Modificados

### 1. `/resources/views/ofertas/create.blade.php` (Publicar Ruta)
- Reemplazados los campos de texto de Origen y Destino por selectores en cascada
- Agregados scripts y estilos necesarios

### 2. `/resources/views/ofertas_carga/create.blade.php` (Publicar Carga)
- Reemplazados los campos de texto de Origen y Destino por selectores en cascada
- Agregados scripts y estilos necesarios

## Funcionamiento

1. **Selección de País**: El usuario selecciona un país del primer dropdown
2. **Selección de Departamento/Región**: Se habilita el segundo dropdown con los departamentos/regiones del país seleccionado
3. **Selección de Ciudad**: Se habilita el tercer dropdown con las ciudades del departamento/región seleccionado
4. **Almacenamiento**: El campo hidden almacena el valor completo en formato: "Ciudad, Departamento/Región, País"

## Ejemplo de Flujo

```
Usuario selecciona:
1. País: Bolivia
2. Departamento: La Paz
3. Ciudad: La Paz

Valor almacenado en campo hidden: "La Paz, La Paz, Bolivia"
```

## Validación
- Todos los selectores son requeridos (required)
- Los selectores dependientes están deshabilitados hasta que se seleccione el valor anterior
- El formulario no se puede enviar hasta que se complete la selección completa

## Extensibilidad

Para agregar más países o ciudades, editar el archivo `location-data.js` siguiendo la estructura:

```javascript
'NombrePaís': {
    'Departamento/Región': ['Ciudad1', 'Ciudad2', 'Ciudad3'],
    'Otro Departamento': ['CiudadA', 'CiudadB']
}
```

## Notas Técnicas
- Compatible con Laravel Blade
- Usa Bootstrap 5 para estilos base
- JavaScript vanilla (sin dependencias adicionales)
- Responsive y mobile-friendly
