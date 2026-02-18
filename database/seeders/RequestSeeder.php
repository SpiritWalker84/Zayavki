<?php

namespace Database\Seeders;

use App\Modules\Request\Models\Request;
use App\Modules\User\Models\User;
use Illuminate\Database\Seeder;

class RequestSeeder extends Seeder
{
    public function run(): void
    {
        $masters = User::where('role', User::ROLE_MASTER)->get();
        
        if ($masters->isEmpty()) {
            $this->command->warn('Мастера не найдены. Сначала выполните UserSeeder.');
            return;
        }

        $master1 = $masters->first();
        $master2 = $masters->skip(1)->first() ?? $master1;

        // Новая заявка
        Request::create([
            'client_name' => 'Иванов Иван Иванович',
            'phone' => '+7 (999) 123-45-67',
            'address' => 'г. Москва, ул. Ленина, д. 10, кв. 5',
            'problem_text' => 'Не работает отопление в квартире. Батареи холодные.',
            'status' => Request::STATUS_NEW,
        ]);

        // Назначенная заявка
        Request::create([
            'client_name' => 'Петрова Мария Сергеевна',
            'phone' => '+7 (999) 234-56-78',
            'address' => 'г. Москва, ул. Пушкина, д. 20, кв. 12',
            'problem_text' => 'Протекает кран на кухне. Нужна замена смесителя.',
            'status' => Request::STATUS_ASSIGNED,
            'assigned_to' => $master1->id,
        ]);

        // Заявка в работе
        Request::create([
            'client_name' => 'Сидоров Петр Александрович',
            'phone' => '+7 (999) 345-67-89',
            'address' => 'г. Москва, пр. Мира, д. 30, кв. 8',
            'problem_text' => 'Не работает розетка в спальне. Возможно, проблема с проводкой.',
            'status' => Request::STATUS_IN_PROGRESS,
            'assigned_to' => $master1->id,
        ]);

        // Завершенная заявка
        Request::create([
            'client_name' => 'Козлова Анна Викторовна',
            'phone' => '+7 (999) 456-78-90',
            'address' => 'г. Москва, ул. Гагарина, д. 15, кв. 3',
            'problem_text' => 'Заменить лампочку в люстре. Высокий потолок, нужна стремянка.',
            'status' => Request::STATUS_DONE,
            'assigned_to' => $master2->id,
        ]);

        // Отмененная заявка
        Request::create([
            'client_name' => 'Морозов Дмитрий Петрович',
            'phone' => '+7 (999) 567-89-01',
            'address' => 'г. Москва, ул. Садовая, д. 5, кв. 10',
            'problem_text' => 'Проблема с канализацией. Клиент решил проблему самостоятельно.',
            'status' => Request::STATUS_CANCELED,
        ]);

        // Еще одна новая заявка
        Request::create([
            'client_name' => 'Волкова Елена Николаевна',
            'phone' => '+7 (999) 678-90-12',
            'address' => 'г. Москва, ул. Цветочная, д. 7, кв. 15',
            'problem_text' => 'Не закрывается входная дверь. Проблема с замком.',
            'status' => Request::STATUS_NEW,
        ]);

        // Еще одна назначенная заявка
        Request::create([
            'client_name' => 'Новиков Сергей Владимирович',
            'phone' => '+7 (999) 789-01-23',
            'address' => 'г. Москва, ул. Лесная, д. 12, кв. 7',
            'problem_text' => 'Требуется покраска стен в коридоре. Площадь около 15 кв.м.',
            'status' => Request::STATUS_ASSIGNED,
            'assigned_to' => $master2->id,
        ]);

        $this->command->info('Создано 7 тестовых заявок с разными статусами.');
    }
}
