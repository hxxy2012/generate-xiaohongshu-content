<?php
declare(strict_types=1);

namespace app\index\service;

use app\index\model\Content;
use app\index\model\Template;
use app\index\model\Image;
use app\index\model\User;
use think\facade\Log;

/**
 * 图片生成服务
 */
class ImageService
{
    // 默认字体文件路径
    private string $defaultFontPath = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';

    /**
     * 生成图片
     *
     * @param int $contentId 内容ID
     * @param int $templateId 模板ID
     * @param int $userId 用户ID
     * @param array $options 选项
     * @return array
     */
    public function generateImages(int $contentId, int $templateId, int $userId, array $options = []): array
    {
        try {
            // 获取内容
            $content = Content::find($contentId);
            if (!$content) {
                return ['success' => false, 'error' => '内容不存在'];
            }

            // 获取模板
            $template = Template::find($templateId);
            if (!$template) {
                return ['success' => false, 'error' => '模板不存在'];
            }

            // 获取用户
            $user = User::find($userId);
            if (!$user) {
                return ['success' => false, 'error' => '用户不存在'];
            }

            // 检查模板权限
            if (!$template->checkPermission($user)) {
                return ['success' => false, 'error' => '您没有权限使用此模板'];
            }

            // 获取模板配置
            $config = is_array($template->config) ? $template->config : json_decode($template->config, true);
            if (!$config) {
                return ['success' => false, 'error' => '模板配置错误'];
            }

            // 准备内容数据
            $contentData = [
                'title'      => $content->title ?? '',
                'content'    => $content->content ?? '',
                'tags'       => is_array($content->tags) ? implode(' ', $content->tags) : $content->tags,
                'cover_text' => $content->cover_text ?? '',
            ];

            // 文案分页（如果内容过长）
            $pages = $this->splitContent($contentData, $config);

            $generatedImages = [];

            foreach ($pages as $index => $pageData) {
                // 渲染图片
                $image = $this->renderImage($pageData, $config, $options);

                if (!$image) {
                    continue;
                }

                // 保存图片文件
                $savedImage = $this->saveImage($image, $content, $template, $user, $index + 1, $options);

                if ($savedImage) {
                    $generatedImages[] = $savedImage;
                }

                // 释放内存
                imagedestroy($image);
            }

            // 更新模板使用次数
            $template->incrementUseCount();

            return [
                'success' => true,
                'images'  => $generatedImages,
            ];

        } catch (\Exception $e) {
            Log::error('生成图片失败：' . $e->getMessage());
            return [
                'success' => false,
                'error'   => '生成图片失败：' . $e->getMessage(),
            ];
        }
    }

    /**
     * 渲染图片
     *
     * @param array $contentData
     * @param array $config
     * @param array $options
     * @return resource|false
     */
    protected function renderImage(array $contentData, array $config, array $options)
    {
        $width = $config['width'] ?? 1080;
        $height = $config['height'] ?? 1440;

        // 创建画布
        $canvas = imagecreatetruecolor($width, $height);
        if (!$canvas) {
            return false;
        }

        // 绘制背景
        $this->drawBackground($canvas, $config['background'] ?? [], $width, $height);

        // 渲染所有元素
        if (isset($config['elements']) && is_array($config['elements'])) {
            foreach ($config['elements'] as $element) {
                $this->renderElement($canvas, $element, $contentData);
            }
        }

        // 添加水印
        if (!empty($options['watermark']) && empty($options['remove_watermark'])) {
            $this->addWatermark($canvas, $width, $height, $options['watermark_text'] ?? 'RedBookAI');
        }

        return $canvas;
    }

    /**
     * 绘制背景
     *
     * @param resource $canvas
     * @param array $bgConfig
     * @param int $width
     * @param int $height
     */
    protected function drawBackground($canvas, array $bgConfig, int $width, int $height): void
    {
        $type = $bgConfig['type'] ?? 'color';

        if ($type === 'color') {
            // 纯色背景
            $colorHex = $bgConfig['value'] ?? '#FFFFFF';
            $color = $this->hexToRgb($colorHex);
            $bgColor = imagecolorallocate($canvas, $color[0], $color[1], $color[2]);
            imagefilledrectangle($canvas, 0, 0, $width, $height, $bgColor);

        } elseif ($type === 'gradient') {
            // 渐变背景
            $this->drawGradient($canvas, $bgConfig, $width, $height);

        } elseif ($type === 'image') {
            // 图片背景
            $imagePath = $bgConfig['value'] ?? '';
            if ($imagePath && file_exists($imagePath)) {
                $bgImage = $this->loadImage($imagePath);
                if ($bgImage) {
                    imagecopyresampled($canvas, $bgImage, 0, 0, 0, 0, $width, $height, imagesx($bgImage), imagesy($bgImage));
                    imagedestroy($bgImage);
                }
            }
        }
    }

