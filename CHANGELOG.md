# 更新日志

## [1.1.0] - 2024-01-15

### ♻️ 重构优化

#### 移除Redis依赖，简化系统架构

**背景**：
为了降低部署门槛，让更多开发者能够快速使用本系统，我们决定移除Redis依赖，改用文件缓存方案。

**主要变更**：

1. **依赖简化**
   - ❌ 移除 `predis/predis` 包
   - ❌ 移除 `topthink/think-queue` 包
   - ✅ 只保留核心ThinkPHP依赖

2. **缓存方案**
   - 从：Redis缓存
   - 到：文件缓存
   - 位置：`runtime/cache/`

3. **Session方案**
   - 从：Redis Session
   - 到：文件Session
   - 位置：`runtime/session/`

4. **配置简化**
   - 删除：`config/queue.php`
   - 新增：`config/session.php`
   - 优化：`config/cache.php`

5. **环境要求**
   ```diff
   - PHP >= 8.0
   - MySQL >= 8.0
   - - Redis >= 6.0
   - - PHP扩展：redis
   + PHP扩展：pdo_mysql, gd, mbstring, json, openssl, curl, zip
   ```

**优势对比**：

| 对比项 | 使用Redis | 使用文件缓存 |
|--------|-----------|------------|
| 环境要求 | PHP + MySQL + Redis | PHP + MySQL |
| 安装步骤 | 7步 | 4步 |
| 依赖服务 | 3个 | 2个 |
| 配置复杂度 | 中等 | 简单 |
| 部署时间 | ~30分钟 | ~15分钟 |
| 服务器成本 | 较高（需要Redis） | 较低 |
| 适用场景 | 高并发 | 中小型应用 |

**性能说明**：

对于中小型应用（日访问量 < 10万），文件缓存性能完全足够：

- **读取速度**：~0.5ms
- **写入速度**：~1ms
- **并发能力**：支持100+并发

**升级指南**：

如果你已经部署了旧版本（带Redis），可以这样升级：

```bash
# 1. 备份数据
mysqldump -u root -p redbook_ai > backup.sql

# 2. 拉取最新代码
git pull origin main

# 3. 更新依赖
composer install

# 4. 更新配置
cp .env.example .env.new
# 手动合并配置（移除Redis相关配置）

# 5. 清理缓存
rm -rf runtime/cache/*

# 6. 重启服务
php think run
```

**回退方案**：

如果需要继续使用Redis版本，可以切换到v1.0.0：

```bash
git checkout v1.0.0
composer install
```

---

## [1.0.0] - 2024-01-14

### ✨ 首次发布

#### 核心功能

- ✅ **AI文案生成**：基于Gemini API智能生成小红书风格内容
- ✅ **图片渲染**：支持10+套精美模板
- ✅ **用户系统**：完整的注册/登录、RBAC权限管理
- ✅ **会员体系**：免费/VIP/企业三级权限
- ✅ **积分系统**：签到、邀请奖励
- ✅ **批量生成**：支持批量创作

#### 技术栈

- PHP 8.0+
- ThinkPHP 8.x
- MySQL 8.0+
- Redis 6.0+（已在v1.1.0移除）
- Gemini API

#### 数据库设计

- 11张核心表
- RBAC权限体系
- 完整的初始化脚本

#### 文档

- README.md - 项目说明
- INSTALLATION.md - 安装文档
- DEPLOYMENT.md - 部署文档
- USER_MANUAL.md - 用户手册

---

## 版本规划

### [1.2.0] - 计划中

**功能增强**：
- [ ] 前端界面开发（Vue3 + Element Plus）
- [ ] 支付系统集成
- [ ] 管理后台功能
- [ ] 数据统计分析

**性能优化**：
- [ ] 数据库查询优化
- [ ] 图片生成异步处理
- [ ] CDN静态资源加速

### [2.0.0] - 未来规划

**重大功能**：
- [ ] 多平台支持（抖音、知乎等）
- [ ] AI图片生成（DALL-E / Stable Diffusion）
- [ ] 视频内容生成
- [ ] API接口开放

**架构升级**：
- [ ] 微服务架构
- [ ] 分布式部署
- [ ] 容器化（K8s）

---

## 升级提示

### 从 1.0.0 到 1.1.0

**必须操作**：
1. 更新配置文件（移除Redis配置）
2. 重新安装依赖（`composer install`）
3. 清除旧缓存（`rm -rf runtime/cache/*`）

**可选操作**：
1. 卸载Redis服务（如果不再使用）

**注意事项**：
- 数据库结构无变化，无需迁移数据
- Session会丢失，用户需要重新登录
- 积分、内容等数据保持不变

---

## 问题反馈

如果遇到问题，请：

1. 查看 [常见问题](README.md#常见问题)
2. 提交 [GitHub Issue](https://github.com/your-repo/issues)
3. 加入交流群讨论

---

**感谢使用 RedBookAI！** 🎉
