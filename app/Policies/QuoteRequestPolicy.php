<?php

namespace App\Policies;

use App\Models\QuoteRequest;
use App\Models\User;

class QuoteRequestPolicy
{
    public function viewAny(User $u): bool
    {
        return $u->can('view_quote_request');
    }

    public function view(User $u, QuoteRequest $r): bool
    {
        return $u->can('view_quote_request');
    }

    public function take(User $u, QuoteRequest $r): bool
    {
        return $u->can('take_quote_request');
    }

    public function accept(User $u, QuoteRequest $r): bool
    {
        return $u->can('accept_quote_request');
    }

    public function reject(User $u, QuoteRequest $r): bool
    {
        return $u->can('reject_quote_request');
    }
}