<?php

namespace App\Models;

use App\Enums\ClassType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'student_classes';

    protected $fillable = [
        'class_name',
        'class_type',
        'medium',
        'teacher_percentage',
        'is_active',
        'is_ongoing',
        'teacher_id',
        'subject_id',
        'grade_id'
    ];

    protected $casts = [
        'teacher_percentage' => 'decimal:2',
        'is_active'         => 'boolean',
        'is_ongoing'        => 'boolean',
        'teacher_id'        => 'integer',
        'subject_id'        => 'integer',
        'grade_id'          => 'integer',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    // Helpers
    public function isOnline(): bool
    {
        return $this->class_type === ClassType::ONLINE;
    }

    public function isOffline(): bool
    {
        return $this->class_type === ClassType::OFFLINE;
    }
}
