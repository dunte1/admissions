<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $school = current_school();
        $type = $request->query('type', 'email');
        
        $templates = NotificationTemplate::where('school_id', $school?->id)
            ->where('type', $type)
            ->orderBy('event')
            ->get();

        // Auto-create default templates if none exist
        if ($templates->isEmpty() && $school) {
            $defaults = NotificationTemplate::getDefaultTemplates();
            foreach ($defaults as $default) {
                if ($default['type'] === $type) {
                    NotificationTemplate::updateOrCreate(
                        [
                            'school_id' => $school->id,
                            'type' => $default['type'],
                            'event' => $default['event'],
                        ],
                        [
                            'subject' => $default['subject'] ?? null,
                            'body' => $default['body'],
                            'is_active' => true,
                            'variables' => json_encode(array_keys(NotificationTemplate::getAvailableVariables())),
                        ]
                    );
                }
            }
            $templates = NotificationTemplate::where('school_id', $school->id)
                ->where('type', $type)
                ->orderBy('event')
                ->get();
        }

        $events = NotificationTemplate::getAvailableEvents();
        $variables = NotificationTemplate::getAvailableVariables();

        return view('admin.settings.notification-templates', compact(
            'templates',
            'events',
            'variables',
            'type'
        ));
    }

    public function edit(NotificationTemplate $template)
    {
        $this->authorize('update', $template);
        
        $events = NotificationTemplate::getAvailableEvents();
        $variables = NotificationTemplate::getAvailableVariables();

        return view('admin.settings.edit-notification-template', compact(
            'template',
            'events',
            'variables'
        ));
    }

    public function update(Request $request, NotificationTemplate $template)
    {
        $this->authorize('update', $template);

        $validated = $request->validate([
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $template->update($validated);

        return redirect()->route('admin.notification-templates.index', ['type' => $template->type])
            ->with('success', 'Template updated successfully.');
    }

    public function reset(NotificationTemplate $template)
    {
        $this->authorize('update', $template);

        $defaults = NotificationTemplate::getDefaultTemplates();
        
        $default = collect($defaults)->first(function ($item) use ($template) {
            return $item['type'] === $template->type && $item['event'] === $template->event;
        });

        if ($default) {
            $template->update([
                'subject' => $default['subject'] ?? null,
                'body' => $default['body'],
            ]);
        }

        return redirect()->back()->with('success', 'Template reset to default.');
    }

    public function resetAll()
    {
        $school = current_school();
        $defaults = NotificationTemplate::getDefaultTemplates();

        foreach ($defaults as $default) {
            NotificationTemplate::updateOrCreate(
                [
                    'school_id' => $school?->id,
                    'type' => $default['type'],
                    'event' => $default['event'],
                ],
                [
                    'subject' => $default['subject'] ?? null,
                    'body' => $default['body'],
                    'is_active' => true,
                    'variables' => json_encode(array_keys(NotificationTemplate::getAvailableVariables())),
                ]
            );
        }

        return redirect()->back()->with('success', 'All templates reset to defaults.');
    }
}

