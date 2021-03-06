<?php

namespace App\Http\Controllers;

use App\Services\Character\Skin;
use Illuminate\Http\Request;

class SkinController extends Controller
{
    /**
     * @param string $player
     *
     * @return Skin
     */
    protected function skin($player)
    {
        return app(Skin::class, ['player' => $player]);
    }

    /**
     * Check user with given username on exists
     *
     * @param string $username
     *
     * @return bool
     */
    protected function checkUser($username)
    {
        if (\Sentinel::getUserRepository()->findByCredentials(['username' => $username])) {
            return true;
        }

        abort(404);
    }

    public function skinFront(Request $request)
    {
        $username = $request->route('user');
        $skin = $this->skin($username);
        $this->checkUser($username);
        $ready = $skin->getFrontSkin();
        $ready->resizeImage(256);
        $img = $ready->saveImageAsTmpAndGet();

        return response($img->encode('png'), 200, [
            'Content-Type' => 'image/png'
        ]);
    }

    public function skinBack(Request $request)
    {
        $username = $request->route('user');
        $skin = $this->skin($username);
        $this->checkUser($username);
        $ready = $skin->getBackSkin();
        $ready->resizeImage(256);
        $img = $ready->saveImageAsTmpAndGet();

        return response($img->encode('png'), 200, [
            'Content-Type' => 'image/png'
        ]);
    }

    public function headFront(Request $request)
    {
        $username = $request->route('user');
        $skin = $this->skin($username);
        $this->checkUser($username);
        $ready = $skin->getFrontHead();
        $ready->resizeImage(256);
        $img = $ready->saveImageAsTmpAndGet();

        return response($img->encode('png'), 200, [
            'Content-Type' => 'image/png'
        ]);
    }

    public function headBack(Request $request)
    {
        $username = $request->route('user');
        $skin = $this->skin($username);
        $this->checkUser($username);
        $ready = $skin->getBackHead();
        $ready->resizeImage(256);
        $img = $ready->saveImageAsTmpAndGet();

        return response($img->encode('png'), 200, [
            'Content-Type' => 'image/png'
        ]);
    }

    public function cloakFront(Request $request)
    {
        $username = $request->route('user');
        $skin = $this->skin($username);
        $this->checkUser($username);
        $ready = $skin->getFrontCloak();
        if (!$ready) {
            abort(404);
        }

        $ready->resizeImage(256);
        $img = $ready->saveImageAsTmpAndGet();

        return response($img->encode('png'), 200, [
            'Content-Type' => 'image/png'
        ]);
    }

    public function cloakBack(Request $request)
    {
        $username = $request->route('user');
        $skin = $this->skin($username);
        $this->checkUser($username);
        $ready = $skin->getBackCloak();
        if (!$ready) {
            abort(404);
        }

        $ready->resizeImage(256);
        $img = $ready->saveImageAsTmpAndGet();

        return response($img->encode('png'), 200, [
            'Content-Type' => 'image/png'
        ]);
    }
}
