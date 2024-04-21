<?php

namespace App\Policies;

use App\Models\MarketingRepresentativeTarget;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MarketingRepresentativeTargetPolicy
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
        if ($user->can('viewAny marketing_representative_targets')) {
            return true;
        }

    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MarketingRepresentativeTarget  $marketingRepresentativeTarget
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, MarketingRepresentativeTarget $marketingRepresentativeTarget)
    {
        //
        if ($user->can('view marketing_representative_targets')) {
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
        if ($user->can('create marketing_representative_targets')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MarketingRepresentativeTarget  $marketingRepresentativeTarget
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, MarketingRepresentativeTarget $marketingRepresentativeTarget)
    {
        //
        if ($user->can('edit marketing_representative_targets')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MarketingRepresentativeTarget  $marketingRepresentativeTarget
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, MarketingRepresentativeTarget $marketingRepresentativeTarget)
    {
        //
        if ($user->can('delete marketing_representative_targets')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MarketingRepresentativeTarget  $marketingRepresentativeTarget
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, MarketingRepresentativeTarget $marketingRepresentativeTarget)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MarketingRepresentativeTarget  $marketingRepresentativeTarget
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, MarketingRepresentativeTarget $marketingRepresentativeTarget)
    {
        //
    }
}
