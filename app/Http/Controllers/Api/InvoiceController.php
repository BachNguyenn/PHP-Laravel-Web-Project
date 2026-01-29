<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['member:id,member_id,name,mobile', 'user:id,name']);

        // Search by invoice_id or member name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_id', 'like', '%' . $search . '%')
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('name', 'like', '%' . $search . '%')
                         ->orWhere('member_id', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by payment type
        if ($request->has('payment_type') && $request->payment_type !== '') {
            $query->where('payment_type', $request->payment_type);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Filter by member_id
        if ($request->has('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $invoices = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return send_response($invoices, 'Invoices retrieved successfully');
    }

    /**
     * Display the specified invoice (for print/view).
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['member', 'user:id,name']);
        
        // Also return gym settings for invoice header
        $settings = Setting::getAllSettings();

        return send_response([
            'invoice' => $invoice,
            'settings' => $settings,
        ], 'Invoice retrieved successfully');
    }

    /**
     * Store a manually created invoice.
     */
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:cash,transfer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return send_error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $data = $validator->validated();
        $data['invoice_id'] = Invoice::generateInvoiceId();
        $data['user_id'] = $request->user()->id;

        $invoice = Invoice::create($data);
        $invoice->load(['member:id,member_id,name', 'user:id,name']);

        return send_response($invoice, 'Invoice created successfully', 201);
    }
}
