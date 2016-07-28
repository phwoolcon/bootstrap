# Phwoolcon Bootstrap

Phalcon + Swoole

[Demo](http://phwoolcon.com/)

***

**WARNING**: This project is in very early stage of development,  
use at your own risk!

***

The purpose of this project is to create a high performance  
web application, which can run in traditional php-fpm mode and  
service mode.

In service mode, you gain extreme speed for your application,  
by reducing lot of unnecessary and repetitive computing.

If you have bugs in service mode, you can easily turn off the service  
mode, you loose some speed (but still fast) to gain more stability,  
fix your bugs and apply service mode again.

# Usage
Create your project by composer:
```
composer create-project "phwoolcon/bootstrap":"dev-master" your-project-name
```

# Spirits
* Aimed at performance
* Aimed at scalability
* Powerful features, with intuitive and readable codes
* Component based, explicitly introduced
* Configurable features

# Features

## Base Components
* Extended Phalcon Config (Both in native PHP file and DB)
* Phalcon Cache
* Extended Phalcon ORM
* View: Theme based layouts and templates
* Multiple DB connector
* Events
* Configurable Cookies
* Session
* Openssl based encryption/decryption
* Multiple Queue producer and asynchronous CLI worker
* Assets: Theme based, compilable JS/CSS management
* Log
* Lighten route dispatcher
* Internalization
* Finite state machine
* Mail
* Symfony CLI console

## Specific Components
* Admin interface
* User authentication (Register and login)
* SSO Server and client
* Orders
* Payment

[中文](README-zh.md)
