<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\QuoteRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuoteRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:QuoteRequest');
    }

    public function view(AuthUser $authUser, QuoteRequest $quoteRequest): bool
    {
        return $authUser->can('View:QuoteRequest');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:QuoteRequest');
    }

    public function update(AuthUser $authUser, QuoteRequest $quoteRequest): bool
    {
        return $authUser->can('Update:QuoteRequest');
    }

    public function delete(AuthUser $authUser, QuoteRequest $quoteRequest): bool
    {
        return $authUser->can('Delete:QuoteRequest');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:QuoteRequest');
    }

    public function restore(AuthUser $authUser, QuoteRequest $quoteRequest): bool
    {
        return $authUser->can('Restore:QuoteRequest');
    }

    public function forceDelete(AuthUser $authUser, QuoteRequest $quoteRequest): bool
    {
        return $authUser->can('ForceDelete:QuoteRequest');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:QuoteRequest');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:QuoteRequest');
    }

    public function replicate(AuthUser $authUser, QuoteRequest $quoteRequest): bool
    {
        return $authUser->can('Replicate:QuoteRequest');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:QuoteRequest');
    }

}