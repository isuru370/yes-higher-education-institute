<?php

namespace App\Services;

use App\Models\Student;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class StudentIdCardService
{
    public function getStudentForIdCard(string $customId)
    {
        try {
            $student = Student::where('custom_id', $customId)
                ->select('custom_id', 'full_name', 'initial_name', 'address1', 'address2', 'address3', 'img_url', 'created_at')
                ->first();

            if (!$student) {
                return null;
            }

            return [
                'custom_id' => $student->custom_id,
                'fname' => $student->full_name,
                'lname' => $student->initial_name,
                'address' => trim(($student->address1 ?? '') . ' ' . ($student->address2 ?? '') . ' ' . ($student->address3 ?? '')),
                'img_url' => $this->fixImageUrl($student->img_url),
                'created_at' => $student->created_at ?? null,
            ];
        } catch (Exception $e) {
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    /**
     * පිටුකීරීම සමග සියලු සිසුන් ලබා ගැනීම
     */
    public function getAllStudentsForIdCard(
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
        int $perPage = 20,
        ?string $searchDate = null,
        ?string $searchName = null,
        ?string $searchCustomId = null
    ) {
        try {
            $query = Student::select(
                'custom_id',
                'full_name',
                'initial_name',
                'address1',
                'address2',
                'address3',
                'img_url',
                'created_at'
            );

            // Date filter
            if ($searchDate) {
                $query->whereDate('created_at', $searchDate);
            }

            // Name filter
            if ($searchName) {
                $query->where(function ($q) use ($searchName) {
                    $q->where('full_name', 'like', "%{$searchName}%")
                        ->orWhere('initial_name', 'like', "%{$searchName}%");
                });
            }

            // Custom ID filter
            if ($searchCustomId) {
                $query->where('custom_id', 'like', "%{$searchCustomId}%");
            }

            $students = $query
                ->orderBy($sortBy, $sortOrder)
                ->paginate($perPage);

            $students->getCollection()->transform(function ($student) {
                return [
                    'custom_id' => $student->custom_id,
                    'fname' => $student->full_name,
                    'lname' => $student->initial_name,
                    'address' => trim(
                        ($student->address1 ?? '') . ' ' .
                            ($student->address2 ?? '') . ' ' .
                            ($student->address3 ?? '')
                    ),
                    'img_url' => $this->fixImageUrl($student->img_url),
                    'created_at' => optional($student->created_at)->format('Y-m-d H:i:s'),
                ];
            });

            return $students;
        } catch (Exception $e) {
            return new LengthAwarePaginator([], 0, $perPage);
        }
    }


    /**
     * රූප URL හැඩගස්වන්න
     */
    private function fixImageUrl($imgUrl)
    {
        if (empty($imgUrl)) {
            return null;
        }

        // URL එකක් නම් එයම යොදන්න
        if (filter_var($imgUrl, FILTER_VALIDATE_URL)) {
            return $imgUrl;
        }

        // සාපේක්ෂ පථයක් නම් asset() භාවිතා කරන්න
        $cleanPath = ltrim($imgUrl, '/');
        return asset($cleanPath);
    }
}
