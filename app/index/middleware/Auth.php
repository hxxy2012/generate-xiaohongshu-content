<?php
declare(strict_types=1);

namespace app\index\middleware;

use think\facade\Session;
use think\Response;

/**
 * 登录认证中间件
 */
class Auth
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
        // 检查Session中是否有用户ID
        $userId = Session::get('user_id');

        if (!$userId) {
            // 检查是否有记住登录的Cookie
            $rememberToken = cookie('remember_token');
            if ($rememberToken) {
                // 验证记住登录token
                // 这里简化处理，实际应该更严格
                if ($this->verifyRememberToken($rememberToken)) {
                    return $next($request);
                }
            }

            // 未登录，返回错误
            if ($request->isAjax()) {
                return json(['code' => 401, 'msg' => '请先登录']);
            }
            return redirect('/auth/login');
        }

        // 将用户ID注入到请求中
        $request->userId = $userId;

        return $next($request);
    }

    /**
     * 验证记住登录token
     */
    protected function verifyRememberToken($token): bool
    {
        // 简化实现，实际应该查询数据库验证
        return !empty($token);
    }
}