    /**
     * 绘制渐变背景
     */
    protected function drawGradient($canvas, array $config, int $width, int $height): void
    {
        $colors = $config['colors'] ?? ['#FFFFFF', '#F0F0F0'];
        $direction = $config['direction'] ?? 'vertical';

        $startColor = $this->hexToRgb($colors[0]);
        $endColor = $this->hexToRgb($colors[count($colors) - 1]);

        if ($direction === 'vertical') {
            for ($y = 0; $y < $height; $y++) {
                $ratio = $y / $height;
                $r = (int)($startColor[0] + ($endColor[0] - $startColor[0]) * $ratio);
                $g = (int)($startColor[1] + ($endColor[1] - $startColor[1]) * $ratio);
                $b = (int)($startColor[2] + ($endColor[2] - $startColor[2]) * $ratio);
                $color = imagecolorallocate($canvas, $r, $g, $b);
                imageline($canvas, 0, $y, $width, $y, $color);
            }
        } else {
            // horizontal
            for ($x = 0; $x < $width; $x++) {
                $ratio = $x / $width;
                $r = (int)($startColor[0] + ($endColor[0] - $startColor[0]) * $ratio);
                $g = (int)($startColor[1] + ($endColor[1] - $startColor[1]) * $ratio);
                $b = (int)($startColor[2] + ($endColor[2] - $startColor[2]) * $ratio);
                $color = imagecolorallocate($canvas, $r, $g, $b);
                imageline($canvas, $x, 0, $x, $height, $color);
            }
        }
    }

    /**
     * 渲染元素
     */
    protected function renderElement($canvas, array $element, array $contentData): void
    {
        $type = $element['type'] ?? '';

        if ($type === 'text') {
            $field = $element['field'] ?? '';
            $text = $contentData[$field] ?? '';
            if ($text) {
                $this->drawText($canvas, $text, $element);
            }
        }
    }

    /**
     * 绘制文字（支持自动换行）
     */
    protected function drawText($canvas, string $text, array $config): void
    {
        $fontSize = $config['font_size'] ?? 32;
        $color = $this->hexToRgb($config['color'] ?? '#000000');
        $textColor = imagecolorallocate($canvas, $color[0], $color[1], $color[2]);

        $x = $config['position']['x'] ?? 0;
        $y = $config['position']['y'] ?? 0;
        $maxWidth = $config['width'] ?? 1000;
        $lineHeight = $fontSize * ($config['line_height'] ?? 1.5);
        $maxLines = $config['max_lines'] ?? 999;
        $align = $config['align'] ?? 'left';

        // 检查字体文件
        $fontPath = $this->getFontPath();
        if (!file_exists($fontPath)) {
            Log::error('字体文件不存在：' . $fontPath);
            return;
        }

        // 自动换行
        $lines = $this->wrapText($text, $fontPath, $fontSize, $maxWidth);

        // 限制最大行数
        if (count($lines) > $maxLines) {
            $lines = array_slice($lines, 0, $maxLines);
            $lines[$maxLines - 1] .= '...';
        }

        // 逐行绘制
        $currentY = $y;
        foreach ($lines as $line) {
            // 计算对齐位置
            $lineX = $x;
            if ($align === 'center') {
                $bbox = imagettfbbox($fontSize, 0, $fontPath, $line);
                $lineWidth = $bbox[2] - $bbox[0];
                $lineX = $x + ($maxWidth - $lineWidth) / 2;
            } elseif ($align === 'right') {
                $bbox = imagettfbbox($fontSize, 0, $fontPath, $line);
                $lineWidth = $bbox[2] - $bbox[0];
                $lineX = $x + $maxWidth - $lineWidth;
            }

            // 绘制阴影（如果有）
            if (!empty($config['shadow'])) {
                $shadowColor = $this->hexToRgb($config['shadow']['color'] ?? '#00000033');
                $shadowAlpha = hexdec(substr($config['shadow']['color'] ?? '#00000033', -2)) / 255 * 127;
                $shadowColorAllocated = imagecolorallocatealpha(
                    $canvas,
                    $shadowColor[0],
                    $shadowColor[1],
                    $shadowColor[2],
                    (int)$shadowAlpha
                );
                imagettftext(
                    $canvas,
                    $fontSize,
                    0,
                    $lineX + ($config['shadow']['x'] ?? 2),
                    $currentY + $fontSize + ($config['shadow']['y'] ?? 2),
                    $shadowColorAllocated,
                    $fontPath,
                    $line
                );
            }

            // 绘制文字
            imagettftext($canvas, $fontSize, 0, $lineX, $currentY + $fontSize, $textColor, $fontPath, $line);

            $currentY += $lineHeight;
        }
    }

