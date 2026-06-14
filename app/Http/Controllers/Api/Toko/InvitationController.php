<?php

namespace App\Http\Controllers\Api\Toko;

use App\Http\Controllers\Controller;
use App\Models\Auth\Notification\NotificationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Auth\UserModel;
use App\Models\Toko\TokoModel;
use App\Models\Toko\JabatanModel;
use App\Models\Toko\TokoInvitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Toko\TokoUserModel;
use App\Services\Toko\TokoService;
use App\Services\Message\NotificationService;
use App\Services\ValidatorService;

class InvitationController extends Controller
{
    public $tokoService;
    public $validatorService;
    public $notificationService;
    public function __construct(TokoService $tokoService, NotificationService $notificationService, ValidatorService $validatorService)
    {
        $this->tokoService = $tokoService;
        $this->validatorService = $validatorService;
        $this->notificationService = $notificationService;
    }

    public function data()
    {
        $user = Auth::user();
        $userToko = $this->tokoService->getTokoByUser($user);
        if (!$userToko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.'
            ], 403);
        }
        $invitation = TokoInvitation::where('toko_id', $userToko->id)->get();
        $accepted = $invitation->where('status', 'accepted');
        $rejected = $invitation->where('status', 'rejected');
        $pending = $invitation->where('status', 'pending');
        return response()->json([
            'success' => true,
            'data' => [
                'accepted' => $accepted,
                'rejected' => $rejected,
                'pending' => $pending,
            ],
        ], 200);
    }

    public function searchUser(Request $request)
    {
        $search = $request->input('query');

        $users = UserModel::role('guest') // hanya role 'guest' dari Spatie
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            })
            ->whereDoesntHave('tokos')       // Tidak menjadi anggota toko
            ->whereDoesntHave('ownedTokos')  // Tidak memiliki toko
            ->limit(5)
            ->get();
        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function searchJabatan(Request $request)
    {
        $search = $request->input('query');
        $user = Auth::user();

        $userToko = $this->tokoService->getTokoByUser($user);

        if (!$userToko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.'
            ], 403);
        }

        // Ambil jabatan_id user di toko tersebut
        $jabatanIdUser = TokoUserModel::where('user_id', $user->id)
            ->where('toko_id', $userToko->id)
            ->value('jabatan_id');

        // Ambil level jabatan user saat ini
        $userJabatanLevel = JabatanModel::where('id', $jabatanIdUser)->value('level');

        // Ambil semua jabatan yang level-nya <= level user dan cocok dengan pencarian
        $jabatans = JabatanModel::where('name', 'like', "%{$search}%")
            ->when(!is_null($userJabatanLevel), function ($query) use ($userJabatanLevel) {
                $query->where('level', '<', $userJabatanLevel);
            })
            ->limit(5)
            ->get();

        if ($jabatans->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Jabatan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $jabatans,
        ]);
    }


    public function sendInvitation(Request $request)
    {
        $validationRules = [
            'invite_user' => 'required|exists:users,username',
            'jabatan_id' => 'required|exists:jabatan,id',
        ];

        $valid = $this->validatorService->validateRequest($request, $validationRules);
        if ($valid !== true) {
            return $valid;
        }

        $inviteUser = UserModel::where('username', $request->invite_user)->first();
        if (!$inviteUser) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }
        $inviteUserToko = $this->tokoService->getTokoByUser($inviteUser);
        if ($inviteUserToko) {
            return response()->json([
                'success' => false,
                'message' => 'User sudah memiliki toko.'
            ], 403);
        }

        $user = Auth::user();
        $userToko = $this->tokoService->getTokoByUser($user);
        if (!$userToko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.'
            ], 403);
        }

        $tokoKaryawan = TokoUserModel::whereIn('toko_id', $user->tokos->pluck('id'))
            ->where('status', 'active')
            ->get();
        if ($tokoKaryawan->isNotEmpty()) {
            $jabatan = $tokoKaryawan->first()->jabatan;
        }
        if (!$jabatan->can_invite_users) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengundang user'
            ], 403);
        }
        $toJabatan = JabatanModel::find($request->jabatan_id);
        if ($jabatan->level < $toJabatan->level) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengundang user dengan level jabatan yang lebih tinggi atau sama'
            ], 403);
        }
        $existingInvitation = DB::table('toko_invitations')
            ->where('toko_id', $userToko->id)
            ->where('invited_id', $inviteUser->id)
            ->where('status', 'pending')
            ->first();

        if ($existingInvitation) {
            return response()->json([
                'success' => false,
                'message' => 'Undangan untuk user ini sudah ada dan masih dalam status pending'
            ], 403);
        }
        try {
            $invitation = TokoInvitation::create([
                'toko_id' => $userToko->id,
                'inviter_id' => $user->id,
                'invited_id' => $inviteUser->id,
                'jabatan_id' => $request->jabatan_id,
                'message' => "Saya mengundang Anda untuk bergabung dengan toko " . $userToko->name . "",
                'status' => 'pending',
            ]);

            try {
                $notification = $this->notificationService->sendToUser($inviteUser, 'toko_invitation', [
                    'message' => "Saya mengundang Anda untuk bergabung dengan toko " . $userToko->name . "",
                    'invitation_id' => $invitation->id,
                ], $user, '/toko/invitation/' . $inviteUser->id);

                if (!$notification) {
                    throw new \Exception('Gagal membuat notification');
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Undangan berhasil dikirim',
                ], 200);
            } catch (\Exception $e) {
                // Jika gagal membuat notification, hapus invitation yang sudah dibuat
                $invitation->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat notification: ' . $e->getMessage(),
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function handleInvitation($id, Request $request)
    {
        $validationRules = [
            'status' => 'required|in:accepted,rejected',
        ];

        $valid = $this->validatorService->validateRequest($request, $validationRules);
        if ($valid !== true) {
            return $valid;
        }

        $user = Auth::user();
        $invitation = TokoInvitation::where('id', $id)
            ->where('invited_id', $user->id)
            ->where('status', 'pending')
            ->first();
        if (!$invitation) {
            return response()->json([
                'success' => false,
                'message' => 'Undangan tidak ditemukan',
            ], 404);
        }

        if ($request->status == 'accepted') {
            return $this->handleAcceptInvitation($invitation);
        } else {
            return $this->handleRejectInvitation($invitation);
        }
    }

    private function findNotification($invitation)
    {
        return NotificationModel::where('type', 'toko_invitation')
            ->where('notifiable_id', $invitation->invited_id)
            ->whereJsonContains('data->invitation_id', $invitation->id)
            ->first();
    }

    private function handleAcceptInvitation($invitation)
    {
        try {
            DB::beginTransaction();

            $invitation->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            $notification = $this->findNotification($invitation);
            if ($notification) {
                $this->notificationService->refreshNotification(
                    $notification,
                    'toko_invitation',
                    $invitation->invited,
                    ['message' => 'Anda telah bergabung dengan toko ' . $invitation->toko->name, 'invitation_id' => $invitation->id],
                    null,
                    true,
                    true,
                    false,
                    '/toko/detail/' . $invitation->toko->slug
                );
            }

            $this->tokoService->assignUserToToko($invitation->invited, $invitation->toko, $invitation->jabatan);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Undangan berhasil diterima',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function handleRejectInvitation($invitation)
    {
        try {
            $invitation->update([
                'status' => 'rejected',
                'responded_at' => now(),
            ]);
            $notification = $this->findNotification($invitation);
            if ($notification) {
                $notification->delete();
                $invitation->delete();
            }
            return response()->json([
                'success' => true,
                'message' => 'Undangan berhasil ditolak',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function cancelInvitation($id)
    {
        $user = Auth::user();

        $invitation = DB::table('toko_invitations')
            ->where('id', $id)
            ->where('inviter_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$invitation) {
            return response()->json([
                'success' => false,
                'message' => 'Undangan tidak ditemukan atau sudah tidak aktif',
            ], 404);
        }

        // Delete invitation
        DB::table('toko_invitations')
            ->where('id', $id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Undangan telah dibatalkan',
        ], 200);
    }

    public function fireKaryawan(Request $request)
    {
        $validationRules = [
            'fire_user' => 'required|exists:users,username',
        ];

        $valid = $this->validatorService->validateRequest($request, $validationRules);
        if ($valid !== true) {
            return $valid;
        }
        $user = Auth::user();
        if ($user->username == $request->fire_user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat mengeluarkan diri sendiri.'
            ], 403);
        }
        $userToko = $this->tokoService->getTokoByUser($user);
        if (!$userToko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.'
            ], 403);
        }
        $fireUser = UserModel::where('username', $request->fire_user)->first();
        if (!$fireUser) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }
        $fireUserToko = $this->tokoService->getTokoByUser($fireUser);
        if (!$fireUserToko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 403);
        }
        if ($fireUserToko->id != $userToko->id) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak didalam toko ini.'
            ], 403);
        }
        $this->tokoService->fireUserFromToko($fireUser, $userToko);
        return response()->json([
            'success' => true,
            'message' => 'User berhasil dikeluarkan dari toko',
        ], 200);
    }

    public function promoteKaryawan(Request $request)
    {
        $validationRules = [
            'promote_user' => 'required|exists:users,username',
            'jabatan_id' => 'required|exists:jabatan,id',
        ];

        $valid = $this->validatorService->validateRequest($request, $validationRules);
        if ($valid !== true) {
            return $valid;
        }
        $user = Auth::user();
        if ($user->username == $request->promote_user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat mempromosikan diri sendiri.'
            ], 403);
        }
        $userToko = $this->tokoService->getTokoByUser($user);
        if (!$userToko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.'
            ], 403);
        }
        $promoteUser = UserModel::where('username', $request->promote_user)->first();
        if (!$promoteUser) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }
        $promoteUserToko = $this->tokoService->getTokoByUser($promoteUser);
        if (!$promoteUserToko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 403);
        }
        if ($promoteUserToko->id != $userToko->id) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak didalam toko ini.'
            ], 403);
        }
        $newJabatan = JabatanModel::find($request->jabatan_id);
        if (!$newJabatan) {
            return response()->json([
                'success' => false,
                'message' => 'Jabatan tidak ditemukan.'
            ], 404);
        }
        // Find the specific store relationship for this user
        $userTokoRelationship = $promoteUser->tokos()->where('toko_id', $userToko->id)->first();
        $success = $this->tokoService->changeJabatan($promoteUser, $userToko, $newJabatan);
        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mempromosikan user.'
            ], 500);
        }


        return response()->json([
            'success' => true,
            'message' => "Jabatan berhasil diperbarui dari jabatan {$userTokoRelationship->pivot->jabatan->name} ke jabatan {$newJabatan->name}",
        ], 200);
    }
}
