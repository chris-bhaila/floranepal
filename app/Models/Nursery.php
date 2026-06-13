<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Plant;

class Nursery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'location',
        'description',
        'contact_phone',
        'contact_email',
        'reg_cer',
        'pan_cer',
        'google_id',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Resolves the storage directory owner even when user_id is null (decoupled nursery).
    // Files are named {user_id}_{type}.{ext}, so the prefix is the original owner's ID.
    public function getCertOwnerIdAttribute(): ?int
    {
        if ($this->user_id) return $this->user_id;
        $filename = $this->reg_cer ?? $this->pan_cer;
        if (!$filename) return null;
        $prefix = explode('_', $filename)[0];
        return is_numeric($prefix) ? (int) $prefix : null;
    }
    public function plants()
    {
        return $this->hasMany(Plant::class);
    }
}
