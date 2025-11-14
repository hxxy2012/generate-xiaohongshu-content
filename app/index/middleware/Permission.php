<?php
declare(strict_types=1);

namespace app\index\middleware;

use app\index\model\User;
use think\facade\Session;
use think\Response;

/**
 * 权限验证中间件
 */
class Permission
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @param string         $permission 权限编码
     * @return Response
     */
    public function handle($request, \Closure $next, string $permission = '')
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            if ($request->isAjax()) {
                return json(['code' => 401, 'msg' => '请先登录']);
            }
            return redirect('/auth/login');
        }

        // 管理员直接通过
        $roleId = Session::get('role_id');
        if ($roleId === 1) {
            return $next($request);
        }

        // 检查权限
        if ($permission) {
            $user = User::with('role.permissions')->find($userId);
            if (!$user || !$user->hasPermission($permission)) {
                if ($request->isAjax()) {
                    return json(['code' => 403, 'msg' => '无权限访问']);
                }
                return response('无权限访问', 403);
            }
        }

        return $next($request);
    }
}
