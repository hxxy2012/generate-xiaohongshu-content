# RedBookAI 快速安装指南

> 轻量级部署方案，无需Redis，简单快速

## 📋 环境要求

### 必需软件

| 软件 | 版本要求 | 用途 |
|------|---------|------|
| PHP | >= 8.0 | 应用运行环境 |
| MySQL | >= 8.0 | 数据库 |
| Composer | >= 2.0 | PHP依赖管理 |
| Nginx/Apache | 最新稳定版 | Web服务器 |

### PHP扩展要求

```bash
✓ pdo_mysql    - 数据库连接
✓ gd           - 图片处理
✓ mbstring     - 多字节字符串
✓ json         - JSON处理
✓ openssl      - 加密支持
✓ curl         - HTTP请求
✓ zip          - 压缩文件
✓ xml          - XML处理
```

### 检查PHP扩展

```bash
php -m | grep -E "pdo_mysql|gd|mbstring|json|openssl|curl|zip"
```

---

## 🚀 快速安装（3步完成）

### 步骤1：下载项目

```bash
# 克隆项目
git clone <repository-url>
cd generate-xiaohongshu-content

# 或下载ZIP解压
wget https://github.com/your-repo/archive/main.zip
unzip main.zip
```

### 步骤2：安装依赖

```bash
# 安装Composer依赖
composer install
```

### 步骤3：配置环境

```bash
# 复制配置文件
cp .env.example .env

# 编辑配置文件
nano .env
```

填入以下配置：

```ini
APP_DEBUG = true

[DATABASE]
TYPE = mysql
HOSTNAME = 127.0.0.1
DATABASE = redbook_ai
USERNAME = root
PASSWORD = your_password
HOSTPORT = 3306

[CACHE]
DRIVER = file

[GEMINI]
API_KEY = your_gemini_api_key_here
MODEL = gemini-1.5-flash
```

### 步骤4：初始化数据库

```bash
# 创建数据库
mysql -u root -p -e "CREATE DATABASE redbook_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 导入数据库结构
mysql -u root -p redbook_ai < database.sql

# 导入模板数据
mysql -u root -p redbook_ai < init_templates.sql
```

### 步骤5：设置权限

```bash
# Linux/macOS
chmod -R 755 runtime
chmod -R 755 public/uploads

# Windows
# 右键目录 -> 属性 -> 安全 -> 编辑权限，给予写入权限
```

### 步骤6：启动服务

```bash
# 使用PHP内置服务器（开发环境）
php think run

# 访问 http://localhost:8000
```

---

## 💻 不同系统的详细安装

### Windows系统

#### 1. 安装PHP

