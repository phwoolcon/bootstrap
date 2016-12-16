# Phwoolcon Bootstrap

Phalcon + Swoole

[Demo](http://phwoolcon.com/)

***

**警告**：此项目现在处于 Alpha 版本开发状态，请慎重使用！

***

> ## 易于使用

Bootstrap 用于提供 Phwoolcon 运行时的目录结构。

本项目的目的是创建一个高性能的 Web 应用程序，既可以运行于传统的 php-fpm  
模式下，也可以运行在服务模式下。

在服务模式中，你的应用程序可以减少许多非必要的重复计算，获得极致的性能。

如果在服务模式中出现了 Bug，你可以轻松地关闭服务模式，损失一些性能（但是  
仍然很快）换取稳定性，待 Bug 修复后再启用服务模式。

# 1. 系统要求
* Linux 或者 MacOS（不推荐 Windows，也没测试过。  
不过你可以在 Windows 上用 VirtualBox 之类的虚拟机安装 Linux）
* Nginx（推荐最新版本）
* PHP version >= 5.5（推荐 5.6 或者 7.0，2016 年注）
* PHP 组件: fpm, gd, cli, curl, dev, json, mbstring, mcrypt, pdo-mysql, xml, zip
* MySQL server (或者 MariaDB / Percona)
* Phalcon（推荐最新版本）
* Swoole 1.8.13（Phwoolcon 和 Swoole 1.9 之间有未修复的兼容性问题）
* Composer（推荐最新版本）

# 2. 使用
## 2.1. 创建工作目录
用 composer 创建一个工作目录（带上 `--keep-vcs` 选项）：
```bash
composer create-project -salpha --keep-vcs "phwoolcon/bootstrap" my-project-name
cd my-project-name
```

<a name="s2.2"></a>
## 2.2. 配置 Composer
请 **不要** 直接编辑 `composer.json`，这样会使你无法获取 bootstrap 的更新。

请编辑 `composer.local.json`：
```bash
vim composer.local.json
```

在这里管理你的项目仓库，例如：
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

更多详情请查看 [Composer Merge Plugin (by Wikimedia)](https://github.com/wikimedia/composer-merge-plugin/blob/master/README.md#plugin-configuration)。

Demo: [Phwoolcon Demo](https://github.com/phwoolcon/demo#7-install-phwoolcondemo).

## 2.3. 组织你的项目代码
把所有项目代码都用 `composer 包` 来组织。

**绝对不要** 把你的代码放进 `app/` 目录里面，这样非常难以实施模块化。

### 2.3.1 创建项目的 Composer Package
如果你是第一次使用 Phwoolcon，在 `vender` 目录里面，  
给你的项目创建一个新的代码仓库：
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
    "bin": [
        "phwoolcon-package/phwoolcon-package-my-project.php"
    ],
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

现在你拥有了一个私有的 composer 仓库，你的第一个 `Phwoolcon 包`。

如果你乐于分享，你可以把它发布到 [Github](https://github.com) 和 [Packagist](https://packagist.org) 上。

### 2.3.2. 获取最新代码
回到你的工作目录（也就是 Phwoolcon Bootstrap 所在的目录），  
然后拉取代码：
```bash
cd ../../..
git pull # 确保 bootstrap 是最新的
composer update # 更新项目
```

## 2.4. Phwoolcon 配置文件
项目配置文件是用 symlink 从各个 `Phwoolcon 包` 里面连接 `app/config` 目录里的。

请 **不要** 直接编辑 `app/config` 目录下的文件。

### 2.4.1. 应用环境配置
复制配置文件到 `app/config/{$environment}/` 里面以覆盖默认配置。

`{$environment}` 是运行时环境的名称，默认为 `production`。

你可以通过 `$_SERVER['PHWOOLCON_ENV']` 改变这个名称。

### 2.4.2. 增加自己的配置文件
回到你的项目包（`vendor/my/project`），你可以在子目录  
`phwoolcon-package/config/` 下面创建新的配置文件。

然后运行 `bin/dump-autoload` 把他们连接到 `app/config`.

在你的代码里面可以使用这些配置项，举个例子：

配置文件 `phwoolcon-package/config/key.php`
```php
<?php
return [
    'to' => [
        'config' => 'hello',
    ],
];
```

在你的代码里面：

```php
echo Config::get('key.to.config'); // 输出 "hello"
```

**重要** 请 **不要** 创建重名的配置文件（例如默认的 `app.php`）。

## 2.5. 模块化
代码复用，模块化，都是这么讲，可是真正实施起来还是有难度的，  
你是不是把“模块”在不同项目之间复制来复制去？

Phwoolcon 这样解决：

* 首先把项目本身做成一个 `Phwoolcon 包`；
* 接下来把你各个项目里面的公用组件也都做成 `Phwoolcon 包`；
* 然后把它们加到项目包的 `composer.json` 里面。

**重要** 所有私有仓库都 **必须** 在 `composer.local.json` 文件（步骤[2.2.](#s2.2)创建的）的  
`repositories` 章节声明，否则 `composer` 找不到它们。

## 2.6. 构建 / 部署
部署 composer 项目是一件痛苦的事情，因为：

* 如果你依赖 `composer update`，你可能部署了不一致的代码；
* 如果你把 `vendor` 放进版本控制系统，在开发过程中要么和  
composer 冲突，要么干脆放弃更新；
* `composer update` 真他妈慢，而且可能会失败。

Phwoolcon 解决部署问题的方法是，把工作目录打包，然后放进 `release` 分支。

### 2.6.1. 构建
```bash
bin/build
```

build 脚本会在工作目录下创建一个 `ignore/release` 目录，  
这个目录里的内容是可以直接推到生产环境上的。

把这个目录加入你的项目仓库，放到 `release` 分支下。

### 2.6.2. 部署
让我们用 `rsync` 作例子：
```bash
rsync -auv --delete --chown=www-data:www-data --rsync-path='sudo rsync' \
    --exclude-from=./deployignore ./ \
    user@production-host:/path/to/production/directory/
ssh www-data@production-host \
    '/path/to/production/directory/bin/dump-autoload'
```

# 3. 主旨
* 关注性能
    * 这就是为什么用 Phalcon 和 Swoole
* 关注伸缩性
    * 构建跨机房分布式系统的潜力（还在开发中）
* 提供强大的功能，但是保持直观易读的代码
    * Keep it simple and do it right
* 模块化实施
    * 再也不在 `app/` 里写代码，用 composer 包代替
* 部署友好
    * 再也不用在生产环境上面跑 `composer update`

# 4. 功能

## 4.1. 基础组件
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

## 4.2. 特定组件
* Admin interface
* User authentication (Register and login)
* SSO Server and client
* Orders
* Payment

## 5. 附录
> 软件供应商在努力尝试让他们的软件更“易于操作”。迄今为止，他们最好的办法就是，翻出所有的老手册，然后在封面盖上“易于操作”这几个字。
>  
> <div align="right">— 比尔·盖茨</div>

我也是。
