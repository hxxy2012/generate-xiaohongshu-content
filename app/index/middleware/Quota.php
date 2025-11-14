<?php
declare(strict_types=1);

namespace app\index\middleware;

use app\index\model\User;
use think\facade\Session;
use think\Response;

/**
 * 配额验证中间件（检查用户生成次数）
 */
class Quota
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            if ($request->isAjax()) {
                return json(['code' => 401, 'msg' => '请先登录']);
            }
            return redirect('/auth/login');
        }

        $user = User::find($userId);
        if (!$user) {
            if ($request->isAjax()) {
                return json(['code' => 404, 'msg' => '用户不存在']);
            }
            return response('用户不存在', 404);
        }

        // 检查是否还有剩余次数
        if ($user->getRemainingQuota() <= 0) {
            if ($request->isAjax()) {
                return json([
                    'code' => 403,
                    'msg'  => '今日生成次数已用完，请明天再来或升级VIP',
                    'data' => [
                        'remaining' => 0,
                        'limit'     => $user->generate_limit_daily,
                    ]
                ]);
            }
            return response('今日生成次数已用完', 403);
        }

        // 将用户对象注入到请求中
        $request->user = $user;

        return $next($request);
    }
}
