<?php

namespace App\Modules\Request\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Request\Models\Request as RepairRequest;
use App\Modules\Request\Services\RequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class MasterController extends Controller
{
    public function __construct(
        private RequestService $requestService
    ) {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->isMaster()) {
                abort(403, 'Доступ запрещен. Требуется роль мастера.');
            }
            return $next($request);
        });
    }

    public function index(): View
    {
        $master = auth()->user();
        $requests = $this->requestService->getByAssignedTo($master);

        return view('master.index', compact('requests'));
    }

    public function takeInProgress(RepairRequest $request): RedirectResponse
    {
        $master = auth()->user();

        try {
            $this->requestService->takeInProgress($request, $master);
            return back()->with('success', "Заявка #{$request->id} взята в работу");
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Произошла ошибка при взятии заявки в работу']);
        }
    }

    public function complete(RepairRequest $request): RedirectResponse
    {
        $master = auth()->user();

        try {
            $this->requestService->complete($request, $master);
            return back()->with('success', "Заявка #{$request->id} завершена");
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Произошла ошибка при завершении заявки']);
        }
    }
}
