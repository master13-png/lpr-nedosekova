<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function feedbackForm()
    {
        return view('feedback');
    }

    public function feedbackSend(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'message' => 'required|string|max:1000',
        ]);

        return redirect()
            ->route('feedback.form')
            ->with(
                'status',
                "Спасибо, {$data['name']}! Сообщение получено: {$data['message']}"
            );
    }
}
