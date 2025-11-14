-- =====================================================
-- RedBookAI - 小红书AI创作助手
-- 数据库初始化脚本
-- =====================================================

-- 创建数据库
CREATE DATABASE IF NOT EXISTS `redbook_ai` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `redbook_ai`;

-- =====================================================
-- 1. 用户系统表
-- =====================================================

-- 用户表
DROP TABLE IF EXISTS `rb_user`;
CREATE TABLE `rb_user` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '用户ID',
  `username` VARCHAR(50) NOT NULL UNIQUE COMMENT '用户名',
  `phone` VARCHAR(20) DEFAULT NULL UNIQUE COMMENT '手机号',
  `email` VARCHAR(100) DEFAULT NULL UNIQUE COMMENT '邮箱',
  `password` VARCHAR(255) NOT NULL COMMENT '密码',
  `nickname` VARCHAR(50) DEFAULT NULL COMMENT '昵称',
  `avatar` VARCHAR(255) DEFAULT NULL COMMENT '头像',
  `role_id` INT UNSIGNED DEFAULT 2 COMMENT '角色ID：1管理员2免费用户3VIP4企业',
  `status` TINYINT DEFAULT 1 COMMENT '状态：0禁用1正常',
  `generate_limit_daily` INT DEFAULT 5 COMMENT '每日生成次数限制',
  `generate_used_today` INT DEFAULT 0 COMMENT '今日已用次数',
  `generate_total` INT DEFAULT 0 COMMENT '累计生成次数',
  `points` INT DEFAULT 100 COMMENT '积分',
  `vip_expire_time` DATETIME DEFAULT NULL COMMENT 'VIP到期时间',
  `storage_limit` BIGINT DEFAULT 104857600 COMMENT '存储空间限制（字节），默认100MB',
  `storage_used` BIGINT DEFAULT 0 COMMENT '已用存储空间（字节）',
  `inviter_id` INT UNSIGNED DEFAULT NULL COMMENT '邀请人ID',
  `invite_code` VARCHAR(20) DEFAULT NULL UNIQUE COMMENT '邀请码',
  `last_login_time` DATETIME DEFAULT NULL COMMENT '最后登录时间',
  `last_login_ip` VARCHAR(50) DEFAULT NULL COMMENT '最后登录IP',
  `create_time` DATETIME NOT NULL COMMENT '创建时间',
  `update_time` DATETIME DEFAULT NULL COMMENT '更新时间',
  INDEX `idx_phone` (`phone`),
  INDEX `idx_email` (`email`),
  INDEX `idx_role_id` (`role_id`),
  INDEX `idx_invite_code` (`invite_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表';

-- 角色表
DROP TABLE IF EXISTS `rb_role`;
CREATE TABLE `rb_role` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '角色ID',
  `role_name` VARCHAR(50) NOT NULL COMMENT '角色名称',
  `role_code` VARCHAR(50) NOT NULL UNIQUE COMMENT '角色编码',
  `description` VARCHAR(255) DEFAULT NULL COMMENT '描述',
  `sort` INT DEFAULT 0 COMMENT '排序',
  `status` TINYINT DEFAULT 1 COMMENT '状态：0禁用1启用',
  `create_time` DATETIME NOT NULL COMMENT '创建时间',
  INDEX `idx_role_code` (`role_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='角色表';

-- 权限表
DROP TABLE IF EXISTS `rb_permission`;
CREATE TABLE `rb_permission` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '权限ID',
  `permission_name` VARCHAR(50) NOT NULL COMMENT '权限名称',
  `permission_code` VARCHAR(100) NOT NULL UNIQUE COMMENT '权限编码',
  `description` VARCHAR(255) DEFAULT NULL COMMENT '描述',
  `create_time` DATETIME NOT NULL COMMENT '创建时间',
  INDEX `idx_permission_code` (`permission_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='权限表';

-- 角色权限关联表
DROP TABLE IF EXISTS `rb_role_permission`;
CREATE TABLE `rb_role_permission` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'ID',
  `role_id` INT UNSIGNED NOT NULL COMMENT '角色ID',
  `permission_id` INT UNSIGNED NOT NULL COMMENT '权限ID',
  UNIQUE KEY `uk_role_permission` (`role_id`, `permission_id`),
  INDEX `idx_role_id` (`role_id`),
  INDEX `idx_permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='角色权限关联表';

-- =====================================================
-- 2. 内容系统表
-- =====================================================

-- 内容表（文案）
DROP TABLE IF EXISTS `rb_content`;
CREATE TABLE `rb_content` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '内容ID',
  `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
  `keywords` VARCHAR(255) NOT NULL COMMENT '关键词/主题',
  `content_type` VARCHAR(50) DEFAULT NULL COMMENT '内容类型：grass/review/tutorial等',
  `style` VARCHAR(50) DEFAULT NULL COMMENT '风格：casual/professional/humorous等',
  `word_count` INT DEFAULT 0 COMMENT '字数要求',
  `target_audience` VARCHAR(50) DEFAULT NULL COMMENT '目标人群',
  `title` VARCHAR(255) DEFAULT NULL COMMENT '标题',
  `content` TEXT DEFAULT NULL COMMENT '正文',
  `tags` VARCHAR(500) DEFAULT NULL COMMENT '标签（JSON数组）',
  `cover_text` VARCHAR(100) DEFAULT NULL COMMENT '封面文字',
  `generate_params` TEXT DEFAULT NULL COMMENT '生成参数（JSON）',
  `ai_model` VARCHAR(50) DEFAULT NULL COMMENT 'AI模型',
  `token_used` INT DEFAULT 0 COMMENT '消耗Token',
  `status` TINYINT DEFAULT 0 COMMENT '状态：0草稿1已生成2生成失败',
  `error_message` TEXT DEFAULT NULL COMMENT '错误信息',
  `is_favorite` TINYINT DEFAULT 0 COMMENT '是否收藏：0否1是',
  `generate_time` DATETIME DEFAULT NULL COMMENT '生成时间',
  `create_time` DATETIME NOT NULL COMMENT '创建时间',
  `update_time` DATETIME DEFAULT NULL COMMENT '更新时间',
  `delete_time` DATETIME DEFAULT NULL COMMENT '删除时间（软删除）',
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_create_time` (`create_time`),
  INDEX `idx_delete_time` (`delete_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='内容表';

-- 图片模板表
DROP TABLE IF EXISTS `rb_template`;
CREATE TABLE `rb_template` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '模板ID',
  `template_name` VARCHAR(100) NOT NULL COMMENT '模板名称',
  `template_code` VARCHAR(50) NOT NULL UNIQUE COMMENT '模板编码',
  `category` VARCHAR(50) DEFAULT NULL COMMENT '分类：basic/premium/custom',
  `scene` VARCHAR(100) DEFAULT NULL COMMENT '适用场景',
  `preview_image` VARCHAR(255) DEFAULT NULL COMMENT '预览图',
  `config` TEXT NOT NULL COMMENT '模板配置（JSON）',
  `width` INT DEFAULT 1080 COMMENT '图片宽度',
  `height` INT DEFAULT 1440 COMMENT '图片高度',
  `permission_level` TINYINT DEFAULT 1 COMMENT '权限等级：1免费2VIP3企业',
  `use_count` INT DEFAULT 0 COMMENT '使用次数',
  `sort` INT DEFAULT 0 COMMENT '排序',
  `status` TINYINT DEFAULT 1 COMMENT '状态：0禁用1启用',
  `creator_id` INT UNSIGNED DEFAULT NULL COMMENT '创建者ID（自定义模板）',
  `create_time` DATETIME NOT NULL COMMENT '创建时间',
  `update_time` DATETIME DEFAULT NULL COMMENT '更新时间',
  INDEX `idx_category` (`category`),
  INDEX `idx_permission_level` (`permission_level`),
  INDEX `idx_template_code` (`template_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='图片模板表';

-- 生成的图片表
DROP TABLE IF EXISTS `rb_image`;
CREATE TABLE `rb_image` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '图片ID',
  `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
  `content_id` INT UNSIGNED NOT NULL COMMENT '内容ID',
  `template_id` INT UNSIGNED NOT NULL COMMENT '模板ID',
  `file_path` VARCHAR(255) NOT NULL COMMENT '文件路径',
  `file_name` VARCHAR(255) NOT NULL COMMENT '文件名',
  `file_size` INT DEFAULT 0 COMMENT '文件大小（字节）',
  `file_url` VARCHAR(255) DEFAULT NULL COMMENT '访问URL',
  `width` INT DEFAULT 0 COMMENT '宽度',
  `height` INT DEFAULT 0 COMMENT '高度',
  `format` VARCHAR(10) DEFAULT 'jpg' COMMENT '格式：jpg/png/webp',
  `has_watermark` TINYINT DEFAULT 1 COMMENT '是否有水印：0否1是',
  `page_number` TINYINT DEFAULT 1 COMMENT '页码（文案分页）',
  `is_favorite` TINYINT DEFAULT 0 COMMENT '是否收藏：0否1是',
  `download_count` INT DEFAULT 0 COMMENT '下载次数',
  `create_time` DATETIME NOT NULL COMMENT '创建时间',
  `delete_time` DATETIME DEFAULT NULL COMMENT '删除时间（软删除）',
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_content_id` (`content_id`),
  INDEX `idx_template_id` (`template_id`),
  INDEX `idx_create_time` (`create_time`),
  INDEX `idx_delete_time` (`delete_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='生成的图片表';

-- =====================================================
-- 3. 任务队列表
-- =====================================================

-- 生成任务队列表
DROP TABLE IF EXISTS `rb_generate_task`;
CREATE TABLE `rb_generate_task` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '任务ID',
  `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
  `task_type` VARCHAR(20) NOT NULL COMMENT '任务类型：content/image/batch',
  `content_id` INT UNSIGNED DEFAULT NULL COMMENT '内容ID',
  `params` TEXT DEFAULT NULL COMMENT '任务参数（JSON）',
  `status` TINYINT DEFAULT 0 COMMENT '状态：0待处理1处理中2已完成3失败',
  `priority` TINYINT DEFAULT 5 COMMENT '优先级：1-10（VIP优先）',
  `result` TEXT DEFAULT NULL COMMENT '结果',
  `error_message` TEXT DEFAULT NULL COMMENT '错误信息',
  `start_time` DATETIME DEFAULT NULL COMMENT '开始时间',
  `finish_time` DATETIME DEFAULT NULL COMMENT '完成时间',
  `retry_count` TINYINT DEFAULT 0 COMMENT '重试次数',
  `create_time` DATETIME NOT NULL COMMENT '创建时间',
  INDEX `idx_status` (`status`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_task_type` (`task_type`),
  INDEX `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='生成任务队列表';

-- =====================================================
-- 4. 日志和记录表
-- =====================================================

-- API调用日志表
DROP TABLE IF EXISTS `rb_api_log`;
CREATE TABLE `rb_api_log` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '日志ID',
  `user_id` INT UNSIGNED DEFAULT NULL COMMENT '用户ID',
  `api_type` VARCHAR(50) NOT NULL COMMENT 'API类型：gemini',
  `request_data` TEXT DEFAULT NULL COMMENT '请求数据',
  `response_data` TEXT DEFAULT NULL COMMENT '响应数据',
  `token_used` INT DEFAULT 0 COMMENT '消耗Token',
  `status_code` INT DEFAULT NULL COMMENT 'HTTP状态码',
  `success` TINYINT DEFAULT 1 COMMENT '是否成功：0失败1成功',
  `error_message` TEXT DEFAULT NULL COMMENT '错误信息',
  `duration` INT DEFAULT 0 COMMENT '耗时（毫秒）',
  `ip_address` VARCHAR(50) DEFAULT NULL COMMENT 'IP地址',
  `create_time` DATETIME NOT NULL COMMENT '创建时间',
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_create_time` (`create_time`),
  INDEX `idx_api_type` (`api_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='API调用日志表';

-- =====================================================
-- 5. 订单和积分表
-- =====================================================

-- 订单表
DROP TABLE IF EXISTS `rb_order`;
CREATE TABLE `rb_order` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '订单ID',
  `order_no` VARCHAR(50) NOT NULL UNIQUE COMMENT '订单号',
  `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
  `package_type` VARCHAR(20) NOT NULL COMMENT '套餐类型：vip_month/vip_year/enterprise',
  `package_name` VARCHAR(50) NOT NULL COMMENT '套餐名称',
  `amount` DECIMAL(10,2) NOT NULL COMMENT '订单金额',
  `pay_method` VARCHAR(20) DEFAULT NULL COMMENT '支付方式：wechat/alipay/points',
  `pay_status` TINYINT DEFAULT 0 COMMENT '支付状态：0未支付1已支付2已退款',
  `pay_time` DATETIME DEFAULT NULL COMMENT '支付时间',
  `transaction_id` VARCHAR(100) DEFAULT NULL COMMENT '第三方交易号',
  `expire_time` DATETIME DEFAULT NULL COMMENT '过期时间（未支付订单）',
  `create_time` DATETIME NOT NULL COMMENT '创建时间',
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_order_no` (`order_no`),
  INDEX `idx_pay_status` (`pay_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单表';

-- 积分记录表
DROP TABLE IF EXISTS `rb_points_log`;
CREATE TABLE `rb_points_log` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '记录ID',
  `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
  `points` INT NOT NULL COMMENT '积分变化（正数增加，负数减少）',
  `type` VARCHAR(50) NOT NULL COMMENT '类型：register/checkin/invite/consume',
  `description` VARCHAR(255) DEFAULT NULL COMMENT '描述',
  `related_id` INT UNSIGNED DEFAULT NULL COMMENT '关联ID',
  `create_time` DATETIME NOT NULL COMMENT '创建时间',
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='积分记录表';

-- =====================================================
-- 6. 系统配置表
-- =====================================================

-- 系统配置表
DROP TABLE IF EXISTS `rb_config`;
CREATE TABLE `rb_config` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '配置ID',
  `config_key` VARCHAR(100) NOT NULL UNIQUE COMMENT '配置键',
  `config_value` TEXT DEFAULT NULL COMMENT '配置值',
  `config_type` VARCHAR(20) DEFAULT 'string' COMMENT '配置类型',
  `config_group` VARCHAR(50) DEFAULT NULL COMMENT '配置分组',
  `description` VARCHAR(255) DEFAULT NULL COMMENT '描述',
  `sort` INT DEFAULT 0 COMMENT '排序',
  `create_time` DATETIME NOT NULL COMMENT '创建时间',
  `update_time` DATETIME DEFAULT NULL COMMENT '更新时间',
  INDEX `idx_config_key` (`config_key`),
  INDEX `idx_config_group` (`config_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置表';

-- =====================================================
-- 初始化数据
-- =====================================================

-- 初始化角色
INSERT INTO `rb_role` (`role_name`, `role_code`, `description`, `sort`, `create_time`) VALUES
('管理员', 'admin', '系统管理员', 1, NOW()),
('免费用户', 'free', '免费注册用户', 2, NOW()),
('VIP会员', 'vip', 'VIP付费用户', 3, NOW()),
('企业用户', 'enterprise', '企业团队用户', 4, NOW());

-- 初始化权限
INSERT INTO `rb_permission` (`permission_name`, `permission_code`, `description`, `create_time`) VALUES
('生成内容', 'content:generate', '生成小红书文案', NOW()),
('下载图片', 'content:download', '下载生成的图片', NOW()),
('批量生成', 'content:batch', '批量生成内容', NOW()),
('基础模板', 'template:basic', '使用基础模板', NOW()),
('高级模板', 'template:premium', '使用高级模板', NOW()),
('自定义模板', 'template:custom', '上传自定义模板', NOW()),
('去除水印', 'watermark:remove', '去除图片水印', NOW()),
('永久历史', 'history:permanent', '永久保存生成历史', NOW()),
('API访问', 'api:access', 'API接口访问', NOW());

-- 免费用户权限（角色ID=2）
INSERT INTO `rb_role_permission` (`role_id`, `permission_id`) VALUES
(2, 1), -- 生成内容
(2, 2), -- 下载图片
(2, 4); -- 基础模板

-- VIP用户权限（角色ID=3）
INSERT INTO `rb_role_permission` (`role_id`, `permission_id`) VALUES
(3, 1), -- 生成内容
(3, 2), -- 下载图片
(3, 3), -- 批量生成
(3, 4), -- 基础模板
(3, 5), -- 高级模板
(3, 7), -- 去除水印
(3, 8); -- 永久历史

-- 企业用户权限（角色ID=4）
INSERT INTO `rb_role_permission` (`role_id`, `permission_id`) VALUES
(4, 1), -- 生成内容
(4, 2), -- 下载图片
(4, 3), -- 批量生成
(4, 4), -- 基础模板
(4, 5), -- 高级模板
(4, 6), -- 自定义模板
(4, 7), -- 去除水印
(4, 8), -- 永久历史
(4, 9); -- API访问

-- 初始化系统配置
INSERT INTO `rb_config` (`config_key`, `config_value`, `config_group`, `description`, `create_time`) VALUES
('site_name', 'RedBookAI', 'site', '网站名称', NOW()),
('site_logo', '', 'site', '网站Logo', NOW()),
('site_keywords', '小红书,AI生成,内容创作', 'site', '网站关键词', NOW()),
('site_description', 'RedBookAI - 小红书AI创作助手', 'site', '网站描述', NOW()),

('gemini_api_key', '', 'api', 'Gemini API Key', NOW()),
('gemini_model', 'gemini-1.5-flash', 'api', 'Gemini模型', NOW()),

('free_daily_limit', '5', 'quota', '免费用户每日次数', NOW()),
('vip_daily_limit', '50', 'quota', 'VIP每日次数', NOW()),
('enterprise_daily_limit', '200', 'quota', '企业用户每日次数', NOW()),
('free_storage_limit', '104857600', 'quota', '免费用户存储空间（字节）', NOW()),
('vip_storage_limit', '5368709120', 'quota', 'VIP存储空间（字节）', NOW()),

('watermark_text', 'RedBookAI', 'image', '默认水印文字', NOW()),
('image_quality', '90', 'image', '图片质量（0-100）', NOW()),

('vip_month_price', '29.90', 'price', 'VIP月付价格', NOW()),
('vip_year_price', '299.00', 'price', 'VIP年付价格', NOW()),
('enterprise_price', '999.00', 'price', '企业版价格', NOW()),

('register_points', '100', 'points', '注册赠送积分', NOW()),
('checkin_points', '10', 'points', '每日签到积分', NOW()),
('invite_points', '50', 'points', '邀请好友积分', NOW());

-- 创建默认管理员账号
-- 用户名: admin  密码: admin123 (密码已使用password_hash加密)
INSERT INTO `rb_user` (
  `username`,
  `password`,
  `nickname`,
  `role_id`,
  `generate_limit_daily`,
  `storage_limit`,
  `points`,
  `create_time`
) VALUES (
  'admin',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  '系统管理员',
  1,
  999,
  999999999,
  0,
  NOW()
);

-- =====================================================
-- 数据库初始化完成
-- =====================================================
