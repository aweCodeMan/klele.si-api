<div align="center" style="margin-top: 2rem">
<a href="https://klele.si" target="_blank">
<svg width="116" height="28" viewBox="0 0 116 28" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.7481 28H15.2096V25.4538H12.6738V22.9076H10.1406V20.3614H7.60479V17.8206H5.06897V28H0V0H5.06897V10.1821H7.60479V7.63587H10.1406V5.08968H12.6738V2.54619H15.2096V0H20.2786V5.08968H17.7481V7.63587H15.2096V10.1821H12.6738V12.7256H10.1406V15.2717H12.6738V17.8206H15.2096V20.3641H17.7481V22.9103H20.2812V28H17.7481Z" fill="#FF637B"></path><path d="M27.0847 28H24.5488V0H29.6205V28H27.0847Z" fill="#FF637B"></path><path d="M44.0181 28.0001H36.416V25.4539H33.8828V10.1822H36.4186V7.63867H46.5566V10.1822H49.0924V17.8207H38.9491V22.9077H44.0181V20.3642H49.0897V25.4539H46.5539V28.0001H44.0181ZM41.4849 15.2718H44.0181V12.7284H38.9491V15.2718H41.4849Z" fill="#FF637B"></path><path d="M55.8796 28H53.3438V0H58.4127V28H55.8796Z" fill="black"></path><path d="M72.8127 28.0001H65.2079V25.4539H62.6748V10.1822H65.2079V7.63867H75.3486V10.1822H77.8817V17.8207H67.7438V22.9077H72.8127V20.3642H77.8817V25.4539H75.3486V28.0001H72.8127ZM70.2769 15.2718H72.8127V12.7284H67.7438V15.2718H70.2769Z" fill="black"></path><path d="M84.6718 28.0001H82.1387V22.9077H87.2076V28.0001H84.6718Z" fill="black"></path><path d="M101.607 28.0001H91.4668V22.9077H101.607V20.3642H94.0026V17.8207H91.4668V10.1822H94.0026V7.63867H106.676V12.7284H96.5385V15.2718H104.143V17.8207H106.679V25.4539H104.143V28.0001H101.607Z" fill="black"></path><path d="M113.48 5.09238H110.944V0H116.013V5.09238H113.48ZM113.48 28H110.944V7.63857H116.013V28H113.48Z" fill="black"></path></svg>
</a>
</div>

## About Klele.si API

The API for [klele.si](https://klele.si) is based on the PHP framework [Laravel](https://laravel.com/).

## Setting up the API

We use [Sail](https://laravel.com/docs/8.x/sail) for development, but there's nothing stopping you from using a
different development environment.

## Setting your `env`

There's a couple of variables that you need to set before you get going.

### `FRONTEND_URL`

You should set the url to your frontend application. This url is used when redirecting users after they have verified
their email, or when they need to reset their password.

### `CORS_ALLOWED_ORIGINS`

Either set the url of your frontend or `*` if you're developing locally. Make sure that this is explicitly set when used
in production. This is only needed if the API is on a different domain (or subdomain).

### `SESSION_DOMAIN`

Because we're using [Sanctum](https://laravel.com/docs/8.x/sanctum#introduction) to authenticate users, we need to setup
a parent session domain name, otherwise we might have a problem with setting the API cookies needed to authenticate
users. This is only needed if the API is on a different domain (or subdomain)

### Running tests

If you're using Sail, just run `sail test` and you should be good to go.

## Security Vulnerabilities

If you discover a security vulnerability within Klele.si, please send an e-mail to Miha Medven
via [miha@klele.si](mailto:miha@klele.si). All security vulnerabilities will be promptly addressed.

## License

The Klele.si API is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
