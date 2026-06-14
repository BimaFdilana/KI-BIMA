<?php

namespace App\Services;

use App\Services\Toko\TokoService;
use Illuminate\Support\Facades\Validator;

class ValidatorService
{
    protected $tokoService;
    public function __construct(TokoService $tokoService)
    {
        $this->tokoService = $tokoService;
    }
    public function validateRequest($request, $rules)
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter input tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        return true;
    }


    public function validateStore($user)
    {
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        return $toko;
    }
}
