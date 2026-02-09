<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Tag;

use App\Http\Requests\FollowTagRequest;

class TagFollowController extends Controller
{
        public function follow(FollowTagRequest $request, Tag $tag)
    {
        Auth::user()->followedTags()->syncWithoutDetaching([$tag->id]);
        return back()->with('success', 'The tag was successfully followed!');
    }

    public function unfollow(Tag $tag)
    {
        Auth::user()->followedTags()->detach($tag->id);
        return back()->with('success', 'The tag has been unfollowed');
    }
}
