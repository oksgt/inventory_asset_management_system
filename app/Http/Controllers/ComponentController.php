<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DcCategory;
use App\Models\DcComponent;
use App\Services\MaintenanceService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComponentController extends Controller
{
    public function __construct(
        protected MaintenanceService $maintenanceService
    ) {}

    public function index(Request $request): View
    {
        $query = DcComponent::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $term = '%'.$request->input('search').'%';
            $query->where(function ($q) use ($term) {
                $q->where('component_code', 'like', $term)
                    ->orWhere('name', 'like', $term)
                    ->orWhere('serial_number', 'like', $term)
                    ->orWhere('rack_location', 'like', $term);
            });
        }

        $components = $query->orderBy('rack_location')->orderBy('component_code')->paginate(12)->withQueryString();
        $categories = DcCategory::orderBy('name')->get();

        // Data view untuk status kesehatan
        $statusView = DB::table('view_component_maintenance_status')->get()->keyBy('id');

        return view('components.index', compact('components', 'categories', 'statusView'));
    }

    public function create(): View
    {
        $categories = DcCategory::orderBy('name')->get();

        return view('components.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:dc_categories,id'],
            'component_code' => ['required', 'string', 'max:50', 'unique:dc_components,component_code'],
            'name' => ['required', 'string', 'max:150'],
            'model' => ['required', 'string', 'max:100'],
            'serial_number' => ['required', 'string', 'max:100', 'unique:dc_components,serial_number'],
            'rack_location' => ['required', 'string', 'max:50'],
            'rack_unit' => ['required', 'string', 'max:20'],
            'ip_address' => ['nullable', 'ip'],
            'status' => ['required', 'in:operational,warning,maintenance_required,under_maintenance,decommissioned'],
            'condition_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'vendor_sla' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['last_maintenance_at'] = Carbon::now();
        $validated['next_maintenance_at'] = Carbon::now()->addDays(90);

        $component = DcComponent::create($validated);

        AuditLog::create([
            'event_type' => 'COMPONENT_CREATED',
            'table_name' => 'dc_components',
            'record_id' => $component->id,
            'message' => "Komponen baru didaftarkan: [{$component->component_code}] {$component->name} di {$component->rack_location}",
            'payload_json' => $validated,
        ]);

        return redirect()->route('components.show', $component)->with('success', "Komponen {$component->component_code} berhasil ditambahkan ke inventaris Pusat Data.");
    }

    public function show(DcComponent $component): View
    {
        $component->load(['category', 'schedules', 'logs.sparePartUsages.sparePart']);

        // Panggil Stored Function MySQL
        $sqlMetrics = $this->maintenanceService->getComponentSqlMetrics($component->id);

        return view('components.show', compact('component', 'sqlMetrics'));
    }

    public function edit(DcComponent $component): View
    {
        $categories = DcCategory::orderBy('name')->get();

        return view('components.edit', compact('component', 'categories'));
    }

    public function update(Request $request, DcComponent $component): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:dc_categories,id'],
            'component_code' => ['required', 'string', 'max:50', 'unique:dc_components,component_code,'.$component->id],
            'name' => ['required', 'string', 'max:150'],
            'model' => ['required', 'string', 'max:100'],
            'serial_number' => ['required', 'string', 'max:100', 'unique:dc_components,serial_number,'.$component->id],
            'rack_location' => ['required', 'string', 'max:50'],
            'rack_unit' => ['required', 'string', 'max:20'],
            'ip_address' => ['nullable', 'ip'],
            'status' => ['required', 'in:operational,warning,maintenance_required,under_maintenance,decommissioned'],
            'condition_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'vendor_sla' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $component->update($validated);

        AuditLog::create([
            'event_type' => 'COMPONENT_UPDATED',
            'table_name' => 'dc_components',
            'record_id' => $component->id,
            'message' => "Data komponen [{$component->component_code}] diperbarui.",
            'payload_json' => $validated,
        ]);

        return redirect()->route('components.show', $component)->with('success', "Data komponen {$component->component_code} berhasil diperbarui.");
    }

    public function destroy(DcComponent $component): RedirectResponse
    {
        $code = $component->component_code;
        $component->delete();

        AuditLog::create([
            'event_type' => 'COMPONENT_DELETED',
            'table_name' => 'dc_components',
            'record_id' => null,
            'message' => "Komponen [{$code}] telah dihapus dari sistem.",
        ]);

        return redirect()->route('components.index')->with('success', "Komponen {$code} berhasil dihapus.");
    }
}
