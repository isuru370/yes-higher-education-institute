<?php

namespace App\Http\Controllers;

use App\Services\BankBranchService;

class BankBranchController extends Controller
{
    protected $bankBranchService;

    public function __construct(BankBranchService $bankBranchService)
    {
        $this->bankBranchService = $bankBranchService;
    }

    public function fetchDropdownBranches($bankId)
    {
        return $this->bankBranchService->fetchDropdownBranches($bankId);
    }

    public function fetchBranches($bankId)
    {
        return $this->bankBranchService->fetchBranches($bankId);
    }
}
