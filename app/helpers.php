<?php

/**
 *  File with declaration helpers-functions
 *
 * @author D3lph1 <d3lph1.contact@gmail.com>
 */

if (!function_exists('s_get')) {
    /**
     * Get the setting value
     *
     * @param      $key
     * @param null $default
     * @param bool $lower
     *
     * @return string
     */
    function s_get($key, $default = null, $lower = false)
    {
        if ($lower) {
            return mb_strtolower(\Setting::get($key, $default));
        }

        return \Setting::get($key, $default);
    }
}

if (!function_exists('s_set')) {
    /**
     * Set the option value
     *
     * @param string|array $option Option name or array `option` => `value`
     * @param mixed        $value  Option value
     */
    function s_set($option, $value = null)
    {
        \Setting::set($option, $value);
    }
}

if (!function_exists('s_save')) {
    /**
     * Save the settings
     */
    function s_save()
    {
        \Setting::save();
    }
}

if (!function_exists('is_auth')) {
    /**
     * Checking whether a user is logged in at the moment
     *
     * @return bool|\Cartalyst\Sentinel\Users\UserInterface
     */
    function is_auth()
    {
        return \Sentinel::check();
    }
}

if (!function_exists('is_admin')) {
    /**
     * Checks user for administrator rights
     *
     * @return bool
     */
    function is_admin()
    {
        if (is_auth()) {
            $user = \Sentinel::getUser();

            return $user->hasAccess(['user.admin']);
        }

        return false;
    }
}

if (!function_exists('access_mode_auth')) {
    /**
     * Checks shopping mode
     *
     * @return bool
     */
    function access_mode_auth()
    {
        return s_get('shop.access_mode', 'auth', true) === 'auth' ? true : false;
    }
}

if (!function_exists('access_mode_guest')) {
    /**
     * Checks shopping mode
     *
     * @return bool
     */
    function access_mode_guest()
    {
        return s_get('shop.access_mode', 'auth', true) === 'guest' ? true : false;
    }
}

if (!function_exists('access_mode_any')) {
    /**
     * Checks shopping mode
     *
     * @return bool
     */
    function access_mode_any()
    {
        return s_get('shop.access_mode', 'auth', true) === 'any' ? true : false;
    }
}

if (!function_exists('is_enable')) {
    /**
     * Checks for the specific rights the shop
     *
     * @return bool
     */
    function is_enable($action)
    {
        return (bool)s_get($action);
    }
}

if (!function_exists('img_path')) {
    /**
     * Return path to images folder
     *
     * @return bool
     */
    function img_path($url)
    {
        return public_path("img/$url");
    }
}

if (!function_exists('json_response')) {
    /**
     * Return filled json response object
     *
     * @param       $status
     * @param array $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function json_response($status, $data = [])
    {
        $response = [
            'status' => $status
        ];

        if (count($data) > 0) {
            return response()->json(array_merge($response, $data));
        }

        return response()->json($response);
    }
}

if (!function_exists('refill_user_balance')) {
    /**
     * Adds an given sum to the user's account
     *
     * @param int  $sum
     * @param null $userId
     */
    function refill_user_balance($sum, $userId = null)
    {
        if (is_null($userId)) {
            if (is_auth()) {
                $balance = \Sentinel::getUser()->getBalance();
                \Sentinel::update(\Sentinel::getUser(), [
                    'balance' => $balance + $sum
                ]);
            } else {
                throw new LogicException('User not auth');
            }
        } else {
            $user = \Sentinel::getUserRepository()->findById($userId);
            $balance = $user->getBalance();
            \Sentinel::update($user, [
                'balance' => $balance + $sum
            ]);
        }
    }
}

if (!function_exists('humanize_month')) {
    /**
     * @param string $month
     *
     * @return string
     */
    function humanize_month($month)
    {
        $list = [
            'January' => 'Январь',
            'February' => 'Февраль',
            'March' => 'Март',
            'April' => 'Апрель',
            'May' => 'Май',
            'June' => 'Июнь',
            'July' => 'Июль',
            'August' => 'Август',
            'September' => 'Сентябрь',
            'October' => 'Октябрь',
            'Novemver' => 'Ноябрь',
            'December' => 'Декабрь'
        ];

        return strtr($month, $list);
    }
}

if (!function_exists('short_string')) {
    /**
     * @param string $string
     * @param int    $length
     *
     * @return string
     */
    function short_string($string, $length)
    {
        if (mb_strlen($string) < $length) {
            return $string;
        }

        return mb_substr($string, 0, $length) . '...';
    }
}

if (!function_exists('dt')) {
    /**
     * dt - default time.
     * Formatted time to default format.
     *
     * @param string|\Carbon\Carbon $date
     *
     * @return string
     */
    function dt( $date)
    {
        $format = 'd-m-Y H:i:s';

        if ($date instanceof \Carbon\Carbon) {
            return $date->format($format);
        }

        return (new \Carbon\Carbon($date))->format($format);
    }
}

if (!function_exists('build_ban_message')) {
    /**
     * @param null|\Carbon\Carbon $until
     * @param null|string         $reason
     *
     * @return string
     */
    function build_ban_message($until = null, $reason = null)
    {
        if (is_null($until)) {
            $until = "навсегда";
        } else {
            $until = dt($until);
            $until = "до $until";
        }

        if ($reason) {
            $reason = " по причине: \"$reason\"";
        }
        $result = "Аккаунт этого пользователя заблокирован {$until}{$reason}.";

        return $result;
    }
}

if (!function_exists('username')) {
    /**
     * @return string
     */
    function username()
    {
        return \Sentinel::getUser()->getUserLogin();
    }
}

if (!function_exists('cloak_exists')) {
    /**
     * @param string $username
     *
     * @return bool
     */
    function cloak_exists($username)
    {
        return file_exists(config('l-shop.profile.cloaks.path') . '/' . $username . '.png');
    }
}

if (!function_exists('get_server_by_id')) {
    /**
     * @param $servers
     * @param $id
     *
     * @return mixed
     */
    function get_server_by_id($servers, $id)
    {
        foreach ($servers as $server) {
            if ($server->id === $id) {
                return $server;
            }
        }
    }
}