1. 下载 [PHP 8.1 for Windows](https://windows.php.net/download/)
2. 选择 **Thread Safe** 版本
3. 解压到 `C:\php`
4. 编辑 `php.ini`，启用扩展：

```ini
extension=curl
extension=gd
extension=mbstring
extension=mysqli
extension=pdo_mysql
extension=openssl
extension=zip
```

5. 添加到环境变量：`系统属性 -> 环境变量 -> Path -> 新建 -> C:\php`

#### 2. 安装MySQL

1. 下载 [MySQL Installer](https://dev.mysql.com/downloads/installer/)
2. 运行安装程序
3. 选择 **Developer Default**
4. 设置root密码（请记住！）

#### 3. 安装Composer

1. 下载 [Composer-Setup.exe](https://getcomposer.org/download/)
2. 运行安装，自动检测PHP路径
3. 完成安装

#### 4. 部署项目

```cmd
# 进入项目目录
cd C:\www\redbook-ai

# 安装依赖
composer install

# 配置环境
copy .env.example .env
notepad .env

# 创建数据库
mysql -u root -p
CREATE DATABASE redbook_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# 导入数据
mysql -u root -p redbook_ai < database.sql
mysql -u root -p redbook_ai < init_templates.sql

# 启动服务
php think run
```

---

### macOS系统

#### 1. 安装Homebrew（如未安装）

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

#### 2. 安装环境

```bash
# 安装PHP 8.1
brew install php@8.1
brew link php@8.1 --force

# 安装MySQL
brew install mysql
brew services start mysql

# 安装Composer
brew install composer

# 验证安装
php -v
mysql --version
composer --version
```

#### 3. 部署项目

```bash
# 进入项目目录
cd ~/Sites/redbook-ai

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
chmod -R 755 runtime public/uploads

# 启动服务
php think run
```

---

### Ubuntu/Debian系统

#### 完整安装脚本

```bash
#!/bin/bash
# RedBookAI 一键安装脚本（Ubuntu/Debian）

# 更新系统
sudo apt update && sudo apt upgrade -y

# 安装PHP 8.1
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.1-fpm php8.1-cli php8.1-mysql php8.1-gd \
    php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-bcmath

# 安装MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# 安装Nginx
sudo apt install -y nginx

# 安装Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# 创建项目目录
sudo mkdir -p /var/www/redbook-ai
cd /var/www/redbook-ai

# 克隆项目（请替换为实际URL）
# git clone <your-repo-url> .

# 安装依赖
composer install

# 配置环境
cp .env.example .env
# 请手动编辑 .env 文件配置数据库和API Key

# 创建数据库
sudo mysql -u root -p <<EOF
CREATE DATABASE redbook_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'redbook_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON redbook_ai.* TO 'redbook_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
EOF

# 导入数据库
mysql -u redbook_user -p redbook_ai < database.sql
mysql -u redbook_user -p redbook_ai < init_templates.sql

# 设置权限
sudo chown -R www-data:www-data /var/www/redbook-ai
sudo chmod -R 755 /var/www/redbook-ai
sudo chmod -R 775 /var/www/redbook-ai/runtime
sudo chmod -R 775 /var/www/redbook-ai/public/uploads

echo "安装完成！请配置Nginx虚拟主机"
```

#### Nginx配置

```bash
sudo nano /etc/nginx/sites-available/redbook-ai
```

添加以下内容：

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/redbook-ai/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }
}
```

启用站点：

```bash
sudo ln -s /etc/nginx/sites-available/redbook-ai /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

### CentOS/RHEL系统

```bash
# 更新系统
sudo yum update -y

# 安装EPEL和Remi仓库
sudo yum install -y epel-release
sudo yum install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm

# 启用PHP 8.1
sudo yum module reset php
sudo yum module enable php:remi-8.1 -y

# 安装PHP及扩展
sudo yum install -y php php-fpm php-cli php-mysqlnd php-gd \
    php-mbstring php-xml php-json php-zip php-bcmath

# 安装MySQL
wget https://dev.mysql.com/get/mysql80-community-release-el8-1.noarch.rpm
sudo rpm -ivh mysql80-community-release-el8-1.noarch.rpm
sudo yum install -y mysql-server
sudo systemctl start mysqld

# 安装Nginx
sudo yum install -y nginx

# 安装Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# 后续步骤同Ubuntu
```

---

## 🔧 配置说明

### 数据库配置

在`.env`文件中：

```ini
[DATABASE]
TYPE = mysql
HOSTNAME = 127.0.0.1          # 数据库地址
DATABASE = redbook_ai          # 数据库名
USERNAME = root                # 数据库用户名
PASSWORD = your_password       # 数据库密码
HOSTPORT = 3306               # 数据库端口
PREFIX = rb_                  # 表前缀
```

### Gemini API配置

1. 访问 [Google AI Studio](https://makersuite.google.com/app/apikey)
2. 登录Google账号
3. 点击 **Create API Key**
4. 复制API Key

在`.env`文件中：

```ini
[GEMINI]
API_KEY = AIzaSy...your_key_here    # 必填：你的API Key
MODEL = gemini-1.5-flash             # 推荐：快速且经济
TIMEOUT = 60                         # 超时时间（秒）
```

### 缓存配置

系统使用文件缓存，无需额外配置：

```ini
[CACHE]
DRIVER = file
```

缓存文件存储在 `runtime/cache/` 目录。

---

## ✅ 安装验证

### 1. 检查PHP扩展

```bash
php -m | grep -E "pdo_mysql|gd|mbstring"
# 应该看到这些扩展已启用
```

### 2. 检查数据库

```bash
mysql -u root -p
USE redbook_ai;
SHOW TABLES;
# 应该看到11张表
```

### 3. 访问网站

浏览器访问：`http://localhost:8000` 或 `http://your-domain.com`

### 4. 测试注册

```bash
curl -X POST http://localhost:8000/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "username": "testuser",
    "password": "test123456",
    "password_confirm": "test123456"
  }'
```

预期返回：

```json
{
  "code": 200,
  "msg": "注册成功"
}
```

### 5. 测试登录

```bash
curl -X POST http://localhost:8000/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "account": "testuser",
    "password": "test123456"
  }'
```

---

## ❓ 常见问题

### 1. 数据库连接失败

**症状**：`Connection refused`

**解决**：
```bash
# 检查MySQL是否运行
sudo systemctl status mysql

# 启动MySQL
sudo systemctl start mysql

# 检查连接
mysql -u root -p -h 127.0.0.1
```

### 2. 缺少PHP扩展

**症状**：`Extension xxx is missing`

**解决**：
```bash
# Ubuntu/Debian
sudo apt install php8.1-xxx

# CentOS
sudo yum install php-xxx

# 重启PHP-FPM
sudo systemctl restart php8.1-fpm
```

### 3. 权限错误

**症状**：`Permission denied`

**解决**：
```bash
# Linux/macOS
sudo chown -R www-data:www-data /var/www/redbook-ai
sudo chmod -R 755 /var/www/redbook-ai
sudo chmod -R 775 runtime public/uploads

# 验证权限
ls -la runtime/
```

### 4. Gemini API调用失败

**症状**：`API Key未配置` 或 `403 Forbidden`

**解决**：
1. 检查`.env`文件中API Key是否正确
2. 验证API Key：
```bash
curl "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=YOUR_KEY" \
  -H "Content-Type: application/json" \
  -d '{"contents":[{"parts":[{"text":"test"}]}]}'
```

### 5. 图片生成失败

**症状**：`GD库错误`

**解决**：
```bash
# 检查GD扩展
php -m | grep gd

# 如果没有，安装GD扩展
sudo apt install php8.1-gd

# 检查字体文件
fc-list | grep -i "dejavu"

# 如果没有，安装字体
sudo apt install fonts-dejavu fonts-noto-cjk
```

### 6. 500错误

**解决**：
```bash
# 开启调试模式查看详细错误
nano .env
# 设置 APP_DEBUG = true

# 查看错误日志
tail -f runtime/log/$(date +%Y%m%d).log

# 清除缓存
rm -rf runtime/cache/*
```

---

## 🎯 生产环境优化

### PHP优化

编辑 `php.ini`：

```ini
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 20M
post_max_size = 20M

; 开启OPcache
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

### MySQL优化

编辑 `my.cnf`：

```ini
[mysqld]
max_connections = 200
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
```

### Nginx优化

编辑 `nginx.conf`：

```nginx
worker_processes auto;
worker_connections 2048;

gzip on;
gzip_vary on;
gzip_types text/plain text/css application/json application/javascript;
```

---

## 🔒 安全建议

1. **修改默认密码**
```sql
UPDATE rb_user SET password = 'new_hash' WHERE username = 'admin';
```

2. **禁用调试模式**（生产环境）
```ini
APP_DEBUG = false
```

3. **配置防火墙**
```bash
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

4. **配置SSL证书**
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

---

## 📚 下一步

- ✅ 阅读 [用户手册](USER_MANUAL.md)
- ✅ 配置定时任务（每日重置配额）
- ✅ 设置备份策略
- ✅ 配置监控告警

---

## 🆘 获取帮助

- 📖 查看 [完整文档](README.md)
- 🐛 提交 [Issue](https://github.com/your-repo/issues)
- 💬 加入交流群

---

**安装完成，开始创作吧！** 🎉
