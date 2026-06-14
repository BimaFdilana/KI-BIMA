<?php

namespace App\Policies;

use App\Models\Auth\UserModel;
use App\Models\Toko\TokoPesananProgress;
use Illuminate\Auth\Access\Response;

class TokoPesananProgressPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(UserModel $userModel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(UserModel $userModel, TokoPesananProgress $tokoPesananProgress): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(UserModel $userModel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(UserModel $userModel, TokoPesananProgress $tokoPesananProgress): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(UserModel $userModel, TokoPesananProgress $tokoPesananProgress): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(UserModel $userModel, TokoPesananProgress $tokoPesananProgress): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(UserModel $userModel, TokoPesananProgress $tokoPesananProgress): bool
    {
        return false;
    }
}
