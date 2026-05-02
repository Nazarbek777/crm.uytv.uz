<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Investor;
use App\Models\Property;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@crm.uy',
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
        ]);

        $investors = collect([
            ['name' => 'Rustam Olimov', 'phone' => '+998901234567', 'email' => 'rustam@crm.uy', 'notes' => 'Bosh investor.'],
            ['name' => 'Nilufar Ismoilova', 'phone' => '+998931234567', 'email' => 'nilufar@crm.uy', 'notes' => 'Ijara uchun ko‘p ishlaydi.'],
            ['name' => 'Azizbek Ergashev', 'phone' => '+998991234567', 'email' => 'azizbek@crm.uy', 'notes' => 'Sotuvchilar bilan aloqada.'],
        ])->map(fn ($data) => Investor::updateOrCreate(['email' => $data['email']], $data));

        $clients = collect([
            ['name' => 'Sardor Qodirov', 'phone' => '+998971234567', 'email' => 'sardor@crm.uy', 'notes' => 'Yangi uy izlaydi.'],
            ['name' => 'Madina Karimova', 'phone' => '+998933334445', 'email' => 'madina@crm.uy', 'notes' => 'Ijara uchun mijoz.'],
            ['name' => 'Umid Toshpulatov', 'phone' => '+998994445566', 'email' => 'umid@crm.uy', 'notes' => 'Tez-tez murojaat qiladi.'],
            ['name' => 'Gulnora Xolmirzaeva', 'phone' => '+998977889900', 'email' => 'gulnora@crm.uy', 'notes' => 'Sotib olishga qaror qildi.'],
        ])->map(fn ($data) => Client::updateOrCreate(['email' => $data['email']], $data));

        $properties = collect([
            ['investor_id' => $investors[0]->id, 'title' => 'Markaziy hududdagi 3 xonali', 'address' => 'Toshkent, Shayxontohur', 'price' => 250000000, 'status' => 'free', 'rooms' => 3, 'floor' => 2, 'total_floors' => 5, 'area' => 120, 'description' => 'Yangi remont qilingan uy.', 'image' => null],
            ['investor_id' => $investors[0]->id, 'title' => 'Yangi qurilgan kvartira', 'address' => 'Toshkent, Chilonzor', 'price' => 320000000, 'status' => 'rent', 'rooms' => 4, 'floor' => 6, 'total_floors' => 12, 'area' => 150, 'description' => 'Ijara uchun ideal variant.', 'image' => null],
            ['investor_id' => $investors[1]->id, 'title' => 'Tinch hududdagi 2 xonali', 'address' => 'Toshkent, Uchtepa', 'price' => 180000000, 'status' => 'sold', 'rooms' => 2, 'floor' => 4, 'total_floors' => 10, 'area' => 85, 'description' => 'Qulay transport aloqasi.', 'image' => null],
            ['investor_id' => $investors[1]->id, 'title' => 'Xorazmiy 3 xonali kvartira', 'address' => 'Toshkent, Mirzo Ulug‘bek', 'price' => 220000000, 'status' => 'free', 'rooms' => 3, 'floor' => 1, 'total_floors' => 9, 'area' => 110, 'description' => 'Yagona juda yaxshi variant.', 'image' => null],
            ['investor_id' => $investors[2]->id, 'title' => 'Kichik oilaviy uy', 'address' => 'Toshkent, Yakkasaroy', 'price' => 150000000, 'status' => 'sold', 'rooms' => 3, 'floor' => 2, 'total_floors' => 6, 'area' => 95, 'description' => 'Qulay sharoit, yaxshi narx.', 'image' => null],
            ['investor_id' => $investors[2]->id, 'title' => 'Tijorat uchun yaroqli bino', 'address' => 'Toshkent, Yunusobod', 'price' => 410000000, 'status' => 'free', 'rooms' => 5, 'floor' => 9, 'total_floors' => 15, 'area' => 220, 'description' => 'Tijorat faoliyati uchun ideal.', 'image' => null],
        ])->map(fn ($data) => Property::updateOrCreate(['title' => $data['title'], 'address' => $data['address']], $data));

        collect([
            ['property_id' => $properties[2]->id, 'client_id' => $clients[0]->id, 'price' => 180000000, 'type' => 'sale', 'sale_date' => now()->subDays(4), 'notes' => 'Tezkor to‘lov bilan sotildi.'],
            ['property_id' => $properties[4]->id, 'client_id' => $clients[1]->id, 'price' => 150000000, 'type' => 'sale', 'sale_date' => now()->subDays(12), 'notes' => 'Sotib olish shartlari rasman tasdiqlandi.'],
            ['property_id' => $properties[1]->id, 'client_id' => $clients[2]->id, 'price' => 12000000, 'type' => 'rent', 'sale_date' => now()->subDays(2), 'notes' => 'Uzoq muddatli ijaraga olindi.'],
        ])->each(fn ($data) => Sale::updateOrCreate([
            'property_id' => $data['property_id'],
            'client_id' => $data['client_id'],
            'type' => $data['type'],
            'sale_date' => $data['sale_date'],
        ], $data));
    }
}
