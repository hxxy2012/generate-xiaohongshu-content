<?php
declare(strict_types=1);

namespace app\index\controller;

use app\BaseController;
use app\index\model\Content;
use app\index\model\User;
use app\index\service\GeminiService;
use app\index\service\SensitiveWordService;
use think\facade\Session;
use think\Response;

/**
 * 内容生成控制器
 */
class Generate extends BaseController
{
    /**
     * 生成页面
     */
    public function index()
    {
        return view('generate/index');
    }

    /**
     * 生成小红书文案
     */
    public function createContent(): Response
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return json(['code' => 401, 'msg' => '请先登录']);
        }

        $data = request()->post();

        try {
            // 验证参数
            if (empty($data['keywords'])) {
                return json(['code' => 400, 'msg' => '请输入主题/关键词']);
            }

            // 获取用户
            $user = User::find($userId);
            if (!$user) {
                return json(['code' => 404, 'msg' => '用户不存在']);
            }

            // 检查配额
            if ($user->getRemainingQuota() <= 0) {
                return json([
                    'code' => 403,
                    'msg'  => '今日生成次数已用完',
                    'data' => [
                        'remaining' => 0,
                        'limit'     => $user->generate_limit_daily,
                    ]
                ]);
            }

            // 准备生成参数
            $params = [
                'user_id'         => $userId,
                'keywords'        => $data['keywords'],
                'content_type'    => $data['content_type'] ?? 'grass',
                'style'           => $data['style'] ?? 'casual',
                'word_count'      => (int)($data['word_count'] ?? 300),
                'target_audience' => $data['target_audience'] ?? '所有人',
                'tag_count'       => (int)($data['tag_count'] ?? 5),
                'include_emoji'   => !empty($data['include_emoji']),
            ];

            // 先创建内容记录（草稿状态）
            $content = Content::create([
                'user_id'         => $userId,
                'keywords'        => $params['keywords'],
                'content_type'    => $params['content_type'],
                'style'           => $params['style'],
                'word_count'      => $params['word_count'],
                'target_audience' => $params['target_audience'],
                'generate_params' => $params,
                'ai_model'        => config('gemini.model'),
                'status'          => Content::STATUS_DRAFT,
            ]);

            // 调用Gemini服务生成内容
            $geminiService = new GeminiService();
            $result = $geminiService->generateContent($params);

            if (!$result['success']) {
                // 生成失败，更新状态
                $content->status = Content::STATUS_FAILED;
                $content->error_message = $result['error'];
                $content->save();

                return json([
                    'code' => 500,
                    'msg'  => '生成失败：' . $result['error'],
                ]);
            }

            // 敏感词过滤
            $sensitiveService = new SensitiveWordService();
            $filteredData = $sensitiveService->filterContent($result['data']);

            // 更新内容记录
            $content->title = $filteredData['title'];
            $content->content = $filteredData['content'];
            $content->tags = $filteredData['tags'];
            $content->cover_text = $filteredData['cover_text'];
            $content->token_used = $result['token_used'];
            $content->status = Content::STATUS_GENERATED;
            $content->generate_time = date('Y-m-d H:i:s');
            $content->save();

            // 消耗用户配额
            $user->consumeQuota(1);

            return json([
                'code' => 200,
                'msg'  => '生成成功',
                'data' => [
                    'content_id' => $content->id,
                    'title'      => $content->title,
                    'content'    => $content->content,
                    'tags'       => $content->tags,
                    'cover_text' => $content->cover_text,
                    'token_used' => $content->token_used,
                    'remaining'  => $user->getRemainingQuota(),
                ]
            ]);

        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'msg'  => '系统错误：' . $e->getMessage(),
            ]);
        }
    }

    /**
     * 重新生成
     */
    public function regenerate(): Response
    {
        $contentId = request()->post('content_id');
        $userId = Session::get('user_id');

        if (!$contentId) {
            return json(['code' => 400, 'msg' => '内容ID不能为空']);
        }

        $content = Content::where('id', $contentId)
            ->where('user_id', $userId)
            ->find();

        if (!$content) {
            return json(['code' => 404, 'msg' => '内容不存在']);
        }

        // 获取原参数，重新生成
        $params = $content->generate_params;
        $params['user_id'] = $userId;

        $user = User::find($userId);
        if ($user->getRemainingQuota() <= 0) {
            return json(['code' => 403, 'msg' => '今日生成次数已用完']);
        }

        // 调用Gemini服务
        $geminiService = new GeminiService();
        $result = $geminiService->generateContent($params);

        if (!$result['success']) {
            return json(['code' => 500, 'msg' => '生成失败：' . $result['error']]);
        }

        // 敏感词过滤
        $sensitiveService = new SensitiveWordService();
        $filteredData = $sensitiveService->filterContent($result['data']);

        // 更新内容
        $content->title = $filteredData['title'];
        $content->content = $filteredData['content'];
        $content->tags = $filteredData['tags'];
        $content->cover_text = $filteredData['cover_text'];
        $content->token_used = $result['token_used'];
        $content->status = Content::STATUS_GENERATED;
        $content->generate_time = date('Y-m-d H:i:s');
        $content->save();

        // 消耗配额
        $user->consumeQuota(1);

        return json([
            'code' => 200,
            'msg'  => '重新生成成功',
            'data' => [
                'title'      => $content->title,
                'content'    => $content->content,
                'tags'       => $content->tags,
                'cover_text' => $content->cover_text,
                'remaining'  => $user->getRemainingQuota(),
            ]
        ]);
    }

    /**
     * 编辑内容
     */
    public function update(): Response
    {
        $data = request()->post();
        $userId = Session::get('user_id');

        if (empty($data['content_id'])) {
            return json(['code' => 400, 'msg' => '内容ID不能为空']);
        }

        $content = Content::where('id', $data['content_id'])
            ->where('user_id', $userId)
            ->find();

        if (!$content) {
            return json(['code' => 404, 'msg' => '内容不存在']);
        }

        // 更新允许编辑的字段
        if (isset($data['title'])) {
            $content->title = $data['title'];
        }
        if (isset($data['content'])) {
            $content->content = $data['content'];
        }
        if (isset($data['tags'])) {
            $content->tags = $data['tags'];
        }
        if (isset($data['cover_text'])) {
            $content->cover_text = $data['cover_text'];
        }

        $content->save();

        return json(['code' => 200, 'msg' => '更新成功']);
    }

    /**
     * 删除内容（软删除）
     */
    public function delete(): Response
    {
        $contentId = request()->post('content_id');
        $userId = Session::get('user_id');

        if (!$contentId) {
            return json(['code' => 400, 'msg' => '内容ID不能为空']);
        }

        $content = Content::where('id', $contentId)
            ->where('user_id', $userId)
            ->find();

        if (!$content) {
            return json(['code' => 404, 'msg' => '内容不存在']);
        }

        $content->delete();

        return json(['code' => 200, 'msg' => '删除成功']);
    }

    /**
     * 收藏/取消收藏
     */
    public function toggleFavorite(): Response
    {
        $contentId = request()->post('content_id');
        $userId = Session::get('user_id');

        if (!$contentId) {
            return json(['code' => 400, 'msg' => '内容ID不能为空']);
        }

        $content = Content::where('id', $contentId)
            ->where('user_id', $userId)
            ->find();

        if (!$content) {
            return json(['code' => 404, 'msg' => '内容不存在']);
        }

        $content->is_favorite = $content->is_favorite ? 0 : 1;
        $content->save();

        return json([
            'code' => 200,
            'msg'  => $content->is_favorite ? '已收藏' : '已取消收藏',
            'data' => ['is_favorite' => $content->is_favorite]
        ]);
    }

    /**
     * 我的内容列表
     */
    public function myContents(): Response
    {
        $userId = Session::get('user_id');
        $page = (int)request()->param('page', 1);
        $limit = (int)request()->param('limit', 20);
        $status = request()->param('status');
        $isFavorite = request()->param('is_favorite');

        $where = [['user_id', '=', $userId]];

        if ($status !== null && $status !== '') {
            $where[] = ['status', '=', $status];
        }

        if ($isFavorite !== null && $isFavorite !== '') {
            $where[] = ['is_favorite', '=', $isFavorite];
        }

        $list = Content::where($where)
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
     * 内容详情
     */
    public function detail(): Response
    {
        $contentId = request()->param('content_id');
        $userId = Session::get('user_id');

        if (!$contentId) {
            return json(['code' => 400, 'msg' => '内容ID不能为空']);
        }

        $content = Content::with(['images'])
            ->where('id', $contentId)
            ->where('user_id', $userId)
            ->find();

        if (!$content) {
            return json(['code' => 404, 'msg' => '内容不存在']);
        }

        return json([
            'code' => 200,
            'msg'  => 'success',
            'data' => $content,
        ]);
    }
}
