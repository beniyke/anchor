<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Analytics service for Support package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Support\Services;

use Database\DB;
use Helpers\DateTimeHelper;
use Support\Enums\TicketPriority;
use Support\Enums\TicketStatus;
use Support\Models\Ticket;

class SupportAnalyticsService
{
    public function getDashboard(): array
    {
        $now = DateTimeHelper::now();

        return [
            'overview' => $this->getOverview(),
            'by_status' => $this->getByStatus(),
            'by_priority' => $this->getByPriority(),
            'sla_metrics' => $this->getSlaMetrics(),
            'agent_performance' => $this->getAgentPerformance(),
            'response_times' => $this->getResponseTimeMetrics(),
        ];
    }

    public function getOverview(): array
    {
        $today = DateTimeHelper::now()->format('Y-m-d');
        $thisWeek = DateTimeHelper::now()->subDays(7)->format('Y-m-d');
        $thisMonth = DateTimeHelper::now()->subDays(30)->format('Y-m-d');

        return [
            'total_tickets' => Ticket::count(),
            'open_tickets' => Ticket::open()->count(),
            'unassigned_tickets' => Ticket::whereNull('assigned_to')->open()->count(),
            'tickets_today' => Ticket::where('created_at', '>=', $today)->count(),
            'tickets_this_week' => Ticket::where('created_at', '>=', $thisWeek)->count(),
            'tickets_this_month' => Ticket::where('created_at', '>=', $thisMonth)->count(),
            'avg_resolution_time' => $this->calculateAvgResolutionTime(),
            'first_response_time' => $this->calculateAvgFirstResponseTime(),
        ];
    }

    public function getByStatus(): array
    {
        $stats = Ticket::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $result = [];
        foreach (TicketStatus::cases() as $status) {
            $result[$status->value] = 0;
        }

        foreach ($stats as $stat) {
            $result[$stat->status->value] = $stat->count;
        }

        return $result;
    }

    public function getByPriority(): array
    {
        $stats = Ticket::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->get();

        $result = [];
        foreach (TicketPriority::cases() as $priority) {
            $result[$priority->value] = 0;
        }

        foreach ($stats as $stat) {
            $result[$stat->priority->value] = $stat->count;
        }

        return $result;
    }

    public function getSlaMetrics(): array
    {
        $now = DateTimeHelper::now()->toDateTimeString();
        $atRiskThreshold = DateTimeHelper::now()->addHours(2)->toDateTimeString();

        $stats = Ticket::open()
            ->selectRaw('COUNT(*) as total_open')
            ->selectRaw('SUM(CASE WHEN sla_due_at < ? THEN 1 ELSE 0 END) as breached', [$now])
            ->selectRaw('SUM(CASE WHEN sla_due_at >= ? AND sla_due_at <= ? THEN 1 ELSE 0 END) as at_risk', [$now, $atRiskThreshold])
            ->first();

        $totalOpen = (int) $stats->total_open;
        $breached = (int) $stats->breached;

        return [
            'total_open' => $totalOpen,
            'breached' => $breached,
            'at_risk' => (int) $stats->at_risk,
            'compliance_rate' => $totalOpen > 0
                ? round((($totalOpen - $breached) / $totalOpen) * 100, 2)
                : 100,
        ];
    }

    public function getAgentPerformance(int $limit = 10): array
    {
        $agents = Ticket::selectRaw('assigned_to, COUNT(*) as total_tickets')
            ->whereNotNull('assigned_to')
            ->groupBy('assigned_to')
            ->orderBy('total_tickets', 'desc')
            ->limit($limit)
            ->get();

        $result = [];

        foreach ($agents as $agent) {
            $agentTickets = Ticket::where('assigned_to', $agent->assigned_to);

            $result[] = [
                'agent_id' => $agent->assigned_to,
                'total_tickets' => $agent->total_tickets,
                'resolved' => (clone $agentTickets)->whereIn('status', [TicketStatus::RESOLVED, TicketStatus::CLOSED])->count(),
                'open' => (clone $agentTickets)->open()->count(),
            ];
        }

        return $result;
    }

    public function getByCategory(): array
    {
        return Ticket::selectRaw('category_id, COUNT(*) as ticket_count')
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->orderBy('ticket_count', 'desc')
            ->get()
            ->all();
    }

    public function getDailyTrends(int $days = 30): array
    {
        $startDate = DateTimeHelper::now()->subDays($days)->format('Y-m-d');

        $createdStats = Ticket::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date');

        $resolvedStats = Ticket::whereNotNull('resolved_at')
            ->selectRaw('DATE(resolved_at) as date, COUNT(*) as count')
            ->where('resolved_at', '>=', $startDate)
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date');

        $trends = [];
        for ($i = 0; $i < $days; $i++) {
            $date = DateTimeHelper::now()->subDays($days - $i - 1)->format('Y-m-d');
            $trends[] = [
                'date' => $date,
                'created' => $createdStats[$date] ?? 0,
                'resolved' => $resolvedStats[$date] ?? 0,
            ];
        }

        return $trends;
    }

    public function getResponseTimeMetrics(): array
    {
        return [
            'avg_first_response_hours' => $this->calculateAvgFirstResponseTime(),
            'avg_resolution_hours' => $this->calculateAvgResolutionTime(),
        ];
    }

    private function calculateAvgFirstResponseTime(): ?float
    {
        $avg = DB::table('support_ticket', 't')
            ->join('support_ticket_reply as r', 't.id', '=', 'r.ticket_id')
            ->whereRaw('r.id = (SELECT id FROM support_ticket_reply WHERE ticket_id = t.id AND user_id != t.user_id ORDER BY created_at ASC LIMIT 1)')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, t.created_at, r.created_at)) as avg_hours')
            ->first();

        return $avg && $avg->avg_hours !== null ? round((float)$avg->avg_hours, 2) : null;
    }

    private function calculateAvgResolutionTime(): ?float
    {
        $avg = Ticket::whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->first();

        return $avg && $avg->avg_hours !== null ? round((float)$avg->avg_hours, 2) : null;
    }
}
