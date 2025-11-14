# RedBookAI 快速开始指南 ⚡

> 5分钟快速部署，无需Redis，开箱即用！

## 🎯 最小化安装（仅需3步）

### 前置条件

- PHP 8.0+
- MySQL 8.0+
- Composer

### 步骤1：获取代码（1分钟）

```bash
git clone <repository-url>
cd generate-xiaohongshu-content
composer install
```

### 步骤2：配置环境（2分钟）

```bash
# 复制配置文件
cp .env.example .env

# 编辑配置（只需填3项）
nano .env
```

**必填配置**（仅3项）：

```ini
[DATABASE]
PASSWORD = your_mysql_password

[GEMINI]
API_KEY = your_gemini_api_key
```

### 步骤3：初始化数据库（2分钟）

```bash
# 创建数据库
mysql -u root -p -e "CREATE DATABASE redbook_ai CHARACTER SET utf8mb4;"

# 导入数据
mysql -u root -p redbook_ai < database.sql
mysql -u root -p redbook_ai < init_templates.sql

# 启动服务
php think run
```

✅ **完成！** 访问 http://localhost:8000

---

## 🚀 一键安装脚本

### Linux/macOS

```bash
#!/bin/bash
# RedBookAI 一键安装脚本

echo "🚀 开始安装 RedBookAI..."

# 检查环境
command -v php >/dev/null 2>&1 || { echo "❌ 需要PHP 8.0+"; exit 1; }
command -v mysql >/dev/null 2>&1 || { echo "❌ 需要MySQL 8.0+"; exit 1; }
command -v composer >/dev/null 2>&1 || { echo "❌ 需要Composer"; exit 1; }

# 安装依赖
echo "📦 安装依赖..."
composer install

# 配置环境
echo "⚙️ 配置环境..."
cp .env.example .env

echo "请输入MySQL密码:"
read -s mysql_password

echo "请输入Gemini API Key:"
read gemini_key

# 更新配置文件
sed -i "s/PASSWORD =/PASSWORD = $mysql_password/g" .env
sed -i "s/API_KEY = your_gemini_api_key_here/API_KEY = $gemini_key/g" .env

# 创建数据库
echo "🗄️ 初始化数据库..."
mysql -u root -p$mysql_password -e "CREATE DATABASE IF NOT EXISTS redbook_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p$mysql_password redbook_ai < database.sql
mysql -u root -p$mysql_password redbook_ai < init_templates.sql

# 设置权限
chmod -R 755 runtime
chmod -R 755 public/uploads

echo "✅ 安装完成！"
echo "🌐 启动服务: php think run"
echo "📖 访问地址: http://localhost:8000"
echo "👤 默认账号: admin / admin123"
```

保存为 `install.sh`，然后执行：

```bash
chmod +x install.sh
./install.sh
```

### Windows（PowerShell）

```powershell
# RedBookAI Windows 一键安装脚本

Write-Host "🚀 开始安装 RedBookAI..." -ForegroundColor Green

# 安装依赖
Write-Host "📦 安装依赖..." -ForegroundColor Yellow
composer install

# 配置环境
Write-Host "⚙️ 配置环境..." -ForegroundColor Yellow
Copy-Item .env.example .env

$mysql_password = Read-Host "请输入MySQL密码" -AsSecureString
$mysql_password_plain = [Runtime.InteropServices.Marshal]::PtrToStringAuto(
    [Runtime.InteropServices.Marshal]::SecureStringToBSTR($mysql_password)
)

$gemini_key = Read-Host "请输入Gemini API Key"

# 更新配置
(Get-Content .env) -replace 'PASSWORD =', "PASSWORD = $mysql_password_plain" | Set-Content .env
(Get-Content .env) -replace 'API_KEY = your_gemini_api_key_here', "API_KEY = $gemini_key" | Set-Content .env

# 初始化数据库
Write-Host "🗄️ 初始化数据库..." -ForegroundColor Yellow
mysql -u root -p$mysql_password_plain -e "CREATE DATABASE IF NOT EXISTS redbook_ai CHARACTER SET utf8mb4;"
mysql -u root -p$mysql_password_plain redbook_ai < database.sql
mysql -u root -p$mysql_password_plain redbook_ai < init_templates.sql

Write-Host "✅ 安装完成！" -ForegroundColor Green
Write-Host "🌐 启动服务: php think run" -ForegroundColor Cyan
Write-Host "📖 访问地址: http://localhost:8000" -ForegroundColor Cyan
Write-Host "👤 默认账号: admin / admin123" -ForegroundColor Cyan
```

