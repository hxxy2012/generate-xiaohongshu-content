# RedBookAI 部署文档

## 📋 部署前检查清单

### 服务器环境要求

- [ ] **操作系统**：Ubuntu 20.04+ / CentOS 7+ / Debian 10+
- [ ] **PHP版本**：>= 8.0
- [ ] **MySQL版本**：>= 8.0
- [ ] **内存**：至少 2GB RAM
- [ ] **磁盘空间**：至少 10GB
- [ ] **网络**：能访问Google API服务

**注意**：本系统使用文件缓存，无需Redis，简化部署！

### PHP扩展要求

```bash
# 检查已安装的扩展
php -m

# 必需扩展列表（无需Redis扩展）
✓ pdo_mysql
✓ gd
✓ mbstring
✓ json
✓ openssl
✓ curl
✓ zip
✓ xml
✓ bcmath
```

## 🚀 生产环境部署（Ubuntu 20.04）

### 步骤1：安装基础环境

#### 1.1 更新系统

```bash
sudo apt update
sudo apt upgrade -y
```

#### 1.2 安装Nginx

```bash
sudo apt install nginx -y
sudo systemctl start nginx
sudo systemctl enable nginx
```

#### 1.3 安装PHP 8.1及扩展

```bash
# 添加PHP仓库
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# 安装PHP 8.1及必需扩展（无需Redis）
sudo apt install -y php8.1-fpm php8.1-cli php8.1-mysql \
                 php8.1-gd php8.1-mbstring php8.1-xml php8.1-curl \
                 php8.1-zip php8.1-bcmath -y

# 检查PHP版本
php -v
```

#### 1.4 安装MySQL 8.0

```bash
sudo apt install mysql-server -y
sudo systemctl start mysql
sudo systemctl enable mysql

# 安全配置
sudo mysql_secure_installation
```

#### 1.5 安装Redis

```bash
sudo apt install redis-server -y
sudo systemctl start redis
sudo systemctl enable redis
```

#### 1.6 安装Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

### 步骤2：部署项目

#### 2.1 创建项目目录

```bash
sudo mkdir -p /var/www/redbook-ai
cd /var/www/redbook-ai
```

#### 2.2 上传项目文件

方式一：使用Git克隆

```bash
git clone <your-repository-url> .
```

方式二：使用FTP/SCP上传

```bash
# 在本地打包
tar -czf redbook-ai.tar.gz *

# 上传到服务器
scp redbook-ai.tar.gz user@server:/var/www/redbook-ai/

# 在服务器解压
cd /var/www/redbook-ai
tar -xzf redbook-ai.tar.gz
```

#### 2.3 安装依赖

```bash
cd /var/www/redbook-ai
composer install --no-dev --optimize-autoloader
```

#### 2.4 配置环境变量

```bash
cp .env.example .env
nano .env
```

编辑`.env`文件：

```ini
APP_DEBUG = false

[DATABASE]
TYPE = mysql
HOSTNAME = 127.0.0.1
DATABASE = redbook_ai
USERNAME = redbook_user
PASSWORD = your_strong_password_here
HOSTPORT = 3306

[REDIS]
HOST = 127.0.0.1
PORT = 6379
PASSWORD = your_redis_password

[GEMINI]
API_KEY = your_gemini_api_key_here
MODEL = gemini-1.5-flash
```

#### 2.5 设置目录权限

```bash
sudo chown -R www-data:www-data /var/www/redbook-ai
sudo chmod -R 755 /var/www/redbook-ai
sudo chmod -R 775 /var/www/redbook-ai/runtime
sudo chmod -R 775 /var/www/redbook-ai/public/uploads
```

### 步骤3：配置数据库

#### 3.1 创建数据库和用户

```bash
sudo mysql -u root -p
```

在MySQL中执行：

```sql
CREATE DATABASE redbook_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'redbook_user'@'localhost' IDENTIFIED BY 'your_strong_password_here';

GRANT ALL PRIVILEGES ON redbook_ai.* TO 'redbook_user'@'localhost';

FLUSH PRIVILEGES;

EXIT;
```

#### 3.2 导入数据库结构

```bash
mysql -u redbook_user -p redbook_ai < /var/www/redbook-ai/database.sql
```

### 步骤4：配置Nginx

#### 4.1 创建站点配置

```bash
sudo nano /etc/nginx/sites-available/redbook-ai
```

添加以下内容：

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/redbook-ai/public;
    index index.php index.html;

    # 日志文件
    access_log /var/log/nginx/redbook-ai-access.log;
    error_log /var/log/nginx/redbook-ai-error.log;

    # 隐藏Nginx版本
    server_tokens off;

    # 最大上传文件大小
    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

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

#### 4.2 启用站点

