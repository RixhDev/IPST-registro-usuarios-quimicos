REST: cada recurso se expone como una URL y se manipula con métodos HTTP(GET, POST, PUT).
FORMATO INTERCAMBIO: tratamiento de datos en JSON en todo momento, nunca como HTML en la API.
STATELESS: cada petición incluye exclusivamente la información necesaria, no depende de estado previo en el servidor.

####LISTAR RUTAS CON ARTISAN route:list

### token para pruebas. Borrar luego***.
"1|jdQwt2zpom6SyPNbwdhHR3KMTd2bkFPmMXrHpVR975b50778"

### prueba de usuarios activos
curl -H 'Authorization: Bearer 1|jdQwt2zpom6SyPNbwdhHR3KMTd2bkFPmMXrHpVR975b50778' http://127.0.0.1:8000/api/usua
rios?estado=activo


### generacion de tokens
´´´
    tinker
    > $user = App\Models\User::first();
    > $token = $user->createToken('test')->plainTextToken;
    > $token
´´´
