<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketStoreRequest;
use App\Http\Resources\TicketReplyResource;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\TicketReply;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{

    public function index(Request $request)
    {
        try {
            $query = Ticket::query();

            $query->orderBy('created_at', 'desc');

            if ($request->search) {
                $query->where('code', 'like', '%' . $request->search . '%')
                    ->orWhere('title', 'like', '%' . $request->search . '%');
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->priority) {
                $query->where('priority', $request->priority);
            }

            if (auth()->user()->role == 'user') {
                $query->where('user_id', auth()->id());
            }

            $tickets = $query->get();

            return response()->json([
                'message' => 'Tickets retrieved successfully',
                'data' => TicketResource::collection($tickets)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve tickets: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function show($code)
    {
        try {
            $ticket = Ticket::where('code', $code)->firstOrFail();

            if (!$ticket) {
                return response()->json([
                    'error' => 'Ticket not found'
                ])->setStatusCode(404);
            }

            if (auth()->user()->role == 'user' && $ticket->user_id != auth()->user()->id()) {
                return response()->json([
                    'error' => 'Unauthorized access to this ticket'
                ])->setStatusCode(403);
            }

            return response()->json([
                'message' => 'Ticket retrieved successfully',
                'data' => new TicketResource($ticket)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Ticket not found: ' . $e->getMessage()
            ])->setStatusCode(404);
        }
    }

    public function store(TicketStoreRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $ticket = new Ticket;
            $ticket->user_id = auth()->id();
            $ticket->code = 'TICKET-' . rand(10000, 9999);
            $ticket->title = $data['title'];
            $ticket->description = $data['description'];
            $ticket->priority = $data['priority'];
            $ticket->status = 'open';
            $ticket->save();

            DB::commit();

            return response()->json([
                'message' => 'Ticket created successfully',
                'data' => new TicketResource($ticket)
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create ticket'], 500);
        }
    }

    public function storeTicketReply(Request $request, $code)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $ticket = Ticket::where('code', $code)->firstOrFail();
            if (!$ticket) {
                return response()->json([
                    'error' => 'Ticket not found'
                ])->setStatusCode(404);
            }
            if (auth()->user()->role == 'user' && $ticket->user_id != auth()->id()) {
                return response()->json([
                    'error' => 'Unauthorized access to this ticket'
                ])->setStatusCode(403);
            }

            $ticketReply = new TicketReply();
            $ticketReply->ticket_id = $ticket->id;
            $ticketReply->user_id = auth()->user()->id();
            $ticketReply->content = $data['content'];

            $ticketReply->save();

            if(auth()->user()->role == 'admin') {
                $ticket->status = $data['status'] ?? $ticket->status;
                if ($ticket->status == 'solved') {
                    $ticket->solved_at = now();
                }
                $ticket->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Ticket reply created successfully',
                'data' => new TicketReplyResource($ticketReply)
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to create ticket reply: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function destroy($code)
    {
        try {
            $ticket = Ticket::where('code', $code)->firstOrFail();

            if (!$ticket) {
                return response()->json([
                    'error' => 'Ticket not found'
                ])->setStatusCode(404);
            }

            if (auth()->user()->role == 'user' && $ticket->user_id != auth()->id()) {
                return response()->json([
                    'error' => 'Unauthorized access to this ticket'
                ])->setStatusCode(403);
            }

            $ticket->delete();

            return response()->json([
                'message' => 'Ticket deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to delete ticket: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
