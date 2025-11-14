-- =====================================================
-- RedBookAI - 初始化模板数据
-- =====================================================

USE `redbook_ai`;

-- 清空现有模板数据
TRUNCATE TABLE `rb_template`;

-- 插入基础模板数据
INSERT INTO `rb_template` (
  `template_name`,
  `template_code`,
  `category`,
  `scene`,
  `preview_image`,
  `config`,
  `width`,
  `height`,
  `permission_level`,
  `sort`,
  `status`,
  `create_time`
) VALUES

-- 1. 粉色少女心
(
  '粉色少女心',
  'pink_girl',
  'basic',
  '美妆、穿搭、生活',
  '/static/templates/basic/pink_girl_preview.jpg',
  '{"template_name":"粉色少女心","width":1080,"height":1440,"background":{"type":"gradient","colors":["#FFB6C1","#FFC0CB","#FFB6D9"],"direction":"vertical"},"elements":[{"type":"text","field":"title","position":{"x":60,"y":120},"width":960,"height":200,"font":"default","font_size":52,"color":"#FFFFFF","align":"left","line_height":1.5,"max_lines":2,"bold":true,"shadow":{"x":2,"y":2,"blur":5,"color":"#00000033"}},{"type":"text","field":"content","position":{"x":80,"y":380},"width":920,"height":900,"font":"default","font_size":36,"color":"#333333","align":"left","line_height":1.8,"max_lines":20,"bold":false},{"type":"text","field":"tags","position":{"x":80,"y":1320},"width":920,"height":80,"font":"default","font_size":30,"color":"#FF69B4","align":"left","line_height":1.5,"max_lines":2,"bold":false}]}',
  1080,
  1440,
  1,
  1,
  1,
  NOW()
),

-- 2. 蓝色清新
(
  '蓝色清新',
  'blue_fresh',
  'basic',
  '旅行、生活、知识',
  '/static/templates/basic/blue_fresh_preview.jpg',
  '{"template_name":"蓝色清新","width":1080,"height":1440,"background":{"type":"gradient","colors":["#87CEEB","#B0E0E6","#ADD8E6"],"direction":"vertical"},"elements":[{"type":"text","field":"title","position":{"x":60,"y":120},"width":960,"height":200,"font":"default","font_size":52,"color":"#FFFFFF","align":"center","line_height":1.5,"max_lines":2,"bold":true,"shadow":{"x":2,"y":2,"blur":5,"color":"#00000033"}},{"type":"text","field":"content","position":{"x":80,"y":380},"width":920,"height":900,"font":"default","font_size":36,"color":"#2C3E50","align":"left","line_height":1.8,"max_lines":20,"bold":false},{"type":"text","field":"tags","position":{"x":80,"y":1320},"width":920,"height":80,"font":"default","font_size":30,"color":"#4682B4","align":"left","line_height":1.5,"max_lines":2,"bold":false}]}',
  1080,
  1440,
  1,
  2,
  1,
  NOW()
),

-- 3. 黄色温暖
(
  '黄色温暖',
  'yellow_warm',
  'basic',
  '美食、生活、情感',
  '/static/templates/basic/yellow_warm_preview.jpg',
  '{"template_name":"黄色温暖","width":1080,"height":1440,"background":{"type":"gradient","colors":["#FFD700","#FFA500","#FFB347"],"direction":"vertical"},"elements":[{"type":"text","field":"title","position":{"x":60,"y":120},"width":960,"height":200,"font":"default","font_size":52,"color":"#FFFFFF","align":"center","line_height":1.5,"max_lines":2,"bold":true,"shadow":{"x":2,"y":2,"blur":5,"color":"#00000033"}},{"type":"text","field":"content","position":{"x":80,"y":380},"width":920,"height":900,"font":"default","font_size":36,"color":"#333333","align":"left","line_height":1.8,"max_lines":20,"bold":false},{"type":"text","field":"tags","position":{"x":80,"y":1320},"width":920,"height":80,"font":"default","font_size":30,"color":"#FF8C00","align":"left","line_height":1.5,"max_lines":2,"bold":false}]}',
  1080,
  1440,
  1,
  3,
  1,
  NOW()
),

