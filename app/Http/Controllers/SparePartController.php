<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DcCategory;
use App\Models\SparePart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SparePartController extends Controller
{
    public function index(Request $request): View
    {
        $showCriticalOnly = $request->boolean('critical_only');

        if ($showCriticalOnly) {
            // Mengambil data langsung dari MySQL Database View 'view_critical_spareparts'
            $criticalViewData = DB::table('view_critical_spareparts')->get();
            $spareParts = SparePart::with('category')
                ->whereIn('id', $criticalViewData->pluck('id'))
                ->orderBy('stock_quantity', 'asc')
                ->paginate(15);
        } else {
            $query = SparePart::with('category');

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->input('category_id'));
            }

            if ($request->filled('search')) {
                $term = '%'.$request->input('search').'%';
                $query->where(function ($q) use ($term) {
                    $q->where('part_code', 'like', $term)
                        ->orWhere('name', 'like', $term)
                        ->orWhere('part_number', 'like', $term)
                        ->orWhere('location_bin', 'like', $term);
                });
            }

            $spareParts = $query->orderBy('name')->paginate(15)->withQueryString();
        }

        $categories = DcCategory::orderBy('name')->get();
        $totalItems = SparePart::count();
        $criticalCount = DB::table('view_critical_spareparts')->count();
        $totalValuation = SparePart::selectRaw('SUM(stock_quantity * unit_cost) as total')->value('total') ?? 0;

        return view('spareparts.index', compact(
            'spareParts',
            'categories',
            'showCriticalOnly',
            'totalItems',
            'criticalCount',
            'totalValuation'
        ));
    }

    public function create(): View
    {
        $categories = DcCategory::orderBy('name')->get();

        return view('spareparts.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:dc_categories,id'],
            'part_code' => ['required', 'string', 'max:50', 'unique:spare_parts,part_code'],
            'name' => ['required', 'string', 'max:150'],
            'part_number' => ['required', 'string', 'max:100'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_stock_alert' => ['required', 'integer', 'min:1'],
            'unit' => ['required', 'string', 'max:30'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
            'location_bin' => ['nullable', 'string', 'max:50'],
            'compatibility_notes' => ['nullable', 'string'],
            'sop_replacement_ref' => ['nullable', 'string', 'max:255'],
        ]);

        $part = SparePart::create($validated);

        return redirect()->route('spareparts.index')->with('success', "Suku cadang [{$part->part_code}] {$part->name} berhasil didaftarkan ke inventaris.");
    }

    public function edit(SparePart $sparepart): View
    {
        $categories = DcCategory::orderBy('name')->get();

        return view('spareparts.edit', compact('sparepart', 'categories'));
    }

    public function update(Request $request, SparePart $sparepart): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:dc_categories,id'],
            'part_code' => ['required', 'string', 'max:50', 'unique:spare_parts,part_code,'.$sparepart->id],
            'name' => ['required', 'string', 'max:150'],
            'part_number' => ['required', 'string', 'max:100'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_stock_alert' => ['required', 'integer', 'min:1'],
            'unit' => ['required', 'string', 'max:30'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
            'location_bin' => ['nullable', 'string', 'max:50'],
            'compatibility_notes' => ['nullable', 'string'],
            'sop_replacement_ref' => ['nullable', 'string', 'max:255'],
        ]);

        $sparepart->update($validated);

        return redirect()->route('spareparts.index')->with('success', "Data suku cadang [{$sparepart->part_code}] berhasil diperbarui.");
    }

    /**
     * Penyesuaian stok langsung (Memicu Trigger MySQL jika stok <= minimum)
     */
    public function adjustStock(Request $request, SparePart $sparepart): RedirectResponse
    {
        $validated = $request->validate([
            'quantity_change' => ['required', 'integer'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $oldStock = $sparepart->stock_quantity;
        $newStock = max(0, $oldStock + (int) $validated['quantity_change']);

        $sparepart->update(['stock_quantity' => $newStock]);

        // Cek apakah trigger MySQL trg_after_sparepart_stock_update menghasilkan audit
        $latestTriggerAlert = AuditLog::where('event_type', 'TRIGGER_STOCK_ALERT')
            ->where('record_id', $sparepart->id)
            ->latest('created_at')
            ->first();

        $triggerMsg = '';
        if ($newStock <= $sparepart->min_stock_alert && $validated['quantity_change'] < 0) {
            $triggerMsg = " (Trigger Database 'trg_after_sparepart_stock_update' TERPICU OTOMATIS: Peringatan stok kritis telah dicatat ke audit_logs).";
        }

        return redirect()->route('spareparts.index')->with('success', "Stok [{$sparepart->part_code}] disesuaikan dari {$oldStock} menjadi {$newStock} {$sparepart->unit}.{$triggerMsg}");
    }

    public function destroy(SparePart $sparepart): RedirectResponse
    {
        $code = $sparepart->part_code;
        $sparepart->delete();

        return redirect()->route('spareparts.index')->with('success', "Suku cadang {$code} berhasil dihapus.");
    }
}
