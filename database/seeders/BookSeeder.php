<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = fake();

        $rooms = Room::all();
        $users = User::all();

        for ($i = 0; $i < 20; $i++) {
            $startDate = $faker->dateTimeBetween('now', '+6 months');
            $endDate = (clone $startDate)->modify('+'.rand(1, 7).' days');

            $room = $rooms->random();

            // Check if this room is available for this range
            $isBooked = Book::where('room_id', $room->id)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start-date', [$startDate, $endDate])
                          ->orWhereBetween('end-date', [$startDate, $endDate])
                          ->orWhere(function ($q) use ($startDate, $endDate) {
                              $q->where('start-date', '<=', $startDate)
                                ->where('end-date', '>=', $endDate);
                          });
                })
                ->exists();

            if (!$isBooked) {
                Book::create([
                    'room_id' => $room->id,
                    'user_id' => $users->random()->id,
                    'start-date' => $startDate,
                    'end-date' => $endDate,
                    "number_of_people" => $faker->numberBetween(1, 10)
                ]);
            }
        }
    }
}
