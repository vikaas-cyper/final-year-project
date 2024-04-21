<?php

namespace App\Policies;

use App\Models\MarketingRepresentative;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MarketingRepresentativePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
        if ($user->can('viewAny marketing_representatives')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MarketingRepresentative  $marketingRepresentative
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, MarketingRepresentative $marketingRepresentative)
    {
        //
        if ($user->can('view marketing_representatives')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
        if ($user->can('create marketing_representatives')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MarketingRepresentative  $marketingRepresentative
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, MarketingRepresentative $marketingRepresentative)
    {
        //
        if ($user->can('edit marketing_representatives')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MarketingRepresentative  $marketingRepresentative
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, MarketingRepresentative $marketingRepresentative)
    {
        //
        if ($user->can('delete marketing_representatives')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MarketingRepresentative  $marketingRepresentative
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, MarketingRepresentative $marketingRepresentative)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MarketingRepresentative  $marketingRepresentative
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, MarketingRepresentative $marketingRepresentative)
    {
        //
    }
}
