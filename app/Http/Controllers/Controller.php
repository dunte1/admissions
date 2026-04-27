<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function success($message = null, $data = null)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    protected function error($message, $errors = [], $code = 422)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    protected function setLanguage(string $locale): void
    {
        if (in_array($locale, ['en', 'sw'])) {
            Session::put('locale', $locale);
            app()->setLocale($locale);
        }
    }

    protected function getSchool(): ?School
    {
        return current_school();
    }

    protected function getSchoolId(): ?int
    {
        return current_school_id();
    }

    protected function isSuperAdmin(): bool
    {
        return is_super_admin();
    }

    protected function canAccessAllSchools(): bool
    {
        return can_access_all_schools();
    }
}
