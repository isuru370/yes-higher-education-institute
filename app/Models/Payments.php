<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'payment_date',
        'status',
        'amount',
        'payment_for',
        'student_id',
        'student_student_student_classes_id',
        'user_id',
    ];

    // Type casting for JSON responses
    protected $casts = [
        'status'                             => 'boolean',
        'amount'                             => 'double',
        'student_id'                          => 'integer',
        'student_student_student_classes_id'  => 'integer',
        'payment_date'                        => 'datetime',
        'created_at'                          => 'datetime',
        'updated_at'                          => 'datetime',
        'user_id'                          => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function studentStudentClass()
    {
        return $this->belongsTo(StudentStudentStudentClass::class, 'student_student_student_classes_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
