
PARKEYMY
Documentación de Conexión API
React Native (Expo) + Laravel Sanctum
Versión 1.0  —  25 de febrero de 2026

1. Configuración base de la API
El archivo de configuración centraliza la URL base y el token de autenticación para todas las peticiones.
Archivo: app/services/api.ts
import axios from 'axios';
import * as SecureStore from 'expo-secure-store';

const api = axios.create({
  baseURL: 'http://192.168.0.103:8000/api',
});

api.interceptors.request.use(async (config) => {
  const token = await SecureStore.getItemAsync('token');
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

export default api;
Nota: Cambiar la IP por la dirección del servidor en producción.

2. Autenticación
2.1 Login
Método	Ruta	Body	Respuesta
POST	/login	usuario, password	token, usuario

await api.post('/login', { usuario, password });
El token recibido se guarda en SecureStore y se usa en todas las peticiones protegidas.

2.2 Logout
Método	Ruta	Auth	Acción
POST	/logout	Bearer token	Invalida el token en el servidor

await api.post('/logout');
await SecureStore.deleteItemAsync('token');

2.3 Perfil
Método	Ruta	Auth	Respuesta
GET	/perfil	Bearer token	{ usuario: { id_usuario, nombres, apellidos, correo_institucional, ... } }

const response = await api.get('/perfil');
const usuario = response.data.usuario;

3. Artículos
3.1 Tabla de endpoints
Método	Ruta	Rol requerido	Descripción
GET	/mis-articulos	Administrador, Aprendiz	Lista artículos del usuario autenticado
GET	/articulos/{id}	Administrador, Aprendiz	Detalle de un artículo
POST	/articulos	Administrador, Aprendiz	Crear nuevo artículo
PUT	/articulos/{id}	Administrador, Aprendiz	Editar artículo
DELETE	/articulos/{id}	Administrador, Aprendiz	Eliminar artículo

3.2 Campos del artículo
Campo	Requerido	Descripción
nombre_articulo	Sí	Nombre del artículo
id_categoria	Sí	ID de la categoría (1-6)
descripcion	No	Descripción del artículo
marca	No	Marca del artículo
modelo	No	Modelo del artículo
color	No	Color del artículo
numero_serie	No	Número de serie o identificación
observaciones	No	Observaciones adicionales

3.3 Estados del artículo
Estado	Color en app	Descripción
registrado	Azul #3498DB	Artículo recién registrado, aún no ha ingresado
en_sede	Verde #3CB371	Artículo dentro del complejo
retirado	Naranja #F39C12	Artículo que salió del complejo

3.4 Ejemplos de uso
// Obtener mis artículos
const response = await api.get('/mis-articulos');

// Crear artículo
await api.post('/articulos', {
  nombre_articulo: 'Portátil Lenovo',
  id_categoria: 1,
  descripcion: 'Equipo de uso académico',
  marca: 'Lenovo', modelo: 'ThinkPad T14',
  color: 'Negro', numero_serie: 'SN-001'
});

// Editar artículo
await api.put(`/articulos/${id}`, { nombre_articulo: 'Nuevo nombre' });

// Eliminar artículo
await api.delete(`/articulos/${id}`);

4. Generación de QR
4.1 Endpoints QR
Método	Ruta	Rol requerido	Descripción
POST	/generar-qr	Administrador, Aprendiz	Genera un QR de ingreso o salida
GET	/validar-qr/{codigo}	Administrador, Vigilante	Valida el QR y actualiza estados
POST	/ingreso/{codigo}	Administrador, Vigilante	Registra el movimiento de ingreso

4.2 Generar QR de ingreso
await api.post('/generar-qr', {
  articulos: [1, 2, 3],   // IDs de artículos
  tipo_movimiento: 'ingreso'
});
Respuesta: { qr_id, codigo_qr, qr_url }  —  La URL apunta a la imagen PNG del QR.

4.3 Reglas de negocio del QR
Ingreso: solo artículos con estado registrado o retirado pueden incluirse.
Salida: solo artículos con estado en_sede pueden incluirse.
El QR expira en 2 horas desde su generación.
Solo puede existir un QR activo por usuario a la vez.
Al escanear: los artículos cambian a en_sede (ingreso) o retirado (salida).

5. Categorías de artículos
Las categorías están definidas en la base de datos. No existe un endpoint para obtenerlas, por lo que se manejan de forma estática en el frontend.
ID	Nombre	Descripción
1	Tecnología	Laptops, tablets, celulares, cámaras, discos duros
2	Equipos académicos	Instrumentos y equipos para actividades académicas
3	Herramientas	Herramientas técnicas, eléctricas o mecánicas
4	Instrumentos musicales	Instrumentos y equipos de sonido personales
5	Objetos personales	Maletas, cascos y objetos personales generales
6	Movilidad personal	Bicicletas, patinetas y medios de transporte personal

6. Configuración de red local
6.1 Requisitos para desarrollo local
Requisito	Detalle
Misma red WiFi	PC y celular deben estar en el mismo WiFi
Red configurada como Privada	Windows: clic en WiFi → cambiar a Red privada
Firewall	Abrir puerto 8000: netsh advfirewall firewall add rule...
Laravel	Correr con: php artisan serve --host=0.0.0.0 --port=8000
XAMPP	MySQL debe estar activo

6.2 Comando para abrir el firewall
netsh advfirewall firewall add rule name="Laravel 8000" ^
  dir=in action=allow protocol=TCP localport=8000 profile=any

6.3 Servidores a correr
Servidor	Comando
Laravel (API)	php artisan serve --host=0.0.0.0 --port=8000
Expo (App)	npx expo start --clear
XAMPP MySQL	Iniciar desde el panel de XAMPP

7. Estructura de archivos relevantes
Archivo	Propósito
app/services/api.ts	Configuración de Axios con interceptor de token
app/(stack)/login/index.tsx	Pantalla de login
app/stackInterno/consultarRegistros.tsx	Lista de artículos del usuario
app/stackInterno/registrarArticulo.tsx	Formulario de registro de artículo
app/stackInterno/editarArticulo.tsx	Formulario de edición y eliminación
app/stackInterno/detalleArticulo.tsx	Vista detallada de un artículo
app/(tabs)/perfil/index.tsx	Perfil del usuario autenticado
app/stackInterno/generarQR.tsx	Generación de QR de ingreso
