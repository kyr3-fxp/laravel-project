# Configuración de Google Maps API

El dashboard Solar AI incluye una función de búsqueda de ubicación manual usando Google Maps. Para que funcione correctamente, necesitas obtener una API Key de Google Maps.

## Pasos para obtener tu API Key

### 1. Ir a Google Cloud Console
- Accede a [Google Cloud Console](https://console.cloud.google.com/)
- Si no tienes una cuenta, crea una en Google Cloud

### 2. Crear un nuevo proyecto
- En el selector de proyecto (esquina superior izquierda), haz clic en "Crear proyecto"
- Asigna un nombre al proyecto (ej: "Solar Dashboard")
- Haz clic en "Crear"

### 3. Habilitar APIs necesarias
- Ve a "APIs y servicios" > "Biblioteca"
- Busca y habilita estas APIs:
  - **Maps JavaScript API**
  - **Geocoding API**
  - **Places API** (opcional, para búsqueda de lugares)

### 4. Crear credenciales
- Ve a "APIs y servicios" > "Credenciales"
- Haz clic en "Crear credenciales" > "Clave de API"
- Se generará automáticamente tu clave API

### 5. Restringir la clave (Recomendado)
- En la página de credenciales, haz clic en tu clave API
- En "Restricciones de aplicación", selecciona "Sitios web HTTP (referentes)"
- Agrega tu dominio (ej: `localhost`, `tudominio.com`)
- En "Restricciones de API", selecciona solo las APIs que usarás:
  - Maps JavaScript API
  - Geocoding API

### 6. Configurar en Laravel
- Abre el archivo `.env` en la raíz del proyecto
- Encuentra o crea la línea:
  ```
  GOOGLE_MAPS_API_KEY=tu_clave_api_aqui
  ```
- Reemplaza `tu_clave_api_aqui` con la clave obtenida

### 7. Guardar y reiniciar
- Guarda el archivo `.env`
- Reinicia el servidor Laravel (presiona Ctrl+C y vuelve a ejecutar)

## Uso del botón de búsqueda

1. En la topbar del dashboard, encontrarás un botón con icono de mapa 🗺️
2. Haz clic para abrir el selector de ubicación
3. Haz clic en el mapa para mover el marcador
4. O arrastra el marcador rojo a la ubicación deseada
5. Se mostrará la dirección en tiempo real
6. Haz clic en "Confirmar ubicación" para cargar los datos solares de esa ubicación

## Solución de problemas

### "La API key no es válida"
- Verifica que la clave esté correctamente copiada en `.env`
- Asegúrate de que las APIs estén habilitadas en Google Cloud Console

### El mapa no carga
- Comprueba que `GOOGLE_MAPS_API_KEY` esté configurada
- Verifica que JavaScript no tiene errores en la consola del navegador (F12)
- Asegúrate de que las restricciones de referencia permitan tu dominio actual

### La geocodificación no funciona
- Verifica que "Geocoding API" esté habilitada en Google Cloud
- Comprueba que tu clave no tiene restricciones de API conflictivas

## Costo

- Google Maps tiene un tier gratuito con $200 de crédito mensual
- La mayoría de usos personales y de prueba están cubiertos gratuitamente
- Consulta [Pricing de Google Maps](https://cloud.google.com/maps-platform/pricing) para más detalles

## Seguridad

- **Nunca** compartas tu API Key públicamente
- Usa restricciones de referencia para limitar su uso
- Si crees que tu clave fue comprometida, regenera una nueva
