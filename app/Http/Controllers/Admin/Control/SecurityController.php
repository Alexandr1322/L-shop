<?php

namespace App\Http\Controllers\Admin\Control;

use App\Http\Requests\Admin\SaveSecurityRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class SecurityController
 *
 * @author D3lph1 <d3lph1.contact@gmail.com>
 *
 * @package App\Http\Controllers\Admin\Control
 */
class SecurityController extends Controller
{
    /**
     * Render security settings page.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render(Request $request)
    {
        $data = [
            'currentServer' => $request->get('currentServer'),
            'recaptchaPublicKey' => s_get('recaptcha.public_key'),
            'recaptchaSecretKey' => s_get('recaptcha.secret_key'),
            'enabledChangePassword' => s_get('user.enable_change_password'),
            'enabledResetPassword' => s_get('shop.enable_password_reset'),
        ];

        return view('admin.control.security', $data);
    }

    /**
     * Handle the save security settings request.
     *
     * @param SaveSecurityRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(SaveSecurityRequest $request)
    {
        s_set([
            'recaptcha.public_key' => $request->get('recaptcha_public_key'),
            'recaptcha.secret_key' => $request->get('recaptcha_secret_key'),
            'user.enable_change_password' => (bool)$request->get('enable_change_password'),
            'shop.enable_password_reset' => (bool)$request->get('enable_reset_password')
        ]);
        s_save();

        \Message::success('Изменения успешно сохранены!');

        return back();
    }
}
