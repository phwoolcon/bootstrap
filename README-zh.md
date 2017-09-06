# Phwoolcon Bootstrap

Phalcon + Swoole

[Demo](https://phwoolcon.org/)

***

Bootstrap 用于提供 Phwoolcon 运行时的目录结构。

本项目的目的是创建一个高性能的 Web 应用程序，既可以运行于传统的 php-fpm
模式下，也可以运行在服务模式下。

在服务模式中，你的应用程序可以减少许多非必要的重复计算，获得极致的性能。

如果在服务模式中出现了 Bug，你可以轻松地关闭服务模式，损失一些性能
（但是仍然很快）换取稳定性，待 Bug 修复后再启用服务模式。

# 1. 系统要求
* Linux 或者 MacOS（不推荐 Windows，也没测试过。
不过你可以在 Windows 上用 VirtualBox 之类的虚拟机安装 Linux）
* Nginx（推荐最新版本）
* PHP version >= 5.5（推荐 7.1，2017 年注）
* PHP 组件: fpm, gd, cli, curl, dev, json, mbstring, pdo-mysql, redis, xml, zip
* MySQL server (或者 MariaDB / Percona / TiDB)
* Phalcon（推荐最新版本）
* Swoole（推荐最新版本）
* Composer（推荐最新版本）

# 2. 使用
## 2.1. 创建工作目录
```bash
git clone git@github.com:phwoolcon/bootstrap.git my-project-name
cd my-project-name
```

<a name="s2.2"></a>
## 2.2. 导入 Composer 包
请 **不要** 直接编辑 `composer.json`，这样会使你无法获取架的更新。

请使用 `bin/import-package` 创建 `composer.local-*.json` 来导入依赖。
`composer.local-*.json` 与框架是隔离的。

例如：

* 导入一个公开的 composer 包：
```bash
bin/import-package some/public-package
```

* 导入一个私有的 composer 包：
```bash
bin/import-package git@git.example.com:my/private-project.git
```

关于 `composer.local-*.json` 的更多详情请查看 [Composer Merge Plugin (by Wikimedia)](https://github.com/wikimedia/composer-merge-plugin/blob/master/README.md#plugin-configuration)。

Demo: [Phwoolcon Demo](https://github.com/phwoolcon/demo#7-install-phwoolcondemo).

## 2.3. 组织你的项目代码
把所有项目代码都用 `composer 包` 来组织。

**绝对不要** 把你的代码放进 `app/` 目录里面，这样非常难以实施模块化。

<a name="s2.3.1"></a>
### 2.3.1 创建一个 Phwoolcon 包
运行：
```bash
bin/cli package:create
```
这个工具会问你填一些基本信息，像这样：

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

然后你在 `vendor` 目录下可以找到一个新包，并且已经添加了  
git 远程仓库，就等你 commit 和 push 了。

```bash
git commit -m "Initial commit"
git push
```

现在你拥有了一个私有的 composer 仓库，你的第一个 `Phwoolcon 包`。

如果你乐于分享，你可以把它发布到 [GitHub](https://github.com) 和 [Packagist](https://packagist.org) 上。

### 2.3.2. 导入你的项目
现在你可以把刚才创建的 Phwoolcon 包导入进来了。

参阅 [2.2. 导入 Composer 包](#s2.2)

### 2.3.3. 更新代码
```bash
bin/update
```
这个脚本会做以下工作：

* `git pull` 更新框架 (`phwoolcon/bootstrap`) 本身；
* `composer update` 更新所有 composer 包，包括你的项目；
* `bin/cli migrate:up` 运行数据库更新脚本；
* `bin/dump-autoload` 更新 composer autoload，应用最新的  
assets，配置文件，翻译文件，生成 model trait 和 IDE helper。

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

**重要** 所有私有仓库都 **必须** 在 `composer.local.json` 文件（参见步骤 [2.3.1 创建一个 Phwoolcon 包](#s2.3.1)）的
`repositories` 中声明，否则 `composer` 找不到它们。

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
#### 2.6.2.1. 手动部署

让我们用 `rsync` 作例子：
```bash
rsync -auv --delete --chown=www-data:www-data --rsync-path='sudo rsync' \
    --exclude-from=./deployignore ./ \
    user@production-host:/path/to/production/directory/
ssh www-data@production-host \
    '/path/to/production/directory/bin/dump-autoload'
```

#### 2.6.2.2. 自动部署
请见 [Deploy Automator](https://github.com/phwoolcon/deploy-automator)

## 2.7. 服务模式

### 2.7.1. 启用服务模式
要启用服务模式，请在 CGI 参数中设置 `USE_SERVICE` 为 1。
```conf
    location ~ \.php$ {
        .
        .
        .
        fastcgi_param USE_SERVICE 1;
    }
```

### 2.7.2. 启动/停止服务
启动服务请运行：
```bash
bin/cli service start
```
现在 phwoolcon 服务将处理你的网站请求。

停止服务请运行：
```bash
bin/cli service stop
```
现在你的网站仍然在 php-fpm 模式下可用。

### 2.7.3. 安装为系统服务（未完成）
运行这个命令把你的网站安装为一个系统服务：
```bash
bin/cli service install
```
然后你可以 start/stop/restart/reload 你的服务：
```bash
service phwoolcon start
service phwoolcon stop
service phwoolcon restart
service phwoolcon reload
```
卸载系统服务：
```bash
bin/cli service uninstall
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

## 5. 许可证
Phwoolcon Bootstrap 采用 [Apache License 2.0](LICENSE) 进行许可

### 5.1. 第三方软件
Phwoolcon Bootstrap 使用了第三方库和其他资源，它们可能采用了与 Phwoolcon  
不同的许可证进行发布，详情请见 [Third Party Licenses](3RD-PARTY-LICENSES.md)。
