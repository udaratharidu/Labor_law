<?php

namespace App\Http\Controllers;

use App\Services\LegalApiClient;
use Illuminate\Http\Request;

class LawController extends Controller
{
    public function index(Request $request, LegalApiClient $api)
    {
        $payload = $api->listActs(
            status: $request->query('status'),
            page: (int) $request->query('page', 1),
        );

        return view('laws.index', [
            'acts' => $payload['data'] ?? null,
            'meta' => $payload['meta'] ?? null,
            'status' => $request->query('status'),
            'loadFailed' => $payload === null,
        ]);
    }

    public function show(int $act, LegalApiClient $api)
    {
        $tree = $api->getActTree($act);

        return view('laws.show', [
            'act' => $tree,
            'loadFailed' => $tree === null,
        ]);
    }
}
