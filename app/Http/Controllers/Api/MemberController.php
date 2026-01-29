<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    /**
     * Display a listing of members.
     */
    public function index(Request $request)
    {
        $query = Member::query();

        // Search by name or member_id
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('member_id', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $members = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return send_response($members, 'Members retrieved successfully');
    }

    /**
     * Store a newly created member.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'mobile' => 'required|string|max:20',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'address' => 'nullable|string',
            'photo' => 'nullable|string', // Base64 encoded image
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'boolean',
            // Invoice fields
            'amount' => 'nullable|numeric|min:0',
            'payment_type' => 'nullable|in:cash,transfer',
        ]);

        if ($validator->fails()) {
            return send_error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $data = $validator->validated();

        // Generate unique member_id
        $data['member_id'] = Member::generateMemberId();

        // Handle Base64 photo upload
        if (!empty($data['photo'])) {
            $data['photo'] = $this->saveBase64Image($data['photo']);
        }

        // Remove invoice fields from member data
        $amount = $data['amount'] ?? null;
        $paymentType = $data['payment_type'] ?? 'cash';
        unset($data['amount'], $data['payment_type']);

        $member = Member::create($data);

        // Auto-create invoice if amount is provided
        $invoice = null;
        if ($amount && $amount > 0) {
            $invoice = Invoice::create([
                'invoice_id' => Invoice::generateInvoiceId(),
                'member_id' => $member->id,
                'amount' => $amount,
                'payment_type' => $paymentType,
                'start_date' => $member->start_date,
                'end_date' => $member->end_date,
                'user_id' => $request->user()->id,
            ]);
        }

        return send_response([
            'member' => $member,
            'invoice' => $invoice,
        ], 'Member created successfully', 201);
    }

    /**
     * Display the specified member.
     */
    public function show(Member $member)
    {
        return send_response($member, 'Member retrieved successfully');
    }

    /**
     * Update the specified member.
     */
    public function update(Request $request, Member $member)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'gender' => 'sometimes|required|in:male,female,other',
            'mobile' => 'sometimes|required|string|max:20',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'address' => 'nullable|string',
            'photo' => 'nullable|string', // Base64 encoded image
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return send_error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $data = $validator->validated();

        // Handle Base64 photo upload
        if (!empty($data['photo'])) {
            // Delete old photo if exists
            if ($member->photo) {
                $this->deletePhoto($member->photo);
            }
            $data['photo'] = $this->saveBase64Image($data['photo']);
        }

        $member->update($data);

        return send_response($member, 'Member updated successfully');
    }

    /**
     * Remove the specified member.
     */
    public function destroy(Member $member)
    {
        // Delete photo if exists
        if ($member->photo) {
            $this->deletePhoto($member->photo);
        }

        $member->delete();

        return send_response(null, 'Member deleted successfully');
    }

    /**
     * Save Base64 encoded image to storage.
     */
    private function saveBase64Image(string $base64Image): string
    {
        // Check if it's a valid base64 string
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            $extension = $matches[1];
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
        } else {
            $extension = 'png'; // Default extension
        }

        $imageData = base64_decode($base64Image);
        $fileName = 'members/' . Str::uuid() . '.' . $extension;

        // Save to public disk
        Storage::disk('public')->put($fileName, $imageData);

        return $fileName;
    }

    /**
     * Delete photo from storage.
     */
    private function deletePhoto(string $path): void
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
