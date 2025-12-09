<?php

if (!function_exists('session_cleanup')) {
    /**
     * Clean up expired sessions
     */
    function session_cleanup()
    {
        \Illuminate\Support\Facades\DB::table(config('session.table'))
            ->where('last_activity', '<', now()->subMinutes(config('session.lifetime'))->timestamp)
            ->delete();
    }
}

if (!function_exists('invalidate_all_sessions')) {
    /**
     * Invalidate all sessions for a user
     */
    function invalidate_all_sessions($userId)
    {
        \Illuminate\Support\Facades\DB::table(config('session.table'))
            ->where('user_id', $userId)
            ->delete();
    }
}

if (!function_exists('get_active_sessions')) {
    /**
     * Get all active sessions for a user
     */
    function get_active_sessions($userId)
    {
        return \Illuminate\Support\Facades\DB::table(config('session.table'))
            ->where('user_id', $userId)
            ->where('last_activity', '>=', now()->subMinutes(config('session.lifetime'))->timestamp)
            ->get();
    }
}

if (!function_exists('set_remember_me')) {
    /**
     * Set remember me cookie
     */
    function set_remember_me($user, $remember = true)
    {
        if ($remember) {
            $token = \Illuminate\Support\Str::random(60);
            $user->remember_token = hash('sha256', $token);
            $user->save();
            
            cookie()->queue('remember_me', $token, 43200); // 30 days
        }
    }
}

if (!function_exists('clear_remember_me')) {
    /**
     * Clear remember me cookie
     */
    function clear_remember_me($user)
    {
        $user->remember_token = null;
        $user->save();
        
        cookie()->queue(cookie()->forget('remember_me'));
    }
}

if (!function_exists('regenerate_session')) {
    /**
     * Regenerate session ID for security
     */
    function regenerate_session()
    {
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        request()->session()->regenerate();
    }
}
