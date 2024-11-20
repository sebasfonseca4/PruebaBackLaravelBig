<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function update(User $user, Project $project)
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Project $project)
    {
        return $user->role === 'admin';
    }

    public function create(User $user)
    {
        return $user->role === 'admin';
    }

    public function view(User $user, Project $project)
    {
        return $user->role === 'admin' || $project->users->contains($user);
    }

    public function viewAny(User $user)
    {
        return $user->role === 'admin';
    }
}
