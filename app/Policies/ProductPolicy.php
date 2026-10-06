<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

/**
 * Aturan otorisasi produk (setara "PostPolicy" pada slide, disesuaikan dengan studi kasus e-commerce):
 *  - admin  : boleh semua
 *  - editor : boleh membuat, dan mengedit produk MILIKNYA sendiri; tidak boleh menghapus
 *  - user   : hanya boleh melihat
 */
class ProductPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Product $product): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin', 'editor');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isAdmin()
            || ($user->isEditor() && $product->user_id === $user->id);
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }
}
