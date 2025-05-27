<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_tickets' => $this['total_tickets'],
            'active_tickets' => $this['active_tickets'],
            'solved_tickets' => $this['solved_tickets'],
            'avg_resolution_time' => $this['avg_resolution_time'],
            'status_distribution' => [
                'open' => $this['status_distribution']['open'],
                'in_progress' => $this['status_distribution']['in_progress'],
                'solved' => $this['status_distribution']['solved'],
                'rejected' => $this['status_distribution']['rejected'],
            ]
        ];
    }
}
