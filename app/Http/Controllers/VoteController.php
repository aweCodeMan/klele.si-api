<?php

namespace App\Http\Controllers;

use App\Aggregates\VoteAggregate;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'uuid' => ['required'],
            'type' => ['required', 'in:post,comment'],
            'vote' => ['present', 'in:-1,0,1'],
        ];

        $this->validate($request, $rules);

        $type = $request->get('type');

        $rules = match ($type) {
            'comment' => ['uuid' => ['exists:comments,uuid']],
            'post' => ['uuid' => ['exists:posts,uuid']],
        };

        $this->validate($request, $rules);

        VoteAggregate::retrieve($request->get('uuid'))
            ->store($request->user()->uuid, $request->get('vote'), $request->get('type'))
            ->persist();

        return response()->json();
    }
}
