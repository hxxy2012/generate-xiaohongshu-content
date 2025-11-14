# RedBookAI - 小红书AI创作助手

> 基于ThinkPHP 8.x + MySQL + Gemini API的智能小红书内容生成系统（轻量级部署，无需Redis）

## 🎯 项目简介

RedBookAI是一个完整的小红书内容智能创作平台，用户输入主题后，系统自动调用Gemini API生成小红书风格的文案，并将文案渲染成精美的图片，支持多种模板和批量生成。

### 核心功能

- ✅ **AI文案生成**：基于Google Gemini API，智能生成小红书风格内容
- ✅ **图片渲染**：将文案自动渲染成精美图片，支持多种模板
- ✅ **用户系统**：完整的注册/登录、RBAC权限管理
- ✅ **会员体系**：免费用户、VIP会员、企业用户三级权限
- ✅ **模板系统**：10+套精美模板，支持自定义
- ✅ **批量生成**：支持批量创作，提高效率
- ✅ **积分系统**：签到、邀请奖励等

## 🛠️ 技术栈

### 后端
- PHP 8.0+
- ThinkPHP 8.x
- MySQL 8.0+

### 核心服务
- Google Gemini API（AI文案生成）
- PHP GD Library（图片处理）

### 缓存方案
- 文件缓存（无需Redis，轻量级部署）

### 前端
- HTML5 + CSS3 + JavaScript
- Layui / Vue 3 + Element Plus（可选）

## 📦 安装部署

### 环境要求

```bash
PHP >= 8.0
MySQL >= 8.0
PHP扩展：pdo_mysql, gd, mbstring, json, openssl, curl, zip
```

### 安装步骤

#### 1. 克隆项目

```bash
git clone <repository-url>
cd generate-xiaohongshu-content
```

#### 2. 安装依赖

```bash
composer install
```

#### 3. 配置环境变量

复制`.env.example`为`.env`并配置：

```bash
cp .env.example .env
```

编辑`.env`文件：

```ini
[DATABASE]
TYPE = mysql
HOSTNAME = 127.0.0.1
DATABASE = redbook_ai
USERNAME = root
PASSWORD = your_password
HOSTPORT = 3306
PREFIX = rb_

[CACHE]
DRIVER = file

[GEMINI]
# 在 https://makersuite.google.com/app/apikey 获取API Key
API_KEY = your_gemini_api_key_here
MODEL = gemini-1.5-flash
```

#### 4. 初始化数据库

```bash
# 导入数据库结构
mysql -u root -p < database.sql
```

或者登录MySQL手动执行：

```bash
mysql -u root -p
source /path/to/database.sql
```

#### 5. 设置目录权限

```bash
chmod -R 755 public/uploads
chmod -R 755 runtime
```

#### 6. 配置Web服务器

