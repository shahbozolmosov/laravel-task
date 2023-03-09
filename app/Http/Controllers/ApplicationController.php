<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        if ($this->checkDate()) {
            return redirect()->back()->with('error', 'You can create only one application at a day.');
        }

        $validate = $request->validate([
            'subject' => 'required|max:255',
            'message' => 'required',
            'file' => 'file|mimes:jpg,png,pdf'
        ]);

        if ($request->hasFile('file')) {
            $name = $request->file('file')->getClientOriginalName();
            $path = $request->file('file')->storeAs(
                'files',
                $name,
                'public'
            );
        }

        $application = Application::create([
            'user_id' => auth()->user()->id,
            'subject' => $request->subject,
            'message' => $request->message,
            'file_url' => $path ?? null,
        ]);

        dispatch(new SendEmailJob($application));

        return redirect()->back();
    }

    protected function checkDate()
    {
        $last_application = auth()->user()->applications()->latest()->first();
        if ($last_application) {
            $last_app_date = Carbon::parse($last_application->created_at)->format('Y-m-d');
            $tody = Carbon::now()->format('Y-m-d');

            if ($last_app_date === $tody) {
                return true;
            }
        }
    }
}
