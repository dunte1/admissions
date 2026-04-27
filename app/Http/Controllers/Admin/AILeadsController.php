<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AILead;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AILeadsController extends Controller
{
    public function index(Request $request): View
    {
        $query = AILead::forSchool(Auth::user()->school_id);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $leads = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.ai.leads.index', compact('leads'));
    }

    public function show(AILead $lead): View
    {
        $this->authorizeLead($lead);

        return view('admin.ai.leads.show', compact('lead'));
    }

    public function update(Request $request, AILead $lead): JsonResponse
    {
        $this->authorizeLead($lead);

        $lead->update([
            'notes' => $request->input('notes'),
        ]);

        if ($request->input('action') === 'contacted') {
            $lead->markAsContacted();
        } elseif ($request->input('action') === 'converted') {
            $lead->markAsConverted();
        } elseif ($request->input('action') === 'lost') {
            $lead->markAsLost();
        }

        return response()->json(['success' => true, 'message' => 'Lead updated']);
    }

    public function destroy(AILead $lead): JsonResponse
    {
        $this->authorizeLead($lead);

        $lead->delete();

        return response()->json(['success' => true, 'message' => 'Lead deleted']);
    }

    public function export(Request $request)
    {
        $leads = AILead::forSchool(Auth::user()->school_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $csvData = [];
        $csvData[] = ['Name', 'Email', 'Phone', 'Program Interest', 'Status', 'Source', 'Created At'];

        foreach ($leads as $lead) {
            $csvData[] = [
                $lead->name,
                $lead->email,
                $lead->phone,
                $lead->program_interest,
                $lead->status,
                $lead->source,
                $lead->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $filename = 'ai-leads-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function authorizeLead(AILead $lead): void
    {
        if ($lead->school_id !== Auth::user()->school_id) {
            abort(403, 'Unauthorized');
        }
    }
}
