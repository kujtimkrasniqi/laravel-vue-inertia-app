<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data for a clean seed
        Client::truncate();

        $this->command->info('Seeding clients...');

        // ── 1. Active clients (expiry in the future) ──────────────────────────
        $active = [
            [
                'name'       => 'Sarah Johnson',
                'phone'      => '0501112233',
                'email'      => 'sarah.johnson@example.com',
                'start_date' => Carbon::today()->subMonths(6),
            ],
            [
                'name'       => 'Mohammed Al-Rashid',
                'phone'      => '0552223344',
                'email'      => 'mo.rashid@example.com',
                'start_date' => Carbon::today()->subMonths(3),
            ],
            [
                'name'       => 'Emily Carter',
                'phone'      => '0503334455',
                'email'      => null,
                'start_date' => Carbon::today()->subMonths(2),
            ],
            [
                'name'       => 'Carlos Rivera',
                'phone'      => '0554445566',
                'email'      => 'c.rivera@example.com',
                'start_date' => Carbon::today()->subMonth(),
            ],
            [
                'name'       => 'Aisha Nkemdirim',
                'phone'      => '0505556677',
                'email'      => 'aisha.nk@example.com',
                'start_date' => Carbon::today()->subWeeks(2),
            ],
        ];

        foreach ($active as $data) {
            // expiry_date auto-calculated by model boot: start_date + 1 month
            Client::create($data);
        }

        // ── 2. Expired clients (expiry in the past) ───────────────────────────
        $expired = [
            [
                'name'        => 'David Kim',
                'phone'       => '0506667788',
                'email'       => 'david.kim@example.com',
                'start_date'  => Carbon::today()->subMonths(4),
                'expiry_date' => Carbon::today()->subMonths(3),
            ],
            [
                'name'        => 'Fatima Al-Hassan',
                'phone'       => '0557778899',
                'email'       => null,
                'start_date'  => Carbon::today()->subMonths(5),
                'expiry_date' => Carbon::today()->subMonths(4),
            ],
            [
                'name'        => 'James Okafor',
                'phone'       => '0508889900',
                'email'       => 'j.okafor@example.com',
                'start_date'  => Carbon::today()->subMonths(3),
                'expiry_date' => Carbon::today()->subWeeks(3),
            ],
            [
                'name'        => 'Lena Müller',
                'phone'       => '0559990011',
                'email'       => 'lena.muller@example.com',
                'start_date'  => Carbon::today()->subMonths(2),
                'expiry_date' => Carbon::today()->subWeeks(2),
            ],
        ];

        foreach ($expired as $data) {
            Client::create($data);
        }

        // ── 3. Expiring this week ─────────────────────────────────────────────
        $expiringThisWeek = [
            [
                'name'        => 'Omar Siddiqui',
                'phone'       => '0501234567',
                'email'       => 'omar.s@example.com',
                'start_date'  => Carbon::today()->subMonth()->addDays(2),
                'expiry_date' => Carbon::today()->addDays(2),
            ],
            [
                'name'        => 'Priya Nair',
                'phone'       => '0552345678',
                'email'       => null,
                'start_date'  => Carbon::today()->subMonth()->addDays(5),
                'expiry_date' => Carbon::today()->addDays(5),
            ],
        ];

        foreach ($expiringThisWeek as $data) {
            Client::create($data);
        }

        // ── 4. Expiring this month (but not this week) ────────────────────────
        $expiringThisMonth = [
            [
                'name'        => 'Nina Petrov',
                'phone'       => '0503456789',
                'email'       => 'nina.p@example.com',
                'start_date'  => Carbon::today()->subMonth()->addDays(10),
                'expiry_date' => Carbon::today()->addDays(10),
            ],
            [
                'name'        => 'Yusuf Abdi',
                'phone'       => '0554567890',
                'email'       => 'yusuf.a@example.com',
                'start_date'  => Carbon::today()->subMonth()->addDays(18),
                'expiry_date' => Carbon::today()->addDays(18),
            ],
            [
                'name'        => 'Laura Bianchi',
                'phone'       => '0505678901',
                'email'       => null,
                'start_date'  => Carbon::today()->subMonth()->addDays(25),
                'expiry_date' => Carbon::today()->addDays(25),
            ],
        ];

        foreach ($expiringThisMonth as $data) {
            Client::create($data);
        }

        // ── Summary ───────────────────────────────────────────────────────────
        $this->command->table(
            ['Group', 'Count'],
            [
                ['Active (long-term)',       count($active)],
                ['Expired',                  count($expired)],
                ['Expiring this week',        count($expiringThisWeek)],
                ['Expiring this month',       count($expiringThisMonth)],
                ['Total', count($active) + count($expired) + count($expiringThisWeek) + count($expiringThisMonth)],
            ]
        );
    }
}
