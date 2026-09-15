## Peticion general
GET -> vista de login (AuthController@showLogin).

## POSTs
POST -> Procesamiento de credenciales(AuthController@login).
POST -> Cerrar sesion (AuthController@logout).

## GETs
GET ->/asistencia → Vista protegida del panel de asistencia (view('asistencia.index')).
GET /welcome -> Vista del dashboard principal (view('welcome')).

## Endpoints definidos
    GET /api/usuarios?estado={ESTADO} (activo|inactivo|todos)
    PUT /api/usuarios/{ID}
    POST /api/asistencia/entrada
    POST /api/asistencia/entrada