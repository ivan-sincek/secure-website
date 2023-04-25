# Secure Website

Secure website with a registration, sign in, [session management](https://github.com/ivan-sincek/secure-website/blob/master/src/php/session.class.php), and CRUD controls. No framework was used.

Used [PHP Data Objects (PDO)](https://github.com/ivan-sincek/secure-website/blob/master/src/php/database.class.php) for securely accessing a database in PHP (MySQL database included).

Used [CSS Flexbox](https://github.com/ivan-sincek/secure-website/blob/master/src/css/main.css) for the responsive design.

All the scripts are successfully validated with [Markup Validation Service](https://validator.w3.org).

Built with security in mind. The following attacks are prevented in the code:

* sign in brute force,
* session fixation,
* SQL injection,
* cross-site scripting (XSS),
* [cross-site request forgery (CSRF)](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html#synchronizer-token-pattern),
* etc.

And, many more attacks are prevented through hardening.

Tested on XAMPP for Windows v7.4.3 (64-bit) with Chrome v104.0.5112.102 (64-bit) and Firefox v104.0 (64-bit).

Made for educational purposes. I hope it will help!

## How to Run

Import [\\db\\secure_website.sql](https://github.com/ivan-sincek/secure-website/blob/master/db/secure_website.sql) to your database server.

Copy all the content from [\\src\\](https://github.com/ivan-sincek/secure-website/tree/master/src) to your server's web root directory (e.g. to \\xampp\\htdocs\\ on XAMPP).

Change the database settings inside [\\src\\php\\config.ini](https://github.com/ivan-sincek/secure-website/blob/master/src/php/config.ini) as necessary.

Check the sign in credentials [here](https://github.com/ivan-sincek/secure-website/blob/master/db/test_accounts.txt).

Navigate to the website with your preferred web browser.

---

On web servers other than XAMPP (Apache) you might need to load `Multibyte String` librabry in PHP.

In XAMPP it is as simple as uncommenting `extension=mbstring` in `php.ini`.

## Apache Hardening

From your Apache directory, open `\conf\httpd.conf`:

**Disable HTTP TRACE method.** Navigate to `Supplemental configuration` section and add new configuration `TraceEnable Off`.

**Prevent directory listing.** Navigate to `DocumentRoot` section and remove `Indexes` from `Options Indexes FollowSymLinks Includes ExecCGI`.

**Prevent clickjacking attacks.** Navigate to `Supplemental configuration` section and add new configuration `Header always set X-Frame-Options "DENY"`.

**Set Content Security Policy (CSP).** The following configuration will only allow you to load resources from your own domain. Navigate to `Supplemental configuration` section and add new configuration `Header always set Content-Security-Policy "default-src 'self'"`. Search the Internet for more Content Security Policy options. Check CSP validator [here](https://csp-evaluator.withgoogle.com).

**Block MIME sniffing.** Navigate to `Supplemental configuration` section and add new configuration `Header always set X-Content-Type-Options "nosniff"`.

**Enforce cross-site scripting (XSS) filter.** Navigate to `Supplemental configuration` section and add new configuration `Header always set X-XSS-Protection "1; mode=block"`.

**Prevent cross-site request forgery.** The following configuration will not allow request from other websites (i.e. cross-site request). Navigate to `Supplemental configuration` section and add new configurations `Header always set Access-Control-Allow-Origin "https://securewebsite.com"` - where `https://securewebsite.com` is your own domain name.

**Set rate limiting.** This is more of a denial-of-service (DoS) protection. Comment out `mod_ratelimit.so` extension, and add the follow code to the end of the file (this is speed in KiB/s, not number of requests; this will also affect the page load speed):

```fundamental
<Location />
	SetOutputFilter RATE_LIMIT
	SetEnv rate-limit 1024
</Location>
```

---

From your Apache directory, open `\conf\extra\httpd-autoindex.conf`:

**Prevent '/icons/' directory listing.** Comment out `Alias /icons/ "C:/xampp/apache/icons/"`.

---

From your Apache directory, open `\conf\extra\httpd-default.conf`:

**Prevent version disclosure.** Set `ServerTokens` to `Prod` and `ServerSignature` to `Off`.

**Mitigate Slow Loris and other DoS attacks.** Lower `Timeout` to `60`.

---

From your Apache directory, open `\conf\extra\httpd-info.conf`:

**Disable '/server-status' page.** Comment out entire `<Location /server-status>` element.

## PHP Hardening

From your PHP directory, open `php.ini`:

**Prevent version disclosure.** Set `expose_php` to `Off`.

**Prevent display errors information disclosure.** Set both `display_errors` and `display_startup_errors` to `Off`.

**Set the correct server's timezone.** Set both instances of `date.timezone` to your timezone. Search the Internet for a list of supported timezones in PHP.

**Set the session cookie's name.** Set `session.name` to your own desired value. In addition to this website, it is also set [here](https://github.com/ivan-sincek/secure-website/blob/master/src/php/session.class.php).

**Set the session cookie's lifetime.** Set `session.cookie_lifetime` to your own desired value. In addition to this website, it is also set [here](https://github.com/ivan-sincek/secure-website/blob/master/src/php/session.class.php).

**Set the session cookie's HttpOnly flag.** The following configuration will not allow client side scripts to access the session cookie. Set `session.cookie_httponly` to `1`. In addition to this website, it is also set [here](https://github.com/ivan-sincek/secure-website/blob/master/src/php/session.class.php).

**Use strict session mode.** Set `session.use_strict_mode` to `1`.

**Disable file uploads.** Do the following only if your website does not utilize file uploads. Set `file_uploads` to `Off`.

**Prevent remote file inclusion.** Set `allow_url_fopen` to `Off`.

**Disable dangerous PHP functions.** Set `disable_functions` to `eval;exec;shell_exec;curl_exec;passthru;system;proc_open;popen`. Search the Internet for additional dangerous PHP functions.

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
