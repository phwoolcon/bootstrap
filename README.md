# Phwoolcon Bootstrap

Phalcon + Swoole

[Demo](http://phwoolcon.com/)

***

**WARNING**: This project is in alpha stage of development,  
use at your own risk!

***

> ## EASY TO USE

The Bootstrap provides directory structure for running Phwoolcon.

The purpose of this project is to create a high performance  
web application, which can run in traditional php-fpm mode and  
service mode.

In service mode, you gain extreme speed for your application,  
by reducing lot of unnecessary and repetitive computing.

If you have bugs in service mode, you can easily turn off the service  
mode, you loose some speed (but still fast) to gain more stability,  
fix your bugs and apply service mode again.

# 1. System Requirements
* Linux or MacOS (Windows is neither recommended nor tested,  
but you can install Linux on Windows by VirtualBox or other virtual machines)
* Nginx (Latest version recommended)
* PHP version >= 5.5 (7.0 is recommended, year 2017)
* PHP components: fpm, gd, cli, curl, dev, json, mbstring, mcrypt, pdo-mysql, redis, xml, zip
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
## 2.2. Configure Composer
Please **DO NOT** edit `composer.json` directly, that will break bootstrap update.

use `composer.local.json` instead:
```bash
vim composer.local.json
```

Manage your project repositories here, for example:
```json
{
    "require": {
        "my/project": "~1.0"
    },
    "repositories": [
        {
            "type": "git",
            "url": "git@git.example.com:my/project.git",
            "vendor-alias": "my-org"
        }
    ]
}
```

Please see [Composer Merge Plugin (by Wikimedia)](https://github.com/wikimedia/composer-merge-plugin/blob/master/README.md#plugin-configuration) for detailed reference.

Demo: [Phwoolcon Demo](https://github.com/phwoolcon/demo#7-install-phwoolcondemo).

## 2.3. Organize Your Project Codes
All project codes will be organized as composer packages.

**NEVER** put your codes into the `app/` directory, that make it complicated to implement modularization.

### 2.3.1 Create Project Composer Package
If you are the first time to use Phwoolcon, create a new repository  
in the `vendor` directory, for your project codes:
```bash
mkdir -p vendor/my/project
cd vendor/my/project
git init
echo "# My First Phwoolcon Project" > README.md
cat > composer.json << 'EOL'
{
    "name": "my/project",
    "description": "My First Phwoolcon Project",
    "type": "library",
    "license": "proprietary",
    "authors": [
        {
            "name": "My Name",
            "email": "my-email@example.com"
        }
    ],
    "minimum-stability": "dev",
    "require": {
        "php": ">=5.5.0",
        "phwoolcon/phwoolcon": "~1.0"
    },
    "autoload": {
        "psr-4": {
            "My\\Project\\": "src",
            "My\\Project\\Tests\\": "tests"
        },
        "exclude-from-classmap": [
            "/tests/"
        ]
    },
    "extra": {
        "branch-alias": {
            "dev-master": "1.0.x-dev"
        }
    }
}
EOL
mkdir phwoolcon-package
cat > phwoolcon-package/phwoolcon-package-my-project.php << 'EOL'
<?php
return [
    'my/project' => [
        'di' => [
            10 => 'di.php',
        ],
    ],
];
EOL
touch phwoolcon-package/di.php
touch phwoolcon-package/routes.php
mkdir -p phwoolcon-package/config
mkdir -p phwoolcon-package/views
mkdir -p phwoolcon-package/assets
git add ./
git commit -m "Initial commit"
git remote add origin git@git.example.com:my/project.git
git push
```

Now you have a private composer repository, your first `Phwoolcon package`.

If you want to share it to others, you can publish it on [Github](https://github.com) and [Packagist](https://packagist.org).

### 2.3.2. Fetch Upstream Codes
Return to your working directory (i.e. the Phwoolcon Bootstrap directory),  
and then pull upstream codes:
```bash
cd ../../..
git pull # Update bootstrap to ensure latest version is used
composer update # Update the project
```

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
in the file `composer.local.json`, which was created in step [2.2.](#s2.2)

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
Let's take `rsync` as an example:
```bash
rsync -auv --delete --chown=www-data:www-data --rsync-path='sudo rsync' \
    --exclude-from=./deployignore ./ \
    user@production-host:/path/to/production/directory/
ssh www-data@production-host \
    '/path/to/production/directory/bin/dump-autoload'
```

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

## 5. License
Phwoolcon Bootstrap is licensed under the [Apache License 2.0](LICENSE)

### 5.1. Third Party Software
Phwoolcon Bootstrap uses third-party libraries or other resources that  
may be distributed under licenses different than Phwoolcon, please  
check [Third Party Licenses](3RD-PARTY-LICENSES.md) for details.

## 6. Appendix
> Software suppliers are trying to make their software packages more "user-friendly". Their best approach, so far, has been to take all the old brochures, and stamp the words, "user-friendly" on the cover.  
>  
> <div align="right">— Bill Gates</div>

So did I.

[中文](README-zh.md)
