<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses.
     */
    public function index(Request $request)
    {
        $query = Expense::with('user:id,name');

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by expense type
        if ($request->has('expense_type') && $request->expense_type !== '') {
            $query->where('expense_type', $request->expense_type);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $expenses = $query->orderBy('date', 'desc')->paginate($perPage);

        return send_response($expenses, 'Expenses retrieved successfully');
    }

    /**
     * Store a newly created expense.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'expense_type' => 'required|in:utilities,equipment,salary,maintenance,other',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return send_error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $data = $validator->validated();
        $data['user_id'] = $request->user()->id;

        $expense = Expense::create($data);

        return send_response($expense, 'Expense created successfully', 201);
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense)
    {
        $expense->load('user:id,name');
        return send_response($expense, 'Expense retrieved successfully');
    }

    /**
     * Update the specified expense.
     */
    public function update(Request $request, Expense $expense)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'date' => 'sometimes|required|date',
            'expense_type' => 'sometimes|required|in:utilities,equipment,salary,maintenance,other',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return send_error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $expense->update($validator->validated());

        return send_response($expense, 'Expense updated successfully');
    }

    /**
     * Remove the specified expense.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();
        return send_response(null, 'Expense deleted successfully');
    }
}
