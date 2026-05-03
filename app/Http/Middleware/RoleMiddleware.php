<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        // إذا لم يكن المستخدم مسجلاً
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // إذا كان دور المستخدم غير موجود ضمن الأدوار المسموح بها
        if (!in_array($user->role, $roles)) {
            return response()->json(['message' => 'Forbidden - You do not have permission'], 403);
        }

        return $next($request);
    }
}
