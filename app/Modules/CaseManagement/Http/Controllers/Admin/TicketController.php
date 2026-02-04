<?php

namespace App\Modules\CaseManagement\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\CaseManagement\Models\Ticket;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::latest('created_at')->take(10)->get();
        return view('admin.pages.tickets', compact('tickets'));
    }
}
