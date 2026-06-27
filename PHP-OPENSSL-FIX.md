# PHP 8.4 extensions (MacPorts)

Your PHP 8.4 is from MacPorts. Some extensions are installed as separate ports.

## OpenSSL (Laravel encryption / sessions)

If you see: `Call to undefined function openssl_cipher_iv_length()`

```bash
sudo port install php84-openssl
```

## cURL (HTTP requests, APIs, Composer, etc.)

To enable the cURL extension:

```bash
sudo port install php84-curl
```

After installing any extension, restart your web server or `php artisan serve`.

## Verify

```bash
php -m | grep -i openssl   # should show: openssl
php -m | grep -i curl     # should show: curl
```
