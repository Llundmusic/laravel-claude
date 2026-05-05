<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function setActiveCompany(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'integer'],
        ]);

        $user = $request->user();
        $companyId = (int) $validated['company_id'];

        // Only allow switching to a company the user actually belongs to
        if (! $user->companies()->where('companies.id', $companyId)->exists()) {
            abort(403);
        }

        $user->update(['active_company_id' => $companyId]);

        return redirect()->back();
    }
}
