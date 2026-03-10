<?php

namespace App\Models;

use App\Enums\ClassType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $fillable = [
        'custom_id',
        'temporary_qr_code',
        'temporary_qr_code_expire_date',
        'full_name',
        'initial_name',
        'mobile',
        'email',
        'whatsapp_mobile',
        'nic',
        'bday',
        'gender',
        'address1',
        'address2',
        'address3',
        'guardian_fname',
        'guardian_lname',
        'guardian_nic',
        'guardian_mobile',
        'is_active',
        'img_url',
        'grade_id',
        'class_type',
        'admission',
        'student_school',
        'permanent_qr_active',
        'student_disable'
    ];

    protected $casts = [
        'temporary_qr_code_expire_date' => 'datetime',
        'grade_id'            => 'integer',
        'admission'           => 'boolean',
        'is_active'           => 'boolean',
        'permanent_qr_active' => 'boolean',
        'student_disable'     => 'boolean',
        'bday'                => 'date',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
    ];

    // ===========================
    // Relationships
    // ===========================

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function portalLogin(): HasOne
    {
        return $this->hasOne(StudentPortalLogin::class);
    }

    public function studentResults(): HasMany
    {
        return $this->hasMany(StudentResults::class, 'student_id');
    }

    // ===========================
    // Helper Methods
    // ===========================

    public function isOnline(): bool
    {
        return $this->class_type === ClassType::ONLINE;
    }

    public function isOffline(): bool
    {
        return $this->class_type === ClassType::OFFLINE;
    }

    public function hasActivePermanentQr(): bool
    {
        return $this->permanent_qr_active;
    }
}