-- 4. 白色简约
(
  '白色简约',
  'white_simple',
  'basic',
  '知识、干货、专业',
  '/static/templates/basic/white_simple_preview.jpg',
  '{"template_name":"白色简约","width":1080,"height":1440,"background":{"type":"color","value":"#FFFFFF"},"elements":[{"type":"text","field":"title","position":{"x":60,"y":120},"width":960,"height":200,"font":"default","font_size":52,"color":"#333333","align":"left","line_height":1.5,"max_lines":2,"bold":true},{"type":"text","field":"content","position":{"x":80,"y":380},"width":920,"height":900,"font":"default","font_size":36,"color":"#666666","align":"left","line_height":1.8,"max_lines":20,"bold":false},{"type":"text","field":"tags","position":{"x":80,"y":1320},"width":920,"height":80,"font":"default","font_size":30,"color":"#999999","align":"left","line_height":1.5,"max_lines":2,"bold":false}]}',
  1080,
  1440,
  1,
  4,
  1,
  NOW()
),

-- 5. 黑色高级
(
  '黑色高级',
  'black_luxury',
  'basic',
  '时尚、高端、品牌',
  '/static/templates/basic/black_luxury_preview.jpg',
  '{"template_name":"黑色高级","width":1080,"height":1440,"background":{"type":"color","value":"#1a1a1a"},"elements":[{"type":"text","field":"title","position":{"x":60,"y":120},"width":960,"height":200,"font":"default","font_size":52,"color":"#FFD700","align":"center","line_height":1.5,"max_lines":2,"bold":true},{"type":"text","field":"content","position":{"x":80,"y":380},"width":920,"height":900,"font":"default","font_size":36,"color":"#FFFFFF","align":"left","line_height":1.8,"max_lines":20,"bold":false},{"type":"text","field":"tags","position":{"x":80,"y":1320},"width":920,"height":80,"font":"default","font_size":30,"color":"#FFD700","align":"left","line_height":1.5,"max_lines":2,"bold":false}]}',
  1080,
  1440,
  1,
  5,
  1,
  NOW()
),

-- 6. 紫色梦幻（VIP）
(
  '紫色梦幻',
  'purple_dream',
  'premium',
  '美妆、时尚、梦幻',
  '/static/templates/premium/purple_dream_preview.jpg',
  '{"template_name":"紫色梦幻","width":1080,"height":1440,"background":{"type":"gradient","colors":["#9370DB","#BA55D3","#DDA0DD"],"direction":"vertical"},"elements":[{"type":"text","field":"title","position":{"x":60,"y":120},"width":960,"height":200,"font":"default","font_size":52,"color":"#FFFFFF","align":"center","line_height":1.5,"max_lines":2,"bold":true,"shadow":{"x":2,"y":2,"blur":5,"color":"#00000033"}},{"type":"text","field":"content","position":{"x":80,"y":380},"width":920,"height":900,"font":"default","font_size":36,"color":"#FFFFFF","align":"left","line_height":1.8,"max_lines":20,"bold":false},{"type":"text","field":"tags","position":{"x":80,"y":1320},"width":920,"height":80,"font":"default","font_size":30,"color":"#FFD700","align":"left","line_height":1.5,"max_lines":2,"bold":false}]}',
  1080,
  1440,
  2,
  6,
  1,
  NOW()
),

-- 7. 绿色生机（VIP）
(
  '绿色生机',
  'green_fresh',
  'premium',
  '健康、运动、自然',
  '/static/templates/premium/green_fresh_preview.jpg',
  '{"template_name":"绿色生机","width":1080,"height":1440,"background":{"type":"gradient","colors":["#90EE90","#98FB98","#00FA9A"],"direction":"vertical"},"elements":[{"type":"text","field":"title","position":{"x":60,"y":120},"width":960,"height":200,"font":"default","font_size":52,"color":"#FFFFFF","align":"left","line_height":1.5,"max_lines":2,"bold":true,"shadow":{"x":2,"y":2,"blur":5,"color":"#00000033"}},{"type":"text","field":"content","position":{"x":80,"y":380},"width":920,"height":900,"font":"default","font_size":36,"color":"#2F4F2F","align":"left","line_height":1.8,"max_lines":20,"bold":false},{"type":"text","field":"tags","position":{"x":80,"y":1320},"width":920,"height":80,"font":"default","font_size":30,"color":"#228B22","align":"left","line_height":1.5,"max_lines":2,"bold":false}]}',
  1080,
  1440,
  2,
  7,
  1,
  NOW()
),

