<?php

namespace App\Http\Controllers;

use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organizationId = Auth::user()->organization_id;
        
        $paymentGateways = PaymentGateway::where('organization_id', $organizationId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('payment-gateways.index', compact('paymentGateways'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $gatewayTypes = PaymentGateway::getGatewayTypes();
        return view('payment-gateways.create', compact('gatewayTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $organizationId = Auth::user()->organization_id;

        $validated = $request->validate([
            'gateway_name' => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'is_manual' => 'boolean',
            'is_default' => 'boolean',
            'credentials' => 'nullable|string',
            'settings' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // If this is set as default, unset other defaults
        if ($request->has('is_default') && $request->is_default) {
            PaymentGateway::where('organization_id', $organizationId)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        // Parse JSON fields
        if ($request->has('credentials') && $request->credentials) {
            $decoded = json_decode($request->credentials, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['credentials'] = $decoded;
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid JSON format for credentials. Please check your JSON syntax.');
            }
        } else {
            $validated['credentials'] = null;
        }

        if ($request->has('settings') && $request->settings) {
            $decoded = json_decode($request->settings, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['settings'] = $decoded;
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid JSON format for settings. Please check your JSON syntax.');
            }
        } else {
            $validated['settings'] = null;
        }

        $validated['organization_id'] = $organizationId;
        $validated['created_by'] = Auth::user()->id;
        $validated['updated_by'] = Auth::user()->id;

        PaymentGateway::create($validated);

        return redirect()->route('payment-gateways.index')
            ->with('success', 'Payment gateway created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentGateway $paymentGateway)
    {
        // Verify gateway belongs to user's organization
        if ($paymentGateway->organization_id !== Auth::user()->organization_id) {
            return redirect()->route('payment-gateways.index')
                ->with('error', 'Unauthorized access.');
        }

        return view('payment-gateways.show', compact('paymentGateway'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentGateway $paymentGateway)
    {
        // Verify gateway belongs to user's organization
        if ($paymentGateway->organization_id !== Auth::user()->organization_id) {
            return redirect()->route('payment-gateways.index')
                ->with('error', 'Unauthorized access.');
        }

        $gatewayTypes = PaymentGateway::getGatewayTypes();
        return view('payment-gateways.edit', compact('paymentGateway', 'gatewayTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentGateway $paymentGateway)
    {
        // Verify gateway belongs to user's organization
        if ($paymentGateway->organization_id !== Auth::user()->organization_id) {
            return redirect()->route('payment-gateways.index')
                ->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'gateway_name' => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'is_manual' => 'boolean',
            'is_default' => 'boolean',
            'credentials' => 'nullable|string',
            'settings' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // If this is set as default, unset other defaults
        if ($request->has('is_default') && $request->is_default) {
            PaymentGateway::where('organization_id', $paymentGateway->organization_id)
                ->where('id', '!=', $paymentGateway->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        // Parse JSON fields
        if ($request->has('credentials') && $request->credentials) {
            $decoded = json_decode($request->credentials, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['credentials'] = $decoded;
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid JSON format for credentials. Please check your JSON syntax.');
            }
        } else {
            $validated['credentials'] = $paymentGateway->credentials;
        }

        if ($request->has('settings') && $request->settings) {
            $decoded = json_decode($request->settings, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['settings'] = $decoded;
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid JSON format for settings. Please check your JSON syntax.');
            }
        } else {
            $validated['settings'] = $paymentGateway->settings;
        }

        $validated['updated_by'] = Auth::user()->id;

        $paymentGateway->update($validated);

        return redirect()->route('payment-gateways.index')
            ->with('success', 'Payment gateway updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentGateway $paymentGateway)
    {
        // Verify gateway belongs to user's organization
        if ($paymentGateway->organization_id !== Auth::user()->organization_id) {
            return redirect()->route('payment-gateways.index')
                ->with('error', 'Unauthorized access.');
        }

        $paymentGateway->delete();

        return redirect()->route('payment-gateways.index')
            ->with('success', 'Payment gateway deleted successfully!');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(PaymentGateway $paymentGateway)
    {
        // Verify gateway belongs to user's organization
        if ($paymentGateway->organization_id !== Auth::user()->organization_id) {
            return redirect()->route('payment-gateways.index')
                ->with('error', 'Unauthorized access.');
        }

        $paymentGateway->update([
            'is_active' => !$paymentGateway->is_active,
            'updated_by' => Auth::user()->id,
        ]);

        return redirect()->route('payment-gateways.index')
            ->with('success', 'Payment gateway status updated successfully!');
    }

    /**
     * Set as default gateway
     */
    public function setDefault(PaymentGateway $paymentGateway)
    {
        // Verify gateway belongs to user's organization
        if ($paymentGateway->organization_id !== Auth::user()->organization_id) {
            return redirect()->route('payment-gateways.index')
                ->with('error', 'Unauthorized access.');
        }

        // Unset other defaults
        PaymentGateway::where('organization_id', $paymentGateway->organization_id)
            ->where('id', '!=', $paymentGateway->id)
            ->update(['is_default' => false]);

        $paymentGateway->update([
            'is_default' => true,
            'updated_by' => Auth::user()->id,
        ]);

        return redirect()->route('payment-gateways.index')
            ->with('success', 'Default payment gateway updated successfully!');
    }
}
