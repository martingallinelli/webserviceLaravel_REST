# **API**

## **MIGRATION**
### **-----( CREAR )**

Se genera un archivo *nombreMigration* en el directorio **database/migrations**

~~~
    php artisan make:migration nombreMigration --create=nombreTabla
~~~

### **-----( CONFIGURAR )**

Dentro del archivo recién creado, en el método **up( )**, agregamos los campos que necesitamos en nuestra tabla


### **-----( CORRER MIGRATION )**

Se va a generar la tabla en nuestra DB

~~~
    php artisan migrate
~~~

<br>

---

<br>

## **SEEDER**

Es una clase de Laravel que permite insertar datos en las tablas de nuestra DB.

### **-----( CREAR )**

Se genera un archivo *NombreSeeder.php* en el directorio **database/seeders**

~~~
    php artisan make:seeder NombreSeeder
~~~

### **-----( CARGAR DATOS )**

Dentro del archivo recién creado, en el método **run( )**, escribimos los datos a cargar en nuestra tabla.

Utilizamos el método **table( )** del Facade **DB**

~~~
    DB::table('nombreTabla')->insert([ 
        'nombreCampo' => 'valor' 
    ]);
~~~

### **-----( CORRER SEEDER )**

Se van a guardar los datos en la tabla de nuestra DB.  

Dentro del archivo **DatabaseSeeder**, en el método **run( )**, debemos llamar a la clase seeder recién creada, y después ejecutar el comando

~~~
    $this->call(NombreSeeder::class);
~~~

~~~
    php artisan db:seed
~~~

<br>

---

<br>

## **MODEL**
### **-----( CREAR )**

Se genera un archivo *NombreModel* en el directorio **app/Models**

~~~
    php artisan make:model NombreModel
~~~

### **-----( CONFIGURAR )**

Dentro del archivo recién creado, debemos configurar la tabla al que pertenece, los datos que vamos a guardar y los que vamos a ocultar cuando querramos verlos

~~~
    protected $table = 'nombreTabla';
~~~

~~~
    protected $fillable = ['nombreCampo1', ..'nombreCampoN'];
~~~

~~~
    protected $hidden = ['nombreCampo1', ..'nombreCampoN'];
~~~

<br>

---

<br>

## **CONTROLLER**
### **-----( CREAR )**

Se genera un archivo *nombreArchivo* en el directorio **app/Http/Controllers**  

***--api** = crea los métodos necesarios para una API*

~~~
    php artisan make:controller NombreController --api
~~~

<br>

---

<br>

## **ROUTES**
### **-----( CREAR )**

Dentro del directorio **routes**, en el archivo **api.php**, creamos nuestra ruta para la API

~~~
    Route::apiResource('nombreRuta', 'NombreController');
~~~

<br>

---

<br>

## **REQUEST VALIDATE**
### **-----( CREAR )**

Nos permite validar los datos a guardar / actualizar dentro de la tabla de nuestra DB

Se genera un archivo *NombreRequest* en el directorio **app/Request**

~~~
    php artisan make:request NombreRequest
~~~

### **-----( CONFIGURAR )**

Para el **CREATE**:  
Dentro del archivo recién creado, en el método **rules( )**, debemos agregar los campos y sus validaciones.  
Y en el controlador, en el método correspondiente, debemos cambiar el parámetro de Request a *NombreRequest*

~~~
    return [
        'nombreCampo1' => 'required | min:5 | max:100',
        'nombreCampoN' => 'required | unique:nombreTabla,nombreCampo'
    ];
~~~

~~~
    public function store(NombreRequest $request) { ... }
~~~

Para el **UPDATE**:  
Debemos crear otro archivo request, y en el método **rules( )**, además de agregar los campos y sus validaciones, en la validación del campo único, debemos agregar una excepcion, el id del elemento, para que permita actualizar.

~~~
    return [
        'nombreCampo1' => 'required | min:5 | max:100',
        'nombreCampoN' => 'required | unique:nombreTabla,nombreCampo,' . $this->route('nombreModel')
    ];
~~~

<br>

---

<br>

## **AUTH**

Nos permite reguardar las rutas de nuestra API utilizando un token.  

### **-----( CONFIGURAR )

Dentro del directorio **routes**, en el archivo **api.php**, creamos nuestra validación de seguridad. 

~~~
    Route::group(['middleware' => 'auth:api'], function () {
        Route::apiResource('nombreRuta', 'NombreController');
    });
~~~

Dentro del directorio **database/migrations**, en el archivo de migración de los **usuarios**, dentro del método **up( )**, agregar el campo *api_token* para almacenar nuestro token. Y luego corremos las migraciones nuevamente.

~~~
    $table->string('api_token')->nullable();
~~~

~~~
    php artisan migrate:fresh
~~~

Creamos un controlador para los **usuarios**, dentro de este, un método **store** para almacenar usuarios, un método **login** para poder loguearos, dentro de este método vamos a crear el token y guardarlo en la tabla de nuestra DB, y otro método **logout** para poder cerrar sesión, eliminando el token de la DB.  
*Esta forma de autenticación es válida para usar solo de una aplicación a la vez!*

~~~
    Str::random(100);
~~~

<br>

---

<br>

## **LARAVEL PASSPORT**

Permite aplicar autenticación OAuth 2.

### **-----( INSTALAR )**

Primero debemos instalar el paquete, y correr las migraciones ( php artisan migrate ).

~~~
    composer require laravel/passport
~~~

Después debemos ejecutar el siguiente comando, para crear las claves de encriptación necesarias para generar los token de acceso.

~~~
    php artisan passport:install
~~~

### **-----( CONFIGURAR )**

Dentro del directorio **app/Models**, debemos agregar el trait a nuestro modelo **User**

~~~
    use Laravel\Passport\HasApiTokens;

    class User extends Authenticatable
    {
        use HasApiTokens, HasFactory, Notifiable;
    }
~~~

Dentro del directorio **app/Providers**, en el arhivo **AuthServiceProvider**, y dentro del método **boot( )**, debemos agregar el llamado a las rutas, e importar la clase Passport.

~~~
    if (! $this->app->routesAreCached()) {
        Passport::routes();
    }
~~~

Dentro del directorio **condig**, debemos modificar el tipo de driver de autenticación de nuestra api.

~~~
    'api' => [
            'driver' => 'passport',
    ]
~~~

### **PERSONAL ACCESS CLIENT**
### **-----( INSTALAR )**

~~~
    php artisan passport:client --personal
~~~

En el archivo **.env**, debemos agregar las siguientes lineas, y reemplazar los valores ID y SECRET por los recién creados.

~~~
    PASSPORT_PERSONAL_ACCESS_CLIENT_ID="client-id-value"
    PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET="unhashed-client-secret-value"
~~~

### **-----( IMPLEMENTACION )**

Dentro del directorio **app/Controllers**, en el **UserController**, y dentro del método **login**, agregamos la creación del token para el usuario.

~~~
    $token = $user->createToken('Token Name')->accessToken;
~~~

### **-----( CERRAR SESION )

Dentro del directorio **app/Controllers**, en el **UserController**, y dentro del método **logout**, agregamos la eliminación de todos los token del usuario.

~~~
    $user->tokens->each(function($token){
        $token->delete();
    });
~~~

<br>

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
