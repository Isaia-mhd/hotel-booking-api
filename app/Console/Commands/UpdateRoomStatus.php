<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

class UpdateRoomStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rooms:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the status of room after the end of reservation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        Room::all()->each(function ($room) use ($now) {
            $hasActiveBookings = Book::where('room_id', $room->id)
                ->where('start-date', '<=', $now)
                ->where('end-date', '>=', $now)
                ->exists();

            $room->isBooked = $hasActiveBookings ? true : false;
            $room->save();
        });

        Log::info('Commande artisan rooms:update-status exécutée à ' . now());

        $this->info('Rooms\'s status updated successfully');


    }
}
