# Secure Website

Secure website with a registration, sign in, [session management](https://github.com/ivan-sincek/secure-website/blob/master/src/php/session.class.php), and CRUD controls. No framework was used.

Used [PHP Data Objects (PDO)](https://github.com/ivan-sincek/secure-website/blob/master/src/php/database.class.php) for securely accessing a database in PHP (MySQL database included).

Used [CSS Flexbox](https://github.com/ivan-sincek/secure-website/blob/master/src/css/main.css) for a responsive design.

All the scripts are successfully validated with the [Markup Validation Service](https://validator.w3.org).

Built with security in mind. The following attacks are prevented:

* sign in brute force,
* session fixation,
* SQL injection,
* cross-site scripting (XSS),
* cross-site request forgery (CSRF),
* etc.

Tested on XAMPP for Windows v7.4.3 (64-bit) with Chrome v80.0.3987.149 (64-bit) and Firefox v74.0 (64-bit).

Made for educational purposes. I hope it will help!

## How to Run

Import [\\db\\secure_website.sql](https://github.com/ivan-sincek/secure-website/blob/master/db/secure_website.sql) to your database server.

Copy all the content from [\\src\\](https://github.com/ivan-sincek/secure-website/tree/master/src) to your server's web root directory (e.g. to \\xampp\\htdocs\\ on XAMPP).

Change the database settings inside [\\src\\php\\config.ini](https://github.com/ivan-sincek/secure-website/blob/master/src/php/config.ini) as necessary.

Check the sign in credentials [here](https://github.com/ivan-sincek/secure-website/blob/master/db/test_accounts.txt).

Navigate to the website with your preferred web browser.

---

On web servers other than XAMPP (Apache) you might need to load `Multibyte String` librabry within PHP.

In XAMPP it is as simple as uncommenting the `extension=mbstring` line in the `php.ini` file.

## Apache Hardening

**Prevent version disclosure.** From your Apache directory go to `\conf\extra\httpd-default.conf` and set `ServerTokens` to `Prod` and `ServerSignature` to `Off`.

**Prevent directory listing.** From your Apache directory go to `\conf\httpd.conf`, navigate to `DocumentRoot` section and remove `Indexes` from `Options Indexes FollowSymLinks Includes ExecCGI`.

**Prevent '/icons/' directory listing.** From your Apache directory go to `\conf\extra\httpd-autoindex.conf` and comment out `Alias /icons/ "C:/xampp/apache/icons/"`.

**Disable '/server-status' page.** From your Apache directory go to `\conf\extra\httpd-info.conf` and comment out entire `<Location /server-status>` element.

**Disable HTTP TRACE method.** From your Apache directory go to `\conf\httpd.conf`, navigate to `Supplemental configuration` section and add new configuration `TraceEnable Off`.

**Prevent clickjacking attacks.** From your Apache directory go to `\conf\httpd.conf`, navigate to `Supplemental configuration` section and add new configuration `Header always set X-Frame-Options "DENY"`.

**Set Content Security Policy HTTP response header.** The following configuration will only allow you to load resources from your own domain. From your Apache directory go to `\conf\httpd.conf`, navigate to `Supplemental configuration` section and add new configuration `Header always set Content-Security-Policy "default-src 'self'"`. Search the Internet for more Content Security Policy options.

**Block MIME sniffing.** From your Apache directory go to `\conf\httpd.conf`, navigate to `Supplemental configuration` section and add new configuration `Header always set X-Content-Type-Options "nosniff"`.

**Enforce cross-site scripting filter.** From your Apache directory go to `\conf\httpd.conf`, navigate to `Supplemental configuration` section and add new configuration `Header always set X-XSS-Protection "1; mode=block"`.

**Mitigate Slow Loris and other DoS attacks.** From your Apache directory go to `\conf\extra\httpd-default.conf` and lower `Timeout` to `60`.

## PHP Hardening

**Prevent version disclosure.** From your PHP directory go to `php.ini` and set `expose_php` to `Off`.

**Prevent display errors information disclosure.** From your PHP directory go to `php.ini` and set both `display_errors` and `display_startup_errors` to `Off`.

**Set the correct server's timezone.** From your PHP directory go to `php.ini` and set both instances of `date.timezone` to your timezone. Search the web for a list of supported timezones in PHP.

**Set the session cookie's name.** From your PHP directory go to `php.ini` and set `session.name` to your own desired value. In addition to this website, it is also set [here](https://github.com/ivan-sincek/secure-website/blob/master/src/php/session.class.php).

**Set the session cookie's lifetime.** From your PHP directory go to `php.ini` and set `session.cookie_lifetime` to your own desired value. In addition to this website, it is also set [here](https://github.com/ivan-sincek/secure-website/blob/master/src/php/session.class.php).

**Set the session cookie's HttpOnly flag.** The following configuration will not allow client side scripts to access the session cookie. From your PHP directory go to `php.ini` and set `session.cookie_httponly` to `1`. In addition to this website, it is also set [here](https://github.com/ivan-sincek/secure-website/blob/master/src/php/session.class.php).

**Use strict session mode.** From your PHP directory go to `php.ini` and set `session.use_strict_mode` to `1`.

**Disable file uploads.** Do the following only if your website does not utilize file uploads. From your PHP directory go to `php.ini` and set `file_uploads` to `Off`.

**Prevent remote file inclusion.** From your PHP directory go to `php.ini` and set `allow_url_fopen` to `Off`.

**Disable dangerous PHP functions.** From your PHP directory go to `php.ini` and set `disable_functions` to `eval;exec;shell_exec;curl_exec;passthru;system;proc_open;popen`. Search the Internet for additional dangerous PHP functions.

## SSL/TLS Certificate

Find out how to create an SSL/TLS certificate [here](https://github.com/ivan-sincek/secure-website/tree/master/crt).

## Images

<p align="center"><img src="https://github.com/ivan-sincek/secure-website/blob/master/img/home_page.jpg" alt="Home Page"></p>

<p align="center">Figure 1 - Home Page</p>

<p align="center"><img src="https://github.com/ivan-sincek/secure-website/blob/master/img/register.jpg" alt="Registration"></p>

<p align="center">Figure 2 - Registration</p>

<p align="center"><img src="https://github.com/ivan-sincek/secure-website/blob/master/img/users.jpg" alt="Users Table"></p>

<p align="center">Figure 3 - Users Table</p>

<p align="center"><img src="https://github.com/ivan-sincek/secure-website/blob/master/img/responsive_design.jpg" alt="Responsive Design"></p>

<p align="center">Figure 4 - Responsive Design</p>