    /**
     * 文字自动换行
     */
    protected function wrapText(string $text, string $fontPath, int $fontSize, int $maxWidth): array
    {
        $lines = [];
        $words = preg_split('/(?<!^)(?!$)/u', $text); // 按字符分割（支持中文）
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine . $word;
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $testLine);
            $lineWidth = $bbox[2] - $bbox[0];

            if ($lineWidth > $maxWidth && $currentLine !== '') {
                $lines[] = $currentLine;
                $currentLine = $word;
            } else {
                $currentLine = $testLine;
            }
        }

        if ($currentLine !== '') {
            $lines[] = $currentLine;
        }

        return $lines;
    }

    /**
     * 添加水印
     */
    protected function addWatermark($canvas, int $width, int $height, string $watermarkText): void
    {
        $fontSize = 24;
        $fontPath = $this->getFontPath();

        if (!file_exists($fontPath)) {
            return;
        }

        $textColor = imagecolorallocatealpha($canvas, 255, 255, 255, 80);
        $bbox = imagettfbbox($fontSize, 0, $fontPath, $watermarkText);
        $textWidth = $bbox[2] - $bbox[0];

        $x = $width - $textWidth - 30;
        $y = $height - 40;

        imagettftext($canvas, $fontSize, 0, $x, $y, $textColor, $fontPath, $watermarkText);
    }

    /**
     * 保存图片
     */
    protected function saveImage($canvas, Content $content, Template $template, User $user, int $pageNumber, array $options): ?array
    {
        // 生成文件名
        $filename = 'img_' . $content->id . '_' . $template->id . '_' . $pageNumber . '_' . time() . '.jpg';
        $savePath = root_path('public') . 'uploads/images/' . date('Ym') . '/';

        // 创建目录
        if (!is_dir($savePath)) {
            mkdir($savePath, 0755, true);
        }

        $fullPath = $savePath . $filename;
        $relativePath = 'uploads/images/' . date('Ym') . '/' . $filename;

        // 保存为JPG
        $quality = $options['quality'] ?? config('image_quality', 90);
        $result = imagejpeg($canvas, $fullPath, (int)$quality);

        if (!$result) {
            return null;
        }

        // 获取文件信息
        $fileSize = filesize($fullPath);
        list($width, $height) = getimagesize($fullPath);

        // 保存到数据库
        $image = Image::create([
            'user_id'        => $user->id,
            'content_id'     => $content->id,
            'template_id'    => $template->id,
            'file_path'      => $relativePath,
            'file_name'      => $filename,
            'file_size'      => $fileSize,
            'file_url'       => '/' . $relativePath,
            'width'          => $width,
            'height'         => $height,
            'format'         => 'jpg',
            'has_watermark'  => !empty($options['watermark']) ? 1 : 0,
            'page_number'    => $pageNumber,
        ]);

        // 更新用户存储空间
        $user->storage_used += $fileSize;
        $user->save();

        return [
            'id'        => $image->id,
            'file_path' => $relativePath,
            'file_url'  => $image->file_url,
            'width'     => $width,
            'height'    => $height,
            'file_size' => $fileSize,
        ];
    }

    /**
     * 文案分页
     */
    protected function splitContent(array $contentData, array $config): array
    {
        // 简单实现：不分页，返回单页
        // 实际应该根据内容长度和模板配置智能分页
        return [$contentData];
    }

    /**
     * 十六进制颜色转RGB
     */
    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 8) {
            $hex = substr($hex, 0, 6); // 移除alpha通道
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * 加载图片
     */
    protected function loadImage(string $path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                return imagecreatefromjpeg($path);
            case 'png':
                return imagecreatefrompng($path);
            case 'gif':
                return imagecreatefromgif($path);
            default:
                return false;
        }
    }

    /**
     * 获取字体文件路径
     */
    protected function getFontPath(): string
    {
        // 优先使用系统中文字体
        $fonts = [
            '/usr/share/fonts/truetype/wqy/wqy-microhei.ttc',
            '/usr/share/fonts/truetype/arphic/uming.ttc',
            '/usr/share/fonts/opentype/noto/NotoSansCJK-Regular.ttc',
            '/System/Library/Fonts/PingFang.ttc',
            $this->defaultFontPath,
        ];

        foreach ($fonts as $font) {
            if (file_exists($font)) {
                return $font;
            }
        }

        return $this->defaultFontPath;
    }
}
