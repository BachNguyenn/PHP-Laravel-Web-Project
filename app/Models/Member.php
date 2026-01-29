<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'name',
        'gender',
        'mobile',
        'blood_group',
        'address',
        'photo',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'boolean',
    ];

    /**
     * Generate a unique member ID with format: YYMM + sequential number
     * Example: 2601001, 2601002, ...
     */
    public static function generateMemberId(): string
    {
        $prefix = date('ym'); // e.g., "2601" for January 2026
        
        // Find the last member with this prefix
        $lastMember = self::where('member_id', 'like', $prefix . '%')
            ->orderBy('member_id', 'desc')
            ->first();
        
        if ($lastMember) {
            // Extract the sequence number and increment
            $lastSeq = (int) substr($lastMember->member_id, 4);
            $newSeq = $lastSeq + 1;
        } else {
            $newSeq = 1;
        }
        
        // Format: YYMM + 3-digit sequence (e.g., 2601001)
        return $prefix . str_pad($newSeq, 3, '0', STR_PAD_LEFT);
    }
}
