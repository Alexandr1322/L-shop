<?php

namespace App\Http\Controllers\Admin\Control;

use App\Http\Requests\Admin\SaveApiRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class ApiController
 *
 * @author D3lph1 <d3lph1.contact@gmail.com>
 *
 * @package App\Http\Controllers\Admin\Control
 */
class ApiController extends Controller
{
    /**
     * Render API settings page.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render(Request $request)
    {
        $data = [
            'currentServer' => $request->get('currentServer'),
            'enabled' => (bool)s_get('api.enabled'),
            'key' => s_get('api.key'),
            'separator' => s_get('api.separator'),
            'salt' => (bool)s_get('api.salt'),
            'algos' => config('l-shop.api.available_algos'),
            'algo' => s_get('api.algo'),
            'signinEnabeld' => (bool)s_get('api.signin.enabled'),
            'signupEnabled' => (bool)s_get('api.signup.enabled'),
            'signinRemember' => (bool)s_get('api.signin.remember_user'),
            'sashokAuthEnabled' => (bool)s_get('api.launcher.sashok.auth.enabled'),
            'sashokAuthFormat' => s_get('api.launcher.sashok.auth.format'),
            'sashokAuthErrorMessage' => s_get('api.launcher.sashok.auth.error_message'),
            'sashokAuthWhiteList' => implode(', ', json_decode(s_get('api.launcher.sashok.auth.ips_white_list')))
        ];

        return view('admin.control.api', $data);
    }

    /**
     * Save API settings.
     *
     * @param SaveApiRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(SaveApiRequest $request)
    {
        $whiteList = $this->convertSashokLauncerAuthWhiteList($request->get('sashok_launcher_auth_white_list'));

        s_set([
            'api.enabled' => (bool)$request->get('enabled'),
            'api.key' => $request->get('key'),
            'api.algo' => $request->get('algo'),
            'api.separator' => $request->get('separator'),
            'api.salt' => (bool)$request->get('salt'),
            'api.signin.enabled' => (bool)$request->get('signin_enabled'),
            'api.signup.enabled' => (bool)$request->get('signup_enabled'),
            'api.signin.remember_user' => (bool)$request->get('signin_remember'),
            'api.launcher.sashok.auth.enabled' => (bool)$request->get('sashok_launcher_auth_enabled'),
            'api.launcher.sashok.auth.format' => $request->get('sashok_launcher_auth_format'),
            'api.launcher.sashok.auth.error_message' => $request->get('sashok_launcher_auth_error_message'),
            'api.launcher.sashok.auth.ips_white_list' => $whiteList,
        ]);
        s_save();

        \Message::success('Изменения успешно сохранены!');

        return back();
    }

    /**
     * Convert given whitelist string in needed format.
     *
     * @param $whiteList
     *
     * @return string
     */
    private function convertSashokLauncerAuthWhiteList($whiteList)
    {
        $whiteList = preg_replace('/(\s+)/ui', '', $whiteList);
        $whiteList = explode(',', $whiteList);

        if (count($whiteList) === 0 or $whiteList[0] === '') {
            return '[]';
        }else {
            return json_encode($whiteList);
        }
    }
}
