<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Models\User\AdminUserService;
use App\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->service = new AdminUserService();
    }

    /**
     * @param User $user
     * @param Request $request
     */
    public function setIsAdmin(User $user, Request $request)
    {
        $request->validate([
            'is_admin' => 'required|boolean',
        ]);

        return $this->service->setIsAdmin($user, $request->input('is_admin'));
    }

}
