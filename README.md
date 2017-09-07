# Phwoolcon Bootstrap

Phalcon + Swoole

[Demo](https://phwoolcon.org/)

***

The Bootstrap provides directory structure for running Phwoolcon.

The purpose of this project is to create a high performance web application,  
which can run in traditional php-fpm mode and service mode.

In service mode, you gain extreme speed for your application,  
by reducing lot of unnecessary and repetitive computing.

If you have bugs in service mode, you can easily turn off the service mode,  
you lose some speed (but still fast) to gain more stability, 
fix bugs and apply service mode again.

# 1. System Requirements
* Linux or MacOS (Windows is neither recommended nor tested,  
but you can install Linux on Windows by VirtualBox or other virtual machines)
* Nginx (Latest version recommended)
* PHP version >= 5.5 (7.1 is recommended, year 2017)
* PHP components: fpm, gd, cli, curl, dev, json, mbstring, pdo-mysql, redis, xml, zip
* MySQL server (or MariaDB / Percona / TiDB)
* Phalcon (Latest version recommended)
* Swoole (Latest version recommended)
* Composer (Latest version recommended)

# 2. Usage
## 2.1. Create Working Directory
```bash
git clone git@github.com:phwoolcon/bootstrap.git my-project-name
cd my-project-name
```

<a name="s2.2"></a>
## 2.2. Import Packages
Please **DO NOT** edit `composer.json` directly, that will break framework update.

Use `bin/import-package` to create `composer.local-*.json` instead,  
`composer.local-*.json` is isolated from the framework itself.

For example:

* To import a public composer package:
```bash
bin/import-package some/public-package
```

* To import a private composer package:
```bash
bin/import-package git@git.example.com:my/private-project.git
```

Please see [Composer Merge Plugin (by Wikimedia)](https://github.com/wikimedia/composer-merge-plugin/blob/master/README.md#plugin-configuration) to learn more about `composer.local-*.json`.

Demo: [Phwoolcon Demo](https://github.com/phwoolcon/demo#7-install-phwoolcondemo).

## 2.3. Organize Your Project Codes
All project codes will be organized as composer packages.

**NEVER** put your codes into the `app/` directory, that make it complicated to implement modularization.

<a name="s2.3.1"></a>
### 2.3.1 Create a Phwoolcon Package
Run:
```bash
bin/cli package:create
```
This tool will ask you to input some basic information, for example:

```text
----------------------------------------------------------------------
Please, provide the following information:
----------------------------------------------------------------------
Your name: Christopher CHEN
Your Github username (<username> in https://github.com/username): Fishdrowned
Your email address: fishdrowned@gmail.com
Your website [https://github.com/Fishdrowned]:
Package vendor (<vendor> in https://github.com/vendor/package) [Fishdrowned]: phwoolcon
Package name (<package> in https://github.com/vendor/package): theme-mdl
Package very short description: The Material Design Lite Theme for Phwoolcon
PSR-4 namespace (usually, Vendor\Package) [Phwoolcon\ThemeMdl]: 

----------------------------------------------------------------------
Please, check that everything is correct:
----------------------------------------------------------------------
Your name: Christopher CHEN
Your Github username: Fishdrowned
Your email address: fishdrowned@gmail.com
Your website: https://github.com/phwoolcon
Package vendor: phwoolcon
Package name: theme-mdl
Package very short description: The Material Design Lite
PSR-4 namespace: Phwoolcon\ThemeMdl

Modify files with these values? [y/N/q] y

Done.
Now you should remove the file 'prefill.php'.

----------------------------------------------------------------------
Please, provide the following information:
----------------------------------------------------------------------
Git repository (The git repository of the package) [git@github.com:phwoolcon/theme-mdl.git]: 
Choose license (1 - APACHE 2.0, 2 - MIT, 3 - Proprietary) [1]: 
```

Then the package is there under the `vendor` directory, with git initialized,  
remote repository added, it is all ready for you to commit and push the files.

```bash
git commit -m "Initial commit"
git push
```

Now you have a private composer repository, your first `Phwoolcon package`.

If you want to share it to others, you can publish it on [GitHub](https://github.com) and [Packagist](https://packagist.org).

### 2.3.2. Import Your Package
Now you can import your newly created package.

See [2.2. Import Packages](#s2.2)

### 2.3.3. Update Codes
```bash
bin/update
```
This script will do:

* `git pull` to update the framework (`phwoolcon/bootstrap`) itself;
* `composer update` to update all composer packages, including your project;
* `bin/cli migrate:up` to run DB migration scripts;
* `bin/dump-autoload` to update composer autoload, apply latest  
assets, configs, locales, generate model traits and IDE helper.

## 2.4. Phwoolcon Configuration
Project configuration files are symlinked from packages into `app/config` directory.

please **DO NOT** edit them directly.

### 2.4.1. Apply Environment Configuration
Adding new copies into `app/config/{$environment}/` to override default values.

`{$environment}` is the runtime environment name, by default `production`.

You can change this name by setting `$_SERVER['PHWOOLCON_ENV']`.

### 2.4.2. Add Custom Configuration Files
Return to your project package (i.e. `vendor/my/project`),  
you may add new configuration files under `phwoolcon-package/config/`.

Then run `bin/dump-autoload` to symlink it to `app/config`.

Get the config values in your codes, for example:

Config file `phwoolcon-package/config/key.php`
```php
<?php
return [
    'to' => [
        'config' => 'hello',
    ],
];
```

In your code:

```php
echo Config::get('key.to.config'); // Prints "hello"
```

**IMPORTANT** Please **DO NOT** create config file with the default names.

## 2.5. Modularization
Code reusing, modularization, the implementation is not as simple as it looks,  
have you ever copy-pasted such "modules" among projects?

Phwoolcon makes it simple:

* First, make your project as a `Phwoolcon package`;
* Then, make all your common components as `Phwoolcon packages`;
* Finally, add them to `composer.json` in your project package.

**IMPORTANT** Any private repositories **MUST** be declared in the `repositories` section  
in the file `composer.local.json`, which was created in step [2.3.1 Create a Phwoolcon Package](#s2.3.1)

## 2.6. Build / Deploy
It was a pain to deploy projects that used composer, because:

* If you rely on `composer update`, you may get inconsistent codes among installations;
* If you put `vendor` into VCS, you may get conflicts or lose latest updates.
* `composer update` is slow and may crash.

Phwoolcon solved this problem, by packing the whole working directory,  
then commit them into a new `release` branch.

### 2.6.1. Build
```bash
bin/build
```

The build script will create `ignore/release` directory, which is  
ready to be pushed to the production environment.

Push them to your project repository, in branch `release`.

### 2.6.2. Deploy
#### 2.6.2.1. Manual Deployment

Let's take `rsync` as an example:
```bash
rsync -auv --delete --chown=www-data:www-data --rsync-path='sudo rsync' \
    --exclude-from=./deployignore ./ \
    user@production-host:/path/to/production/directory/
ssh www-data@production-host \
    '/path/to/production/directory/bin/dump-autoload'
```

#### 2.6.2.2. Auto Deployment
Please see [Deploy Automator](https://github.com/phwoolcon/deploy-automator)

## 2.7. Service Mode

### 2.7.1. Enable Service Mode
To enable service mode, please set CGI parameter `USE_SERVICE` to 1.
```conf
    location ~ \.php$ {
        .
        .
        .
        fastcgi_param USE_SERVICE 1;
    }
```

### 2.7.2. Start Service
To start the service, please run:
```bash
bin/cli service start
```
Now the phwoolcon service handles your site.

To stop the service, please run:
```bash
bin/cli service stop
```
Now your site is still available in php-fpm mode.

### 2.7.3. Install As System Service (Incomplete)
Run this command to install your project as a system service:
```bash
bin/cli service install
```
Then you can start/stop/restart/reload your service by:
```bash
service phwoolcon start
service phwoolcon stop
service phwoolcon restart
service phwoolcon reload
```
To uninstall the service:
```bash
bin/cli service uninstall
```

# 3. Spirits
* Aimed at performance
    * That's why Phalcon and Swoole is introduced
* Aimed at scalability
    * Ability to build a cross-data-center distributed system (under construction)
* Powerful features, with intuitive and readable codes
    * Keep it simple and do it right
* Modular implementation
    * No more `app/` codes, composer packages instead
* Deployment-friendly
    * No more `composer update` on production

# 4. Features

## 4.1. Base Components
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

## 4.2. Specific Components
* Admin interface
* User authentication (Register and login)
* SSO Server and client
* Orders
* Payment

## 5. Credits
Phwoolcon Bootstrap uses third-party libraries or other resources that  
may be distributed under licenses different than Phwoolcon, please  
check [Credits](CREDITS.md) for details.

## 6. License
Phwoolcon Bootstrap is licensed under the [Apache License 2.0](LICENSE.md)

[中文](README-zh.md)
