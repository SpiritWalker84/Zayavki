<?php

namespace App\Modules\Request\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Request\Services\RequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RequestController extends Controller
{
    public function __construct(
        private RequestService $requestService
    ) {}

    public function create(): View
    {
        return view('requests.create');
    }

    public function store(Request $httpRequest): RedirectResponse
    {
        $validated = $httpRequest->validate([
            'client_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'problem_text' => 'required|string|max:5000',
        ]);

        $request = $this->requestService->create($validated);

        return redirect()
            ->route('requests.create')
            ->with('success', "Заявка #{$request->id} успешно создана!");
    }
}