```bash
sudo ln -s /etc/nginx/sites-available/redbook-ai /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 步骤5：配置SSL证书（可选但推荐）

#### 使用Let's Encrypt免费证书

```bash
# 安装Certbot
sudo apt install certbot python3-certbot-nginx -y

# 获取证书
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# 自动续期
sudo certbot renew --dry-run
```

### 步骤6：配置Redis（可选）

#### 6.1 设置Redis密码

```bash
sudo nano /etc/redis/redis.conf
```

找到并修改：

```
requirepass your_redis_password
```

重启Redis：

```bash
sudo systemctl restart redis
```

### 步骤7：优化配置

#### 7.1 PHP-FPM优化

```bash
sudo nano /etc/php/8.1/fpm/pool.d/www.conf
```

调整参数：

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
```

重启PHP-FPM：

```bash
sudo systemctl restart php8.1-fpm
```

#### 7.2 MySQL优化

```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

添加优化参数：

```ini
[mysqld]
max_connections = 200
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
```

重启MySQL：

```bash
sudo systemctl restart mysql
```

### 步骤8：设置定时任务

#### 8.1 创建Cron任务

```bash
sudo crontab -e
```

添加以下任务：

```bash
# 每天凌晨0点重置用户每日配额
0 0 * * * cd /var/www/redbook-ai && php think user:resetQuota

# 每小时清理过期文件
0 * * * * cd /var/www/redbook-ai && php think file:clean

# 每天备份数据库
0 2 * * * mysqldump -u redbook_user -p'password' redbook_ai > /backup/redbook_ai_$(date +\%Y\%m\%d).sql
```

### 步骤9：配置防火墙

```bash
# 允许HTTP和HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# 允许SSH（如果需要）
sudo ufw allow 22/tcp

# 启用防火墙
sudo ufw enable
```

### 步骤10：验证部署

#### 10.1 检查服务状态

```bash
sudo systemctl status nginx
sudo systemctl status php8.1-fpm
sudo systemctl status mysql
sudo systemctl status redis
```

#### 10.2 访问网站

在浏览器访问：`http://your-domain.com`

#### 10.3 测试功能

1. 注册新用户
2. 登录系统
3. 生成文案
4. 生成图片
5. 下载图片

## 🔒 安全加固

### 1. 修改默认管理员密码

```sql
UPDATE rb_user SET password = 'new_hashed_password' WHERE username = 'admin';
```

### 2. 禁用不必要的PHP函数

编辑`/etc/php/8.1/fpm/php.ini`：

```ini
disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_multi_exec,parse_ini_file,show_source
```

### 3. 限制文件上传

```ini
upload_max_filesize = 10M
post_max_size = 10M
```

### 4. 配置Redis访问控制

```bash
# 绑定到本地
bind 127.0.0.1
# 设置密码
requirepass your_strong_password
```

## 📊 监控和日志

### 查看应用日志

```bash
tail -f /var/www/redbook-ai/runtime/log/$(date +%Y%m%d).log
```

### 查看Nginx日志

```bash
tail -f /var/log/nginx/redbook-ai-access.log
tail -f /var/log/nginx/redbook-ai-error.log
```

### 查看PHP错误日志

```bash
tail -f /var/log/php8.1-fpm.log
```

## 🔧 故障排查

### 问题1：500错误

**检查**：
```bash
# 检查PHP错误日志
tail -f /var/log/php8.1-fpm.log

# 检查文件权限
ls -la /var/www/redbook-ai
```

### 问题2：数据库连接失败

**检查**：
```bash
# 测试数据库连接
mysql -u redbook_user -p -h 127.0.0.1

# 检查MySQL状态
sudo systemctl status mysql
```

### 问题3：Redis连接失败

**检查**：
```bash
# 测试Redis连接
redis-cli ping

# 检查Redis状态
sudo systemctl status redis
```

## 📝 更新部署

### 更新代码

```bash
cd /var/www/redbook-ai
git pull origin main
composer install --no-dev
sudo systemctl reload php8.1-fpm
sudo systemctl reload nginx
```

### 数据库迁移

```bash
# 备份数据库
mysqldump -u redbook_user -p redbook_ai > backup_$(date +%Y%m%d).sql

# 执行新的SQL
mysql -u redbook_user -p redbook_ai < migrations/update_xxx.sql
```

## 🎉 部署完成

恭喜！你已成功部署RedBookAI系统。

下一步：
- [ ] 配置Gemini API Key
- [ ] 上传字体文件
- [ ] 导入模板数据
- [ ] 配置支付接口（如需要）
- [ ] 设置备份策略

---

**需要帮助？** 查看 [常见问题](README.md#常见问题) 或提交 [Issue](https://github.com/your-repo/redbook-ai/issues)