-- 8. 橙色活力（VIP）
(
  '橙色活力',
  'orange_energy',
  'premium',
  '运动、活力、青春',
  '/static/templates/premium/orange_energy_preview.jpg',
  '{"template_name":"橙色活力","width":1080,"height":1440,"background":{"type":"gradient","colors":["#FF8C00","#FFA500","#FFB366"],"direction":"vertical"},"elements":[{"type":"text","field":"title","position":{"x":60,"y":120},"width":960,"height":200,"font":"default","font_size":52,"color":"#FFFFFF","align":"center","line_height":1.5,"max_lines":2,"bold":true,"shadow":{"x":2,"y":2,"blur":5,"color":"#00000033"}},{"type":"text","field":"content","position":{"x":80,"y":380},"width":920,"height":900,"font":"default","font_size":36,"color":"#FFFFFF","align":"left","line_height":1.8,"max_lines":20,"bold":false},{"type":"text","field":"tags","position":{"x":80,"y":1320},"width":920,"height":80,"font":"default","font_size":30,"color":"#FFFFFF","align":"left","line_height":1.5,"max_lines":2,"bold":false}]}',
  1080,
  1440,
  2,
  8,
  1,
  NOW()
),

-- 9. 红色热情（VIP）
(
  '红色热情',
  'red_passion',
  'premium',
  '节日、促销、热门',
  '/static/templates/premium/red_passion_preview.jpg',
  '{"template_name":"红色热情","width":1080,"height":1440,"background":{"type":"gradient","colors":["#DC143C","#FF6347","#FF7F7F"],"direction":"vertical"},"elements":[{"type":"text","field":"title","position":{"x":60,"y":120},"width":960,"height":200,"font":"default","font_size":52,"color":"#FFFFFF","align":"center","line_height":1.5,"max_lines":2,"bold":true,"shadow":{"x":2,"y":2,"blur":5,"color":"#00000033"}},{"type":"text","field":"content","position":{"x":80,"y":380},"width":920,"height":900,"font":"default","font_size":36,"color":"#FFFFFF","align":"left","line_height":1.8,"max_lines":20,"bold":false},{"type":"text","field":"tags","position":{"x":80,"y":1320},"width":920,"height":80,"font":"default","font_size":30,"color":"#FFD700","align":"left","line_height":1.5,"max_lines":2,"bold":false}]}',
  1080,
  1440,
  2,
  9,
  1,
  NOW()
),

-- 10. 灰色质感（VIP）
(
  '灰色质感',
  'gray_texture',
  'premium',
  '专业、商务、科技',
  '/static/templates/premium/gray_texture_preview.jpg',
  '{"template_name":"灰色质感","width":1080,"height":1440,"background":{"type":"gradient","colors":["#708090","#778899","#B0C4DE"],"direction":"vertical"},"elements":[{"type":"text","field":"title","position":{"x":60,"y":120},"width":960,"height":200,"font":"default","font_size":52,"color":"#FFFFFF","align":"left","line_height":1.5,"max_lines":2,"bold":true,"shadow":{"x":2,"y":2,"blur":5,"color":"#00000033"}},{"type":"text","field":"content","position":{"x":80,"y":380},"width":920,"height":900,"font":"default","font_size":36,"color":"#FFFFFF","align":"left","line_height":1.8,"max_lines":20,"bold":false},{"type":"text","field":"tags","position":{"x":80,"y":1320},"width":920,"height":80,"font":"default","font_size":30,"color":"#F0F0F0","align":"left","line_height":1.5,"max_lines":2,"bold":false}]}',
  1080,
  1440,
  2,
  10,
  1,
  NOW()
);

-- 更新统计
SELECT COUNT(*) as '已插入模板数量' FROM `rb_template`;
SELECT category, COUNT(*) as '数量' FROM `rb_template` GROUP BY category;

-- =====================================================
-- 模板初始化完成
-- =====================================================