**Nginx配置示例：**

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/redbook-ai/public;
    index index.php index.html;

    location / {
        if (!-e $request_filename) {
            rewrite ^(.*)$ /index.php?s=$1 last;
        }
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

**Apache配置示例：**

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/redbook-ai/public

    <Directory /path/to/redbook-ai/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### 7. 启动服务

```bash
# 启动PHP内置服务器（开发环境）
php think run

# 或使用Nginx/Apache（生产环境）
```

访问：`http://your-domain.com`

## 🔑 默认账号

- 管理员账号：`admin`
- 默认密码：`admin123`

**⚠️ 重要：首次登录后请立即修改密码！**

## 📚 API文档

### 用户认证

#### 注册
```
POST /auth/register
Content-Type: application/json

{
  "username": "testuser",
  "password": "123456",
  "password_confirm": "123456",
  "phone": "13800138000",
  "email": "test@example.com"
}
```

#### 登录
```
POST /auth/login
Content-Type: application/json

{
  "account": "testuser",
  "password": "123456"
}
```

### 内容生成

#### 生成文案
```
POST /generate/createContent
Content-Type: application/json

{
  "keywords": "夏季防晒推荐",
  "content_type": "grass",
  "style": "casual",
  "word_count": 300,
  "target_audience": "学生党",
  "tag_count": 5,
  "include_emoji": true
}
```

#### 生成图片
```
POST /image/generate
Content-Type: application/json

{
  "content_id": 1,
  "template_id": 1,
  "watermark": true,
  "quality": 90
}
```

## 🎨 模板系统

### 基础模板

系统内置10套基础模板：

1. **粉色少女心** (`pink_girl`) - 适合美妆、穿搭
2. **蓝色清新** (`blue_fresh`) - 适合旅行、生活
3. 更多模板正在开发中...

### 自定义模板

模板配置示例（JSON格式）：

```json
{
  "template_name": "模板名称",
  "width": 1080,
  "height": 1440,
  "background": {
    "type": "gradient",
    "colors": ["#FFB6C1", "#FFC0CB"],
    "direction": "vertical"
  },
  "elements": [
    {
      "type": "text",
      "field": "title",
      "position": {"x": 60, "y": 120},
      "width": 960,
      "height": 200,
      "font_size": 52,
      "color": "#FFFFFF",
      "align": "center"
    }
  ]
}
```

## 🔧 配置说明

### Gemini API配置

1. 访问 [Google AI Studio](https://makersuite.google.com/app/apikey)
2. 登录Google账号
3. 创建API Key
4. 将API Key填入`.env`文件

### 权限配置

系统内置4种角色：

| 角色 | 每日次数 | 模板权限 | 存储空间 | 水印 |
|------|---------|---------|---------|------|
| 免费用户 | 5次 | 基础模板 | 100MB | 有 |
| VIP会员 | 50次 | 所有模板 | 5GB | 可去除 |
| 企业用户 | 200次 | 全部+自定义 | 50GB | 可去除 |
| 管理员 | 无限 | 全部 | 无限 | 可控制 |

## 🚀 项目结构

```
redbook-ai/
├── app/                    # 应用目录
│   ├── index/             # 前台应用
│   │   ├── controller/    # 控制器
│   │   ├── model/         # 模型
│   │   ├── service/       # 服务层
│   │   ├── validate/      # 验证器
│   │   └── middleware/    # 中间件
│   └── admin/             # 后台应用
├── config/                # 配置文件
├── public/                # 公共资源
│   ├── static/           # 静态资源
│   │   └── templates/    # 图片模板
│   └── uploads/          # 上传文件
├── runtime/              # 运行时文件
├── database.sql          # 数据库结构
├── .env.example          # 环境配置示例
└── README.md             # 说明文档
```

## 📝 开发说明

### 核心文件说明

| 文件 | 说明 |
|------|------|
| `app/index/service/GeminiService.php` | Gemini API服务 |
| `app/index/service/ImageService.php` | 图片渲染服务 |
| `app/index/controller/Generate.php` | 文案生成控制器 |
| `app/index/controller/ImageController.php` | 图片生成控制器 |
| `app/index/model/User.php` | 用户模型 |
| `app/index/model/Content.php` | 内容模型 |
| `app/index/model/Template.php` | 模板模型 |

### 添加新模板

1. 在`public/static/templates/basic/`创建JSON配置文件
2. 在数据库`rb_template`表添加记录
3. 配置模板元素和样式

### 自定义敏感词

编辑`app/index/service/SensitiveWordService.php`中的`$sensitiveWords`数组。

## 🐛 常见问题

### 1. Gemini API调用失败

**问题**：提示"Gemini API Key未配置"

**解决**：
- 检查`.env`文件中`GEMINI_API_KEY`是否正确配置
- 确认API Key有效且未过期
- 检查网络是否能访问Google服务

### 2. 图片生成失败

**问题**：生成图片时报错

**解决**：
- 检查PHP是否安装GD扩展：`php -m | grep gd`
- 检查`public/uploads/images/`目录权限
- 检查字体文件是否存在

### 3. 数据库连接失败

**问题**：无法连接数据库

**解决**：
- 检查`.env`中数据库配置是否正确
- 确认MySQL服务已启动
- 检查数据库用户权限

## 📄 许可证

MIT License

## 🤝 贡献

欢迎提交Issue和Pull Request！

## 📧 联系方式

- 项目地址：[GitHub](https://github.com/your-repo/redbook-ai)
- 问题反馈：[Issues](https://github.com/your-repo/redbook-ai/issues)

## 🌟 致谢

- [ThinkPHP](https://www.thinkphp.cn/)
- [Google Gemini](https://ai.google.dev/)
- 所有贡献者

---

**⭐ 如果这个项目对你有帮助，请给个Star！**
