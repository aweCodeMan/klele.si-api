<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransformMarkdownRequest;
use App\Services\MarkdownService;
use Illuminate\Http\Request;

class MarkdownController extends Controller
{
    public function transform(TransformMarkdownRequest $request)
    {
        return response()->json(['data' => MarkdownService::parse($request->get('markdown'))]);
    }
}
