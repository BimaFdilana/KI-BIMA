<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TokoAccessMiddleware
{
    /**
     * Handle an incoming request and check store permissions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $checkType
     * @param  mixed   $requirement
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $checkType = 'member', $requirement = null)
    {
        if (!Auth::check()) {
            return $this->unauthorizedResponse();
        }

        $user = Auth::user();
        $tokoId = $this->getTokoId($user);

        switch ($checkType) {
            case 'member':
                return $this->checkMembership($user, $next, $request);

            case 'store':
                return $this->checkSpecificStore($user, $tokoId, $next, $request);

            case 'level':
                return $this->checkLevel($user, $tokoId, (int)$requirement, $next, $request);

            case 'jabatan':
                return $this->checkJabatan($user, $tokoId, $requirement, $next, $request);

            case 'owner':
                return $this->checkOwner($user, $tokoId, $next, $request);

            case 'manager':
                return $this->checkManager($user, $tokoId, $next, $request);

            case 'supervisor':
                return $this->checkSupervisor($user, $tokoId, $next, $request);

            case 'kasir':
                return $this->checkKasir($user, $tokoId, $next, $request);

            case 'can_invite':
                return $this->checkCanInvite($user, $tokoId, $next, $request);

            case 'can_manage_inventory':
                return $this->checkCanManageInventory($user, $tokoId, $next, $request);

            case 'can_view_reports':
                return $this->checkCanViewReports($user, $tokoId, $next, $request);

            case 'can_manage_orders':
                return $this->checkCanManageOrders($user, $tokoId, $next, $request);

            default:
                return $this->forbiddenResponse('Invalid check type');
        }
    }

    /**
     * Get toko ID from user's current active store or first store
     */
    private function getTokoId($user)
    {
        // Option 1: Get from user's current active store (if you have this field)
        if (property_exists($user, 'current_toko_id') && $user->current_toko_id) {
            return $user->current_toko_id;
        }

        // Option 2: Get from user's first store (primary store)
        $userToko = DB::table('toko_user')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'asc') // or 'desc' for latest
            ->first();

        return $userToko ? $userToko->toko_id : null;

        // Option 3: Get from user's owned store (if user is owner)
        // $ownedToko = DB::table('tokos')
        //     ->where('owner_id', $user->id)
        //     ->first();
        // return $ownedToko ? $ownedToko->id : null;

        // Option 4: Get from session
        // return session('current_toko_id');

        // Option 5: Get from user profile/settings table
        // $userSettings = DB::table('user_settings')
        //     ->where('user_id', $user->id)
        //     ->first();
        // return $userSettings ? $userSettings->default_toko_id : null;
    }

    /**
     * Get user's jabatan in specific toko
     */
    private function getUserJabatan($userId, $tokoId)
    {
        return DB::table('toko_user')
            ->join('jabatan', 'toko_user.jabatan_id', '=', 'jabatan.id')
            ->where('toko_user.user_id', $userId)
            ->where('toko_user.toko_id', $tokoId)
            ->where('toko_user.status', 'active')
            ->select('jabatan.*')
            ->first();
    }

    /**
     * Check if user is member of any store
     */
    private function checkMembership($user, $next, $request)
    {
        $isMember = DB::table('toko_user')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();

        if (!$isMember) {
            return $this->forbiddenResponse('You are not a member of any store');
        }

        return $next($request);
    }

    /**
     * Check if user belongs to specific store
     */
    private function checkSpecificStore($user, $tokoId, $next, $request)
    {
        if (!$tokoId) {
            return $this->badRequestResponse('No active store found for user');
        }

        $isMember = DB::table('toko_user')
            ->where('user_id', $user->id)
            ->where('toko_id', $tokoId)
            ->where('status', 'active')
            ->exists();

        if (!$isMember) {
            return $this->forbiddenResponse('You do not belong to this store');
        }

        return $next($request);
    }

    /**
     * Check user level in store
     */
    private function checkLevel($user, $tokoId, $minLevel, $next, $request)
    {
        if (!$tokoId) {
            return $this->badRequestResponse('No active store found for user');
        }

        $jabatan = $this->getUserJabatan($user->id, $tokoId);

        if (!$jabatan) {
            return $this->forbiddenResponse('You do not belong to this store');
        }

        if ($jabatan->level < $minLevel) {
            return $this->forbiddenResponse("Minimum level {$minLevel} required", [
                'current_level' => $jabatan->level,
                'required_level' => $minLevel,
                'current_jabatan' => $jabatan->name
            ]);
        }

        return $next($request);
    }

    /**
     * Check specific jabatan
     */
    private function checkJabatan($user, $tokoId, $requiredJabatan, $next, $request)
    {
        if (!$tokoId) {
            return $this->badRequestResponse('No active store found for user');
        }

        $jabatan = $this->getUserJabatan($user->id, $tokoId);

        if (!$jabatan || $jabatan->slug !== $requiredJabatan) {
            return $this->forbiddenResponse("Position '{$requiredJabatan}' required", [
                'current_jabatan' => $jabatan ? $jabatan->slug : null
            ]);
        }

        return $next($request);
    }

    /**
     * Check if user is owner (level 5)
     */
    private function checkOwner($user, $tokoId, $next, $request)
    {
        return $this->checkJabatan($user, $tokoId, 'pemilik-toko', $next, $request);
    }

    /**
     * Check if user is manager or above (level 4+)
     */
    private function checkManager($user, $tokoId, $next, $request)
    {
        return $this->checkLevel($user, $tokoId, 4, $next, $request);
    }

    /**
     * Check if user is supervisor or above (level 3+)
     */
    private function checkSupervisor($user, $tokoId, $next, $request)
    {
        return $this->checkLevel($user, $tokoId, 3, $next, $request);
    }

    /**
     * Check if user is kasir or above (level 2+)
     */
    private function checkKasir($user, $tokoId, $next, $request)
    {
        return $this->checkLevel($user, $tokoId, 2, $next, $request);
    }

    /**
     * Check if user can invite others
     */
    private function checkCanInvite($user, $tokoId, $next, $request)
    {
        if (!$tokoId) {
            return $this->badRequestResponse('No active store found for user');
        }

        $jabatan = $this->getUserJabatan($user->id, $tokoId);

        if (!$jabatan || !$jabatan->can_invite_users) {
            return $this->forbiddenResponse('You do not have permission to invite users');
        }

        return $next($request);
    }

    /**
     * Check if user can manage inventory
     */
    private function checkCanManageInventory($user, $tokoId, $next, $request)
    {
        if (!$tokoId) {
            return $this->badRequestResponse('No active store found for user');
        }

        $jabatan = $this->getUserJabatan($user->id, $tokoId);

        if (!$jabatan || !$jabatan->can_manage_inventory) {
            return $this->forbiddenResponse('You do not have permission to manage inventory');
        }

        return $next($request);
    }

    /**
     * Check if user can view reports
     */
    private function checkCanViewReports($user, $tokoId, $next, $request)
    {
        if (!$tokoId) {
            return $this->badRequestResponse('No active store found for user');
        }

        $jabatan = $this->getUserJabatan($user->id, $tokoId);

        if (!$jabatan || !$jabatan->can_view_reports) {
            return $this->forbiddenResponse('You do not have permission to view reports');
        }

        return $next($request);
    }

    /**
     * Check if user can manage orders
     */
    private function checkCanManageOrders($user, $tokoId, $next, $request)
    {
        if (!$tokoId) {
            return $this->badRequestResponse('No active store found for user');
        }

        $jabatan = $this->getUserJabatan($user->id, $tokoId);

        if (!$jabatan || !$jabatan->can_manage_orders) {
            return $this->forbiddenResponse('You do not have permission to manage orders');
        }

        return $next($request);
    }

    /**
     * Return unauthorized response
     */
    private function unauthorizedResponse()
    {
        return response()->json([
            'success' => false,
            'message' => 'Anda tidak terotentikasi',
            'error_code' => 'UNAUTHORIZED'
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Return forbidden response
     */
    private function forbiddenResponse($message, $data = [])
    {
        return response()->json(array_merge([
            'success' => false,
            'message' => $message,
            'error_code' => 'FORBIDDEN'
        ], $data), Response::HTTP_FORBIDDEN);
    }

    /**
     * Return bad request response
     */
    private function badRequestResponse($message)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error_code' => 'BAD_REQUEST'
        ], Response::HTTP_BAD_REQUEST);
    }
}
