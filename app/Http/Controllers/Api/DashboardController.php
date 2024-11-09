<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Member;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if ($this->getFinancialYear() == null) {
            return response()->json(['message' => 'No financial year found for current date.'], 404);
        }

        $startDate = $this->getFinancialYear()->start_date;
        $endDate = $this->getFinancialYear()->end_date;
        $id = $this->getFinancialYear()->id;

        $totalMembers = Member::where('status', 'active')->where('is_deleted',0)->count();
        $totalExpenses = Expense::where('date', '>=', $startDate)->where('date', '<=', $endDate)->sum('amount');
        $totalIncomes = Income::where('date', '>=', $startDate)->where('date', '<=', $endDate)->sum('amount');
        $results = DB::select("CALL sp_closing_balances(?)",[$id]);

        $result = [
            'total_members' => $totalMembers,
            'total_expenses' => $totalExpenses,
            'total_incomes' => $totalIncomes,
            'closing_balances' => $results
        ];

        return response()->json(['data' => $result], 200);
    }

    function getFinancialYear()
    {
        $currentDate = Carbon::now()->format('Y-m-d');
        $year = Year::where('start_date', '<=', $currentDate)
                      ->where('end_date', '>=', $currentDate)
                      ->first();
        if ($year) {
            return $year;
        } else {
            return null;
        }
    }
}
