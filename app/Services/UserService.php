<?php

namespace App\Services;

use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;

class UserService
{
    public function all() : object 
    {
        $user = QueryBuilder::for(User::class)
                ->allowedFilters(['firstname', 'lastname', 'email', 'status'])
                ->paginate(20);
                    
        return $user;
    }

    public function userAction(User $user, $action): object
    {
        $user->update(['status' => $action['status']]);

        return $user;
    }

    public function showUser(User $user) : object 
    {
        // $user->load('categories');

        // $user->load('brands');

        // $user->load('variationItems');

        // $user->load('media');
        
        return $user;
    }
}