<?php
declare(strict_types=1);

namespace app\index\controller;

use app\BaseController;
use app\index\model\Image;
use app\index\model\Template;
use app\index\service\ImageService;
use think\facade\Session;
use think\Response;

/**
 * 图片控制器
 */
class ImageController extends BaseController
{
    /**
     * 生成图片
     */
    public function generate(): Response
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return json(['code' => 401, 'msg' => '请先登录']);
        }

        $contentId = (int)request()->post('content_id');
        $templateId = (int)request()->post('template_id');

        if (!$contentId || !$templateId) {
            return json(['code' => 400, 'msg' => '参数错误']);
        }

        // 获取选项
        $options = [
            'watermark'        => request()->post('watermark', true),
            'watermark_text'   => request()->post('watermark_text', 'RedBookAI'),
            'quality'          => (int)request()->post('quality', 90),
            'remove_watermark' => false, // 根据用户权限设置
        ];

        // 调用图片生成服务
        $imageService = new ImageService();
        $result = $imageService->generateImages($contentId, $templateId, $userId, $options);

        if (!$result['success']) {
            return json([
                'code' => 500,
                'msg'  => $result['error'] ?? '生成图片失败',
            ]);
        }

        return json([
            'code' => 200,
            'msg'  => '生成成功',
            'data' => [
                'images' => $result['images'],
                'count'  => count($result['images']),
            ]
        ]);
    }

    /**
     * 获取模板列表
     */
    public function templates(): Response
    {
        $userId = Session::get('user_id');
        $category = request()->param('category');
        $permissionLevel = request()->param('permission_level');

        $where = [['status', '=', 1]];

        if ($category) {
            $where[] = ['category', '=', $category];
        }

        if ($permissionLevel !== null) {
            $where[] = ['permission_level', '<=', $permissionLevel];
        }

        $templates = Template::where($where)
            ->order('sort', 'asc')
            ->order('id', 'asc')
            ->select();

        return json([
            'code' => 200,
            'msg'  => 'success',
            'data' => $templates,
        ]);
    }

    /**
     * 获取模板详情
     */
    public function templateDetail(): Response
    {
        $templateId = (int)request()->param('template_id');

        if (!$templateId) {
            return json(['code' => 400, 'msg' => '模板ID不能为空']);
        }

        $template = Template::find($templateId);

        if (!$template) {
            return json(['code' => 404, 'msg' => '模板不存在']);
        }

        return json([
            'code' => 200,
            'msg'  => 'success',
            'data' => $template,
        ]);
    }

    /**
     * 我的图片列表
     */
    public function myImages(): Response
    {
        $userId = Session::get('user_id');
        $page = (int)request()->param('page', 1);
        $limit = (int)request()->param('limit', 20);
        $contentId = request()->param('content_id');

        $where = [['user_id', '=', $userId]];

        if ($contentId) {
            $where[] = ['content_id', '=', $contentId];
        }

        $list = Image::with(['content', 'template'])
            ->where($where)
            ->order('create_time', 'desc')
            ->paginate([
                'list_rows' => $limit,
                'page'      => $page,
            ]);

        return json([
            'code' => 200,
            'msg'  => 'success',
            'data' => [
                'list'  => $list->items(),
                'total' => $list->total(),
                'page'  => $page,
                'limit' => $limit,
            ]
        ]);
    }

    /**
     * 下载图片
     */
    public function download(): Response
    {
        $imageId = (int)request()->param('image_id');
        $userId = Session::get('user_id');

        if (!$imageId) {
            return json(['code' => 400, 'msg' => '图片ID不能为空']);
        }

        $image = Image::where('id', $imageId)
            ->where('user_id', $userId)
            ->find();

        if (!$image) {
            return json(['code' => 404, 'msg' => '图片不存在']);
        }

        $filePath = root_path('public') . $image->file_path;

        if (!file_exists($filePath)) {
            return json(['code' => 404, 'msg' => '文件不存在']);
        }

        // 增加下载次数
        $image->incrementDownloadCount();

        // 返回文件下载
        return download($filePath, $image->file_name);
    }

    /**
     * 删除图片
     */
    public function delete(): Response
    {
        $imageId = (int)request()->post('image_id');
        $userId = Session::get('user_id');

        if (!$imageId) {
            return json(['code' => 400, 'msg' => '图片ID不能为空']);
        }

        $image = Image::where('id', $imageId)
            ->where('user_id', $userId)
            ->find();

        if (!$image) {
            return json(['code' => 404, 'msg' => '图片不存在']);
        }

        // 删除文件
        $filePath = root_path('public') . $image->file_path;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }

        // 删除数据库记录
        $image->delete();

        return json(['code' => 200, 'msg' => '删除成功']);
    }

    /**
     * 批量下载（打包为ZIP）
     */
    public function batchDownload(): Response
    {
        $imageIds = request()->post('image_ids');
        $userId = Session::get('user_id');

        if (empty($imageIds) || !is_array($imageIds)) {
            return json(['code' => 400, 'msg' => '请选择要下载的图片']);
        }

        $images = Image::where('user_id', $userId)
            ->whereIn('id', $imageIds)
            ->select();

        if ($images->isEmpty()) {
            return json(['code' => 404, 'msg' => '未找到图片']);
        }

        // 创建ZIP文件
        $zipName = 'images_' . time() . '.zip';
        $zipPath = root_path('runtime') . 'temp/' . $zipName;

        if (!is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
            return json(['code' => 500, 'msg' => '创建ZIP文件失败']);
        }

        foreach ($images as $image) {
            $filePath = root_path('public') . $image->file_path;
            if (file_exists($filePath)) {
                $zip->addFile($filePath, $image->file_name);
            }
        }

        $zip->close();

        // 返回下载
        return download($zipPath, $zipName)->deleteFileAfterSend(true);
    }
}
