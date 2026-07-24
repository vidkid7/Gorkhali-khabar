<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForexRate;
use App\Models\GoldSilverPrice;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function forex(): View
    {
        $rates = ForexRate::query()->orderByDesc('date')->paginate(50);
        return view('admin.finance.forex', compact('rates'));
    }

    public function storeForex(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $request->validate([
            'currency' => ['required', 'string', 'max:10'],
            'currency_name' => ['nullable', 'string', 'max:50'],
            'buy_rate' => ['required', 'numeric'],
            'sell_rate' => ['required', 'numeric'],
            'date' => ['required', 'date'],
        ]);

        $rate = ForexRate::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'ForexRate', $rate->id, newValue: $rate->toArray());

        return back()->with('status', 'विनिमय दर थपियो।');
    }

    public function destroyForex(Request $request, AuditService $audit, ForexRate $forex): RedirectResponse
    {
        $old = $forex->toArray();
        $forex->delete();
        $audit->record($request->user(), 'DELETE', 'ForexRate', $forex->id, oldValue: $old);

        return back()->with('status', 'विनिमय दर मेटाइयो।');
    }

    public function goldSilver(): View
    {
        $prices = GoldSilverPrice::query()->orderByDesc('date')->paginate(50);
        return view('admin.finance.gold-silver', compact('prices'));
    }

    public function storeGoldSilver(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $request->validate([
            'metal' => ['required', 'in:GOLD,SILVER'],
            'purity' => ['nullable', 'string', 'max:20'],
            'unit' => ['required', 'string', 'max:20'],
            'price_per_unit' => ['required', 'numeric'],
            'date' => ['required', 'date'],
        ]);

        $price = GoldSilverPrice::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'GoldSilverPrice', $price->id, newValue: $price->toArray());

        return back()->with('status', 'मूल्य थपियो।');
    }

    public function destroyGoldSilver(Request $request, AuditService $audit, GoldSilverPrice $price): RedirectResponse
    {
        $old = $price->toArray();
        $price->delete();
        $audit->record($request->user(), 'DELETE', 'GoldSilverPrice', $price->id, oldValue: $old);

        return back()->with('status', 'मूल्य मेटाइयो।');
    }
}