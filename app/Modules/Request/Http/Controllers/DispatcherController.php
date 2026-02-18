<?php

namespace App\Modules\Request\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Request\Models\Request as RepairRequest;
use App\Modules\Request\Services\RequestService;
use App\Modules\User\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DispatcherController extends Controller
{
    public function __construct(
        private RequestService $requestService
    ) {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->isDispatcher()) {
                abort(403, 'Доступ запрещен. Требуется роль диспетчера.');
            }
            return $next($request);
        });
    }

    public function index(Request $httpRequest): View
    {
        $filters = $httpRequest->only(['status']);
        $requests = $this->requestService->paginate(15, $filters);
        $masters = User::where('role', User::ROLE_MASTER)->get();

        return view('dispatcher.index', compact('requests', 'masters'));
    }

    public function assign(Request $httpRequest, RepairRequest $request): RedirectResponse
    {
        $validated = $httpRequest->validate([
            'master_id' => 'required|exists:users,id',
        ]);

        $master = User::findOrFail($validated['master_id']);

        if (!$master->isMaster()) {
            return back()->withErrors(['master_id' => 'Выбранный пользователь не является мастером']);
        }

        try {
            $this->requestService->assign($request, $master);
            return back()->with('success', "Заявка #{$request->id} назначена мастеру {$master->name}");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function cancel(RepairRequest $request): RedirectResponse
    {
        try {
            if (!$request->canBeCanceled()) {
                return back()->withErrors(['error' => 'Заявка не может быть отменена в текущем статусе']);
            }

            $this->requestService->cancel($request);
            return back()->with('success', "Заявка #{$request->id} отменена");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
