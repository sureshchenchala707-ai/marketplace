<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Http\Controllers\NotificationController;
use App\Notifications\VendorApplicationStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::with('user');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('shop_name', 'like', "%{$search}%")
                    ->orWhere('business_email', 'like', "%{$search}%")
                    ->orWhere('business_phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') &&
            in_array($request->status, ['pending', 'approved', 'rejected', 'suspended'], true)) {
            $query->where('status', $request->status);
        }

        $vendors = $query->latest()
            ->paginate(10)
            ->withQueryString();

        $pendingCount = Vendor::where('status', 'pending')->count();
        $approvedCount = Vendor::where('status', 'approved')->count();
        $rejectedCount = Vendor::where('status', 'rejected')->count();
        $suspendedCount = Vendor::where('status', 'suspended')->count();

        return view('admin.vendors.index', compact(
            'vendors',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'suspendedCount'
        ));
    }

    public function show(Vendor $vendor)
    {
        $vendor->load(['user', 'approver']);

        return view('admin.vendors.show', compact('vendor'));
    }

    public function approve(Vendor $vendor)
    {
        $vendorUser = DB::transaction(function () use ($vendor) {
            $lockedVendor = Vendor::whereKey($vendor->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedVendor->status !== 'pending') {
                abort(422, 'Only pending vendor applications can be approved.');
            }

            $lockedVendor->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'admin_remarks' => null,
            ]);

            return $lockedVendor->user()->firstOrFail();
        });

        // Send only after the database transaction commits.
        $vendorUser->notify(
            new VendorApplicationStatusNotification('approved')
        );

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', 'Vendor application approved successfully.');
    }

    public function reject(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'admin_remarks' => ['required', 'string', 'max:1000'],
        ]);

        $vendorUser = DB::transaction(function () use ($vendor, $validated) {
            $lockedVendor = Vendor::whereKey($vendor->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedVendor->status !== 'pending') {
                abort(422, 'Only pending vendor applications can be rejected.');
            }

            $lockedVendor->update([
                'status' => 'rejected',
                'admin_remarks' => $validated['admin_remarks'],
                'approved_by' => null,
                'approved_at' => null,
            ]);

            return $lockedVendor->user()->firstOrFail();
        });

        // Send only after the database transaction commits.
        $vendorUser->notify(
            new VendorApplicationStatusNotification(
                'rejected',
                $validated['admin_remarks']
            )
        );

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', 'Vendor application rejected.');
    }

    public function suspend(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'admin_remarks' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($vendor, $validated) {
            $lockedVendor = Vendor::whereKey($vendor->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedVendor->status !== 'approved') {
                abort(422, 'Only approved vendors can be suspended.');
            }

            $lockedVendor->update([
                'status' => 'suspended',
                'admin_remarks' => $validated['admin_remarks'],
            ]);
        });

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', 'Vendor suspended successfully.');
    }

    public function reactivate(Vendor $vendor)
    {
        DB::transaction(function () use ($vendor) {
            $lockedVendor = Vendor::whereKey($vendor->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedVendor->status !== 'suspended') {
                abort(422, 'Only suspended vendors can be reactivated.');
            }

            $lockedVendor->update([
                'status' => 'approved',
                'admin_remarks' => null,
            ]);
        });

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', 'Vendor reactivated successfully.');
    }
}
