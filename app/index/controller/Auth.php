<?php
declare(strict_types=1);

namespace app\index\controller;

use app\BaseController;
use app\index\model\User;
use app\index\model\Role;
use app\index\validate\RegisterValidate;
use app\index\validate\LoginValidate;
use think\facade\Session;
use think\Response;

/**
 * 用户认证控制器
 */
class Auth extends BaseController
{
    /**
     * 注册页面
     */
    public function register()
    {
        if (request()->isPost()) {
            return $this->doRegister();
        }

        return view('auth/register');
    }

    /**
     * 执行注册
     */
    protected function doRegister(): Response
    {
        $data = request()->post();

        try {
            // 验证数据
            validate(RegisterValidate::class)->check($data);

            // 检查用户名是否存在
            if (User::where('username', $data['username'])->find()) {
                return json(['code' => 400, 'msg' => '用户名已存在']);
            }

            // 检查手机号是否存在
            if (!empty($data['phone']) && User::where('phone', $data['phone'])->find()) {
                return json(['code' => 400, 'msg' => '手机号已被注册']);
            }

            // 检查邮箱是否存在
            if (!empty($data['email']) && User::where('email', $data['email'])->find()) {
                return json(['code' => 400, 'msg' => '邮箱已被注册']);
            }

            // 生成邀请码
            $inviteCode = $this->generateUniqueInviteCode();

            // 处理邀请码
            $inviterId = null;
            if (!empty($data['invite_code'])) {
                $inviter = User::where('invite_code', $data['invite_code'])->find();
                if ($inviter) {
                    $inviterId = $inviter->id;
                }
            }

            // 创建用户
            $user = User::create([
                'username'     => $data['username'],
                'phone'        => $data['phone'] ?? null,
                'email'        => $data['email'] ?? null,
                'password'     => $data['password'],
                'nickname'     => $data['nickname'] ?? $data['username'],
                'role_id'      => 2, // 免费用户
                'invite_code'  => $inviteCode,
                'inviter_id'   => $inviterId,
                'points'       => 100, // 注册赠送积分
                'create_time'  => date('Y-m-d H:i:s'),
            ]);

            // 记录积分
            if ($user) {
                $user->addPoints(100, 'register', '注册赠送');

                // 邀请人获得积分
                if ($inviterId) {
                    $inviter->addPoints(50, 'invite', '邀请好友注册', $user->id);
                }

                return json(['code' => 200, 'msg' => '注册成功', 'data' => ['user_id' => $user->id]]);
            }

            return json(['code' => 500, 'msg' => '注册失败，请稍后重试']);

        } catch (\think\exception\ValidateException $e) {
            return json(['code' => 400, 'msg' => $e->getError()]);
        } catch (\Exception $e) {
            return json(['code' => 500, 'msg' => '系统错误：' . $e->getMessage()]);
        }
    }

    /**
     * 登录页面
     */
    public function login()
    {
        if (request()->isPost()) {
            return $this->doLogin();
        }

        return view('auth/login');
    }

    /**
     * 执行登录
     */
    protected function doLogin(): Response
    {
        $data = request()->post();

        try {
            // 验证数据
            validate(LoginValidate::class)->check($data);

            $account = $data['account'];
            $password = $data['password'];

            // 查找用户（支持用户名/手机号/邮箱登录）
            $user = User::where(function ($query) use ($account) {
                $query->where('username', $account)
                      ->whereOr('phone', $account)
                      ->whereOr('email', $account);
            })->find();

            if (!$user) {
                return json(['code' => 400, 'msg' => '账号不存在']);
            }

            // 检查账号状态
            if ($user->status != 1) {
                return json(['code' => 400, 'msg' => '账号已被禁用']);
            }

            // 验证密码
            if (!$user->checkPassword($password)) {
                return json(['code' => 400, 'msg' => '密码错误']);
            }

            // 更新登录信息
            $user->last_login_time = date('Y-m-d H:i:s');
            $user->last_login_ip = request()->ip();
            $user->save();

            // 设置Session
            Session::set('user_id', $user->id);
            Session::set('username', $user->username);
            Session::set('role_id', $user->role_id);

            // 是否记住登录
            if (!empty($data['remember'])) {
                cookie('remember_token', md5($user->id . $user->password), 7 * 24 * 3600);
            }

            return json([
                'code' => 200,
                'msg'  => '登录成功',
                'data' => [
                    'user' => [
                        'id'       => $user->id,
                        'username' => $user->username,
                        'nickname' => $user->nickname,
                        'avatar'   => $user->avatar,
                        'role_id'  => $user->role_id,
                    ]
                ]
            ]);

        } catch (\think\exception\ValidateException $e) {
            return json(['code' => 400, 'msg' => $e->getError()]);
        } catch (\Exception $e) {
            return json(['code' => 500, 'msg' => '系统错误：' . $e->getMessage()]);
        }
    }

    /**
     * 退出登录
     */
    public function logout(): Response
    {
        Session::clear();
        cookie('remember_token', null);
        return json(['code' => 200, 'msg' => '退出成功']);
    }

    /**
     * 获取当前登录用户信息
     */
    public function userInfo(): Response
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return json(['code' => 401, 'msg' => '未登录']);
        }

        $user = User::with('role')->find($userId);
        if (!$user) {
            return json(['code' => 404, 'msg' => '用户不存在']);
        }

        return json([
            'code' => 200,
            'msg'  => 'success',
            'data' => [
                'id'                  => $user->id,
                'username'            => $user->username,
                'nickname'            => $user->nickname,
                'avatar'              => $user->avatar,
                'phone'               => $user->phone,
                'email'               => $user->email,
                'role'                => $user->role ? $user->role->role_name : '',
                'role_id'             => $user->role_id,
                'points'              => $user->points,
                'generate_limit_daily'=> $user->generate_limit_daily,
                'generate_used_today' => $user->generate_used_today,
                'generate_total'      => $user->generate_total,
                'remaining_quota'     => $user->getRemainingQuota(),
                'is_vip'              => $user->isVip(),
                'vip_expire_time'     => $user->vip_expire_time,
                'storage_limit'       => $user->storage_limit,
                'storage_used'        => $user->storage_used,
                'invite_code'         => $user->invite_code,
            ]
        ]);
    }

    /**
     * 生成唯一邀请码
     */
    protected function generateUniqueInviteCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 8));
        } while (User::where('invite_code', $code)->find());

        return $code;
    }
}
