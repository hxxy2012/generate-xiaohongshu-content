# RedBookAI 详细安装文档

> 本文档提供RedBookAI系统的详细安装步骤，适用于初学者和有经验的开发者。

## 📋 目录

- [环境要求](#环境要求)
- [安装准备](#安装准备)
- [Windows环境安装](#windows环境安装)
- [macOS环境安装](#macos环境安装)
- [Ubuntu/Debian环境安装](#ubuntudebian环境安装)
- [CentOS/RHEL环境安装](#centosrhel环境安装)
- [Docker部署](#docker部署)
- [配置详解](#配置详解)
- [安装验证](#安装验证)
- [常见问题](#常见问题)

---

## 环境要求

### 必需软件

| 软件 | 版本要求 | 用途 |
|------|---------|------|
| PHP | >= 8.0 | 应用运行环境 |
| MySQL | >= 8.0 | 数据库 |
| Redis | >= 6.0 | 缓存和队列 |
| Composer | >= 2.0 | PHP依赖管理 |
| Nginx/Apache | 最新稳定版 | Web服务器 |

### PHP扩展要求

必需扩展：
```
✓ pdo_mysql    - 数据库连接
✓ redis        - Redis支持
✓ gd           - 图片处理
✓ mbstring     - 多字节字符串
✓ json         - JSON处理
✓ openssl      - 加密支持
✓ curl         - HTTP请求
✓ zip          - 压缩文件
✓ xml          - XML处理
✓ bcmath       - 高精度数学
```

### 服务器资源要求

**最低配置**：
- CPU：1核
- 内存：2GB
- 磁盘：10GB
- 带宽：1Mbps

**推荐配置**：
- CPU：2核+
- 内存：4GB+
- 磁盘：50GB+（SSD）
- 带宽：5Mbps+

### 网络要求

- 需要访问Google API（Gemini API）
- 如果在中国大陆，可能需要配置代理

---

## 安装准备

### 1. 检查系统环境

#### 检查PHP版本
```bash
php -v
# 应该显示 PHP 8.0.x 或更高版本
```

#### 检查PHP扩展
```bash
php -m
# 检查上述必需扩展是否已安装
```

#### 检查MySQL
```bash
mysql --version
# 应该显示 MySQL 8.0.x 或更高版本
```

#### 检查Redis
```bash
redis-cli ping
# 应该返回 PONG
```

#### 检查Composer
```bash
composer --version
# 应该显示 Composer version 2.x.x
```

### 2. 获取Gemini API Key

1. 访问 [Google AI Studio](https://makersuite.google.com/app/apikey)
2. 使用Google账号登录
3. 点击 **Create API Key**
4. 复制生成的API Key（格式：AIzaSy...）
5. 妥善保存，稍后配置时需要使用

**注意事项**：
- API Key是免费的，但有使用限制
- 每分钟请求数有限制
- 建议创建多个Key轮换使用

### 3. 准备服务器

#### 创建项目目录
```bash
# 创建项目目录
sudo mkdir -p /var/www/redbook-ai
sudo chown -R $USER:$USER /var/www/redbook-ai
cd /var/www/redbook-ai
```

#### 下载项目源码

**方式一：使用Git克隆**
```bash
git clone <your-repository-url> .
```

**方式二：下载ZIP包**
```bash
# 下载ZIP包
wget https://github.com/your-repo/redbook-ai/archive/main.zip
unzip main.zip
mv redbook-ai-main/* .
```

**方式三：手动上传**
- 使用FTP/SFTP工具上传项目文件到服务器

---

## Windows环境安装

### 步骤1：安装PHP

#### 1.1 下载PHP

1. 访问 [PHP官网](https://windows.php.net/download/)
2. 下载 **PHP 8.1 VC15 x64 Thread Safe** ZIP包
3. 解压到 `C:\php`

#### 1.2 配置PHP

1. 复制 `php.ini-development` 为 `php.ini`
2. 编辑 `php.ini`，启用扩展：

```ini
; 找到以下行并去掉前面的分号
extension=curl
extension=gd
extension=mbstring
extension=mysqli
extension=pdo_mysql
extension=openssl
extension=redis
extension=zip
extension=bcmath

; 设置时区
date.timezone = Asia/Shanghai

; 设置内存限制
memory_limit = 256M

; 设置上传文件大小
upload_max_filesize = 10M
post_max_size = 10M
```

3. 添加PHP到环境变量：
   - 右键"此电脑" → 属性 → 高级系统设置
   - 环境变量 → 系统变量 → Path
   - 新建 → 添加 `C:\php`

4. 验证安装：
```cmd
php -v
```

### 步骤2：安装MySQL

#### 2.1 下载MySQL

1. 访问 [MySQL官网](https://dev.mysql.com/downloads/installer/)
2. 下载 **MySQL Installer for Windows**
3. 选择 **mysql-installer-community-8.0.xx.msi**

#### 2.2 安装MySQL

1. 运行安装程序
2. 选择 **Developer Default** 或 **Custom**
3. 配置选项：
   - Type and Networking：默认端口3306
   - Authentication Method：Use Strong Password Encryption
   - Accounts and Roles：设置root密码（请记住！）
4. 完成安装

#### 2.3 验证MySQL

```cmd
mysql -u root -p
# 输入密码后应能成功登录
```

### 步骤3：安装Redis

#### 3.1 下载Redis

Redis官方不支持Windows，使用第三方版本：

1. 访问 [Redis Windows版](https://github.com/tporadowski/redis/releases)
2. 下载最新的 `.msi` 安装包
3. 运行安装程序

#### 3.2 启动Redis

```cmd
# Redis会自动作为Windows服务启动
redis-cli ping
# 应返回 PONG
```

### 步骤4：安装Composer

1. 访问 [Composer官网](https://getcomposer.org/download/)
2. 下载 **Composer-Setup.exe**
3. 运行安装程序
4. 安装时会自动检测PHP路径
5. 验证安装：

```cmd
composer --version
```

### 步骤5：安装项目

#### 5.1 进入项目目录

```cmd
cd C:\www\redbook-ai
```

#### 5.2 安装依赖

```cmd
composer install
```

#### 5.3 配置环境变量

```cmd
copy .env.example .env
```

编辑 `.env` 文件：

```ini
APP_DEBUG = true

[DATABASE]
TYPE = mysql
HOSTNAME = 127.0.0.1
DATABASE = redbook_ai
USERNAME = root
PASSWORD = your_mysql_password
HOSTPORT = 3306
PREFIX = rb_

[REDIS]
HOST = 127.0.0.1
PORT = 6379
PASSWORD =

[GEMINI]
API_KEY = your_gemini_api_key_here
MODEL = gemini-1.5-flash
```

#### 5.4 创建数据库

```cmd
# 登录MySQL
mysql -u root -p

# 创建数据库
CREATE DATABASE redbook_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# 导入数据库结构
mysql -u root -p redbook_ai < database.sql

# 导入模板数据
mysql -u root -p redbook_ai < init_templates.sql
```

#### 5.5 设置目录权限

```cmd
# Windows下可能需要设置目录的写入权限
icacls runtime /grant Everyone:F /T
icacls public\uploads /grant Everyone:F /T
```

### 步骤6：启动服务

```cmd
# 启动PHP内置服务器
php think run

# 或指定端口
php think run -p 8080
```

访问：`http://localhost:8000`

---

## macOS环境安装

### 步骤1：安装Homebrew（如未安装）

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### 步骤2：安装PHP 8.1

```bash
# 安装PHP
brew install php@8.1

# 链接PHP
brew link php@8.1 --force

# 验证版本
php -v
```

### 步骤3：安装扩展

```bash
# 安装Redis扩展
pecl install redis

# 编辑php.ini
echo "extension=redis.so" >> $(php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||")
```

### 步骤4：安装MySQL

```bash
# 安装MySQL
brew install mysql

# 启动MySQL
brew services start mysql

# 安全配置
mysql_secure_installation
```

### 步骤5：安装Redis

```bash
# 安装Redis
brew install redis

# 启动Redis
brew services start redis
```

### 步骤6：安装Composer

```bash
brew install composer
```

### 步骤7：安装项目

```bash
# 进入项目目录
cd /Users/yourname/Sites/redbook-ai

# 安装依赖
composer install

# 配置环境
cp .env.example .env
nano .env

# 创建数据库
mysql -u root -p
CREATE DATABASE redbook_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# 导入数据
mysql -u root -p redbook_ai < database.sql
mysql -u root -p redbook_ai < init_templates.sql

# 设置权限
chmod -R 755 runtime
chmod -R 755 public/uploads

# 启动服务
php think run
```

---

## Ubuntu/Debian环境安装

### 完整安装步骤

#### 步骤1：更新系统

```bash
sudo apt update
sudo apt upgrade -y
```

#### 步骤2：安装PHP 8.1及扩展

```bash
# 添加PHP仓库
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# 安装PHP 8.1及所有必需扩展
sudo apt install -y \
    php8.1-fpm \
    php8.1-cli \
    php8.1-mysql \
    php8.1-redis \
    php8.1-gd \
    php8.1-mbstring \
    php8.1-xml \
    php8.1-curl \
    php8.1-zip \
    php8.1-bcmath \
    php8.1-intl

# 验证安装
php -v
php -m | grep -E "gd|mysql|redis"
```

#### 步骤3：安装MySQL 8.0

```bash
# 安装MySQL
sudo apt install -y mysql-server

# 启动MySQL
sudo systemctl start mysql
sudo systemctl enable mysql

# 安全配置
sudo mysql_secure_installation
```

配置选项：
- VALIDATE PASSWORD COMPONENT: **Y**
- Password validation policy: **2** (Strong)
- New password: **输入强密码**
- Remove anonymous users: **Y**
- Disallow root login remotely: **Y**
- Remove test database: **Y**
- Reload privilege tables: **Y**

#### 步骤4：安装Redis

```bash
# 安装Redis
sudo apt install -y redis-server

# 启动Redis
sudo systemctl start redis-server
sudo systemctl enable redis-server

# 验证
redis-cli ping
```

#### 步骤5：安装Nginx

```bash
# 安装Nginx
sudo apt install -y nginx

# 启动Nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

#### 步骤6：安装Composer

```bash
# 下载Composer
curl -sS https://getcomposer.org/installer | php

# 移动到系统路径
sudo mv composer.phar /usr/local/bin/composer

# 验证
composer --version
```

#### 步骤7：创建项目目录

```bash
# 创建项目目录
sudo mkdir -p /var/www/redbook-ai
cd /var/www/redbook-ai

# 克隆项目（或上传文件）
git clone <your-repo-url> .

# 设置所有者
sudo chown -R www-data:www-data /var/www/redbook-ai
sudo chown -R $USER:www-data /var/www/redbook-ai
```

#### 步骤8：安装项目依赖

```bash
cd /var/www/redbook-ai

# 安装Composer依赖
composer install --no-dev
```

#### 步骤9：配置环境变量

```bash
# 复制配置文件
cp .env.example .env

# 编辑配置
nano .env
```

填入以下内容：

```ini
APP_DEBUG = false

[DATABASE]
TYPE = mysql
HOSTNAME = 127.0.0.1
DATABASE = redbook_ai
USERNAME = redbook_user
PASSWORD = your_strong_password_here
HOSTPORT = 3306
PREFIX = rb_

[REDIS]
HOST = 127.0.0.1
PORT = 6379
PASSWORD =

[GEMINI]
API_KEY = your_gemini_api_key_here
MODEL = gemini-1.5-flash
TIMEOUT = 60
```

保存并退出（Ctrl+X, Y, Enter）

#### 步骤10：创建数据库

```bash
# 登录MySQL
sudo mysql -u root -p

# 在MySQL中执行
CREATE DATABASE redbook_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'redbook_user'@'localhost' IDENTIFIED BY 'your_strong_password_here';

GRANT ALL PRIVILEGES ON redbook_ai.* TO 'redbook_user'@'localhost';

FLUSH PRIVILEGES;

EXIT;
```

#### 步骤11：导入数据库

```bash
# 导入数据库结构
mysql -u redbook_user -p redbook_ai < database.sql

# 导入模板数据
mysql -u redbook_user -p redbook_ai < init_templates.sql

# 验证导入
mysql -u redbook_user -p -e "USE redbook_ai; SHOW TABLES;"
```

#### 步骤12：设置目录权限

```bash
# 设置所有者
sudo chown -R www-data:www-data /var/www/redbook-ai

# 设置权限
sudo chmod -R 755 /var/www/redbook-ai
sudo chmod -R 775 /var/www/redbook-ai/runtime
sudo chmod -R 775 /var/www/redbook-ai/public/uploads

# 验证权限
ls -la /var/www/redbook-ai
```

#### 步骤13：配置Nginx

```bash
# 创建Nginx配置
sudo nano /etc/nginx/sites-available/redbook-ai
```

添加以下内容：

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/redbook-ai/public;
    index index.php index.html;

    # 日志
    access_log /var/log/nginx/redbook-ai-access.log;
    error_log /var/log/nginx/redbook-ai-error.log;

    # 隐藏版本号
    server_tokens off;

    # 最大上传大小
    client_max_body_size 20M;

    # 主要路由
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP处理
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # 禁止访问隐藏文件
    location ~ /\. {
        deny all;
    }

    # 禁止访问敏感目录
    location ~* /(runtime|config|app)/ {
        deny all;
    }

    # 静态文件缓存
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

保存并退出。

```bash
# 启用站点
sudo ln -s /etc/nginx/sites-available/redbook-ai /etc/nginx/sites-enabled/

# 测试配置
sudo nginx -t

# 重启Nginx
sudo systemctl reload nginx
```

#### 步骤14：配置PHP-FPM

```bash
# 编辑PHP-FPM配置
sudo nano /etc/php/8.1/fpm/pool.d/www.conf
```

调整以下参数（可选，优化性能）：

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
```

```bash
# 重启PHP-FPM
sudo systemctl restart php8.1-fpm
```

#### 步骤15：配置防火墙

```bash
# 允许HTTP和HTTPS
sudo ufw allow 'Nginx Full'

# 允许SSH（如需要）
sudo ufw allow OpenSSH

# 启用防火墙
sudo ufw enable

# 查看状态
sudo ufw status
```

#### 步骤16：配置SSL（推荐）

```bash
# 安装Certbot
sudo apt install -y certbot python3-certbot-nginx

# 获取证书（将your-domain.com替换为实际域名）
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# 测试自动续期
sudo certbot renew --dry-run
```

#### 步骤17：验证安装

```bash
# 检查所有服务状态
sudo systemctl status nginx
sudo systemctl status php8.1-fpm
sudo systemctl status mysql
sudo systemctl status redis-server

# 访问网站
curl -I http://localhost
# 或在浏览器访问 http://your-domain.com
```

---

## CentOS/RHEL环境安装

### 步骤1：更新系统

```bash
sudo yum update -y
```

### 步骤2：安装EPEL和Remi仓库

```bash
# 安装EPEL
sudo yum install -y epel-release

# 安装Remi仓库
sudo yum install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm

# 启用PowerTools（CentOS 8）
sudo yum install -y yum-utils
sudo yum-config-manager --enable powertools
```

### 步骤3：安装PHP 8.1

```bash
# 重置PHP模块
sudo yum module reset php

# 启用PHP 8.1
sudo yum module enable php:remi-8.1 -y

# 安装PHP及扩展
sudo yum install -y \
    php \
    php-fpm \
    php-cli \
    php-mysqlnd \
    php-redis \
    php-gd \
    php-mbstring \
    php-xml \
    php-json \
    php-zip \
    php-bcmath \
    php-intl

# 启动PHP-FPM
sudo systemctl start php-fpm
sudo systemctl enable php-fpm
```

### 步骤4：安装MySQL

```bash
# 下载MySQL仓库
wget https://dev.mysql.com/get/mysql80-community-release-el8-1.noarch.rpm

# 安装仓库
sudo rpm -ivh mysql80-community-release-el8-1.noarch.rpm

# 安装MySQL
sudo yum install -y mysql-server

# 启动MySQL
sudo systemctl start mysqld
sudo systemctl enable mysqld

# 获取临时密码
sudo grep 'temporary password' /var/log/mysqld.log

# 安全配置
sudo mysql_secure_installation
```

### 步骤5：安装Redis

```bash
# 安装Redis
sudo yum install -y redis

# 启动Redis
sudo systemctl start redis
sudo systemctl enable redis
```

### 步骤6：安装Nginx

```bash
# 安装Nginx
sudo yum install -y nginx

# 启动Nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

### 步骤7：安装Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 步骤8-17：同Ubuntu步骤

其余步骤与Ubuntu相同，请参考上方Ubuntu安装步骤的8-17步。

---

## Docker部署

### 创建Dockerfile

```dockerfile
FROM php:8.1-fpm

# 安装系统依赖
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx

# 安装PHP扩展
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 安装Redis扩展
RUN pecl install redis && docker-php-ext-enable redis

# 安装Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 设置工作目录
WORKDIR /var/www/redbook-ai

# 复制项目文件
COPY . /var/www/redbook-ai

# 安装依赖
RUN composer install --no-dev --optimize-autoloader

# 设置权限
RUN chown -R www-data:www-data /var/www/redbook-ai
RUN chmod -R 755 /var/www/redbook-ai

EXPOSE 9000

CMD ["php-fpm"]
```

### 创建docker-compose.yml

```yaml
version: '3.8'

services:
  app:
    build: .
    container_name: redbook-ai-app
    restart: unless-stopped
    working_dir: /var/www/redbook-ai
    volumes:
      - ./:/var/www/redbook-ai
    networks:
      - redbook-network

  nginx:
    image: nginx:alpine
    container_name: redbook-ai-nginx
    restart: unless-stopped
    ports:
      - "80:80"
    volumes:
      - ./:/var/www/redbook-ai
      - ./docker/nginx:/etc/nginx/conf.d
    networks:
      - redbook-network

  mysql:
    image: mysql:8.0
    container_name: redbook-ai-mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: redbook_ai
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_USER: redbook_user
      MYSQL_PASSWORD: user_password
    volumes:
      - mysql-data:/var/lib/mysql
      - ./database.sql:/docker-entrypoint-initdb.d/01-schema.sql
      - ./init_templates.sql:/docker-entrypoint-initdb.d/02-data.sql
    networks:
      - redbook-network

  redis:
    image: redis:alpine
    container_name: redbook-ai-redis
    restart: unless-stopped
    networks:
      - redbook-network

networks:
  redbook-network:
    driver: bridge

volumes:
  mysql-data:
```

### 启动Docker

```bash
# 构建并启动
docker-compose up -d

# 查看日志
docker-compose logs -f

# 停止
docker-compose down
```

---

## 配置详解

### .env文件完整配置

```ini
# =====================================================
# RedBookAI 环境配置文件
# =====================================================

# 调试模式（生产环境设为false）
APP_DEBUG = false

# =====================================================
# 应用配置
# =====================================================
[APP]
DEFAULT_TIMEZONE = Asia/Shanghai

# =====================================================
# 数据库配置
# =====================================================
[DATABASE]
TYPE = mysql
HOSTNAME = 127.0.0.1
DATABASE = redbook_ai
USERNAME = redbook_user
PASSWORD = your_strong_password_here
HOSTPORT = 3306
CHARSET = utf8mb4
PREFIX = rb_

# =====================================================
# Redis配置
# =====================================================
[REDIS]
HOST = 127.0.0.1
PORT = 6379
PASSWORD =
SELECT = 0
QUEUE_SELECT = 1

# =====================================================
# 缓存配置
# =====================================================
[CACHE]
DRIVER = redis

# =====================================================
# 队列配置
# =====================================================
[QUEUE]
DRIVER = redis

# =====================================================
# Gemini API配置（重要！）
# =====================================================
[GEMINI]
# API Key（必填）- 在 https://makersuite.google.com/app/apikey 获取
API_KEY = AIzaSyxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

# API地址（通常不需要修改）
API_URL = https://generativelanguage.googleapis.com/v1beta

# 模型选择
# - gemini-1.5-pro: 更智能，但慢且贵
# - gemini-1.5-flash: 快速且经济（推荐）
MODEL = gemini-1.5-flash

# 请求超时时间（秒）
TIMEOUT = 60

# 最大重试次数
MAX_RETRIES = 3

# 温度参数（0-2，越高越随机）
TEMPERATURE = 0.9

# =====================================================
# Session配置
# =====================================================
[SESSION]
TYPE = redis
EXPIRE = 86400
PREFIX = redbook_session_

# =====================================================
# 文件上传配置
# =====================================================
[UPLOAD]
MAX_SIZE = 10485760
ALLOWED_EXTS = jpg,jpeg,png,gif
```

### 数据库配置说明

**创建数据库用户**：

```sql
-- 创建数据库
CREATE DATABASE redbook_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 创建用户
CREATE USER 'redbook_user'@'localhost' IDENTIFIED BY 'your_strong_password';

-- 授权
GRANT ALL PRIVILEGES ON redbook_ai.* TO 'redbook_user'@'localhost';

-- 刷新权限
FLUSH PRIVILEGES;
```

**强密码要求**：
- 至少8个字符
- 包含大小写字母
- 包含数字
- 包含特殊字符

示例：`Rb@2024#Secure!`

---

## 安装验证

### 检查清单

#### 1. 系统服务检查

```bash
# 检查PHP
php -v
php -m | grep -E "gd|mysql|redis|mbstring"

# 检查MySQL
mysql -u redbook_user -p -e "SELECT VERSION();"

# 检查Redis
redis-cli ping

# 检查Web服务器
sudo systemctl status nginx
# 或
sudo systemctl status apache2
```

#### 2. 数据库验证

```bash
# 登录数据库
mysql -u redbook_user -p

# 检查表
USE redbook_ai;
SHOW TABLES;

# 应该看到以下表：
# rb_user
# rb_role
# rb_permission
# rb_role_permission
# rb_content
# rb_template
# rb_image
# rb_api_log
# rb_points_log
# rb_order
# rb_config

# 检查模板数据
SELECT COUNT(*) FROM rb_template;
# 应该返回 10

# 退出
EXIT;
```

#### 3. 文件权限验证

```bash
# 检查runtime目录
ls -la runtime/
# 应该可写（775或777）

# 检查uploads目录
ls -la public/uploads/
# 应该可写（775或777）
```

#### 4. 访问测试

**方式一：命令行测试**

```bash
# 测试首页
curl -I http://localhost
# 应返回 200 OK

# 测试API（注册接口）
curl -X POST http://localhost/auth/register \
  -H "Content-Type: application/json" \
  -d '{"username":"testuser","password":"123456","password_confirm":"123456"}'
# 应返回JSON响应
```

**方式二：浏览器测试**

1. 打开浏览器
2. 访问 `http://your-domain.com` 或 `http://localhost`
3. 应该看到网站首页
4. 尝试注册新用户
5. 尝试登录

#### 5. 功能测试

**测试用户注册**：
```bash
curl -X POST http://localhost/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "username": "testuser",
    "password": "test123456",
    "password_confirm": "test123456",
    "email": "test@example.com"
  }'
```

预期返回：
```json
{
  "code": 200,
  "msg": "注册成功",
  "data": {
    "user_id": 2
  }
}
```

**测试登录**：
```bash
curl -X POST http://localhost/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "account": "testuser",
    "password": "test123456"
  }'
```

预期返回：
```json
{
  "code": 200,
  "msg": "登录成功",
  "data": {
    "user": {
      "id": 2,
      "username": "testuser",
      ...
    }
  }
}
```

#### 6. Gemini API测试

**测试API Key是否有效**：

```bash
curl -X POST "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "contents": [{
      "parts":[{"text": "Hello"}]
    }]
  }'
```

如果返回JSON响应（非错误），说明API Key有效。

#### 7. 日志检查

```bash
# 查看应用日志
tail -f runtime/log/$(date +%Y%m%d).log

# 查看Nginx日志
sudo tail -f /var/log/nginx/redbook-ai-error.log

# 查看PHP错误日志
sudo tail -f /var/log/php8.1-fpm.log
```

---

## 常见问题

### 问题1：PHP版本不对

**症状**：
```
PHP Warning: This package requires PHP version...
```

**解决方案**：

Ubuntu/Debian:
```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.1
```

CentOS:
```bash
sudo yum module reset php
sudo yum module enable php:remi-8.1
sudo yum install php81
```

### 问题2：缺少PHP扩展

**症状**：
```
Extension gd is missing
```

**解决方案**：

Ubuntu/Debian:
```bash
sudo apt install php8.1-gd
sudo systemctl restart php8.1-fpm
```

CentOS:
```bash
sudo yum install php-gd
sudo systemctl restart php-fpm
```

验证：
```bash
php -m | grep gd
```

### 问题3：Composer安装失败

**症状**：
```
Failed to download composer
```

**解决方案**：

使用国内镜像：
```bash
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
```

或使用手动下载：
```bash
wget https://getcomposer.org/composer-stable.phar
chmod +x composer-stable.phar
sudo mv composer-stable.phar /usr/local/bin/composer
```

### 问题4：数据库连接失败

**症状**：
```
SQLSTATE[HY000] [2002] Connection refused
```

**解决方案**：

1. 检查MySQL是否运行：
```bash
sudo systemctl status mysql
```

2. 检查连接信息是否正确：
```bash
mysql -h 127.0.0.1 -u redbook_user -p
```

3. 检查防火墙：
```bash
sudo ufw allow 3306/tcp
```

4. 检查bind-address配置：
```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
# 确保 bind-address = 127.0.0.1
```

### 问题5：Redis连接失败

**症状**：
```
Connection refused (redis)
```

**解决方案**：

1. 启动Redis：
```bash
sudo systemctl start redis
```

2. 检查Redis配置：
```bash
sudo nano /etc/redis/redis.conf
# 确保 bind 127.0.0.1
# 确保 protected-mode yes
```

3. 测试连接：
```bash
redis-cli ping
```

### 问题6：权限错误

**症状**：
```
Permission denied: runtime/log/xxx.log
```

**解决方案**：

```bash
# 设置正确的所有者
sudo chown -R www-data:www-data /var/www/redbook-ai

# 设置正确的权限
sudo chmod -R 755 /var/www/redbook-ai
sudo chmod -R 775 /var/www/redbook-ai/runtime
sudo chmod -R 775 /var/www/redbook-ai/public/uploads
```

### 问题7：500错误

**症状**：
浏览器显示"500 Internal Server Error"

**解决方案**：

1. 检查PHP错误日志：
```bash
sudo tail -f /var/log/php8.1-fpm.log
```

2. 开启调试模式（`.env`）：
```ini
APP_DEBUG = true
```

3. 检查.env文件是否正确：
```bash
cat .env
```

4. 清除缓存：
```bash
rm -rf runtime/cache/*
```

### 问题8：Gemini API调用失败

**症状**：
```
Gemini API Key未配置
或
请求失败：403 Forbidden
```

**解决方案**：

1. 检查API Key是否正确配置：
```bash
grep GEMINI_API_KEY .env
```

2. 验证API Key：
```bash
curl "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=YOUR_KEY" \
  -H "Content-Type: application/json" \
  -d '{"contents":[{"parts":[{"text":"test"}]}]}'
```

3. 检查网络访问：
```bash
ping generativelanguage.googleapis.com
```

4. 如果在中国大陆，可能需要配置代理。

### 问题9：图片生成失败

**症状**：
```
生成图片失败：GD库错误
```

**解决方案**：

1. 检查GD扩展：
```bash
php -m | grep gd
```

2. 检查字体文件：
```bash
# 检查系统字体
fc-list | grep -i "dejavu\|noto"

# 如果没有，安装字体
sudo apt install fonts-dejavu fonts-noto-cjk
```

3. 检查目录权限：
```bash
ls -la public/uploads/images/
```

### 问题10：Nginx 403错误

**症状**：
```
403 Forbidden
```

**解决方案**：

1. 检查Nginx配置中的root路径：
```bash
sudo nano /etc/nginx/sites-available/redbook-ai
# 确保 root /var/www/redbook-ai/public;
```

2. 检查目录权限：
```bash
ls -la /var/www/redbook-ai/public/
```

3. 检查SELinux（CentOS）：
```bash
# 临时禁用
sudo setenforce 0

# 永久禁用
sudo nano /etc/selinux/config
# SELINUX=disabled
```

---

## 性能优化建议

### 1. PHP优化

编辑 `php.ini`：
```ini
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 20M
post_max_size = 20M

opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

### 2. MySQL优化

编辑 `/etc/mysql/mysql.conf.d/mysqld.cnf`：
```ini
max_connections = 200
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
query_cache_size = 32M
```

### 3. Redis优化

编辑 `/etc/redis/redis.conf`：
```ini
maxmemory 512mb
maxmemory-policy allkeys-lru
```

### 4. Nginx优化

编辑 `/etc/nginx/nginx.conf`：
```nginx
worker_processes auto;
worker_connections 2048;

gzip on;
gzip_vary on;
gzip_types text/plain text/css application/json application/javascript;
```

---

## 下一步

安装完成后，建议：

1. ✅ 修改默认管理员密码
2. ✅ 配置SSL证书（生产环境必须）
3. ✅ 设置定时任务（每日重置配额）
4. ✅ 配置备份策略
5. ✅ 监控系统性能
6. ✅ 阅读用户手册

---

## 获取帮助

如果遇到其他问题：

1. 查看 [常见问题文档](README.md#常见问题)
2. 查看 [用户手册](USER_MANUAL.md)
3. 提交 [GitHub Issue](https://github.com/your-repo/issues)
4. 发送邮件至：support@redbookai.com

---

**祝您安装顺利！** 🎉