保存为 `install.ps1`，然后执行：

```powershell
.\install.ps1
```

---

## 📝 快速测试

### 测试注册

```bash
curl -X POST http://localhost:8000/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "username": "testuser",
    "password": "test123456",
    "password_confirm": "test123456"
  }'
```

### 测试登录

```bash
curl -X POST http://localhost:8000/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "account": "testuser",
    "password": "test123456"
  }'
```

### 测试文案生成

```bash
curl -X POST http://localhost:8000/generate/createContent \
  -H "Content-Type: application/json" \
  -H "Cookie: PHPSESSID=your_session_id" \
  -d '{
    "keywords": "夏季防晒推荐",
    "content_type": "grass",
    "style": "casual"
  }'
```

---

## 🎨 功能演示

### 1. 注册新用户

浏览器访问：http://localhost:8000/auth/register

填写信息：
- 用户名：mytest
- 密码：123456
- 确认密码：123456

### 2. 生成文案

登录后访问：http://localhost:8000/generate

输入主题：
- 关键词：**夏季穿搭推荐**
- 内容类型：**穿搭分享**
- 风格：**轻松活泼**

点击【生成文案】，等待5-10秒。

### 3. 生成图片

文案生成后：
1. 点击【生成图片】
2. 选择模板：粉色少女心
3. 点击【确认生成】
4. 等待3-5秒
5. 下载图片

---

## 🔧 常用命令

### 开发环境

```bash
# 启动内置服务器
php think run

# 指定端口
php think run -p 8080

# 清除缓存
rm -rf runtime/cache/*

# 查看日志
tail -f runtime/log/$(date +%Y%m%d).log
```

### 生产环境

```bash
# 使用Nginx
sudo systemctl start nginx
sudo systemctl start php8.1-fpm

# 查看服务状态
sudo systemctl status nginx
sudo systemctl status php8.1-fpm

# 重启服务
sudo systemctl reload nginx
```

---

## 🎯 默认账号

| 角色 | 用户名 | 密码 | 权限 |
|------|--------|------|------|
| 管理员 | admin | admin123 | 全部 |

**⚠️ 重要**：首次登录后请立即修改密码！

```sql
-- 修改admin密码
UPDATE rb_user SET password = '新密码的hash值' WHERE username = 'admin';
```

---

## 📊 系统要求

### 最低配置

- CPU：1核
- 内存：1GB
- 磁盘：5GB
- 带宽：1Mbps

### 推荐配置

- CPU：2核+
- 内存：4GB+
- 磁盘：20GB SSD
- 带宽：5Mbps+

---

## ❓ 快速问题排查

### 问题1：数据库连接失败

```bash
# 检查MySQL是否运行
sudo systemctl status mysql

# 测试连接
mysql -u root -p -h 127.0.0.1
```

### 问题2：权限错误

```bash
# 设置正确权限
chmod -R 755 runtime
chmod -R 755 public/uploads
```

### 问题3：Gemini API失败

```bash
# 验证API Key
curl "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=YOUR_KEY" \
  -H "Content-Type: application/json" \
  -d '{"contents":[{"parts":[{"text":"test"}]}]}'
```

### 问题4：图片生成失败

```bash
# 检查GD扩展
php -m | grep gd

# 安装字体
sudo apt install fonts-dejavu fonts-noto-cjk
```

---

## 📚 完整文档

- [完整安装文档](INSTALLATION.md)
- [部署文档](DEPLOYMENT.md)
- [用户手册](USER_MANUAL.md)
- [更新日志](CHANGELOG.md)

---

## 🆘 获取帮助

- 🐛 [提交Issue](https://github.com/your-repo/issues)
- 📖 [查看文档](README.md)
- 💬 加入交流群

---

## ⭐ 特别提示

1. **首次使用**建议先在本地测试
2. **生产环境**请配置SSL证书
3. **定期备份**数据库和上传文件
4. **监控日志**及时发现问题

---

**开始你的AI创作之旅！** 🎉
