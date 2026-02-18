<?php

namespace Tests\Feature;

use App\Modules\Request\Models\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_request_with_valid_data(): void
    {
        $data = [
            'client_name' => 'Иванов Иван Иванович',
            'phone' => '+7 (999) 123-45-67',
            'address' => 'г. Москва, ул. Ленина, д. 10, кв. 5',
            'problem_text' => 'Не работает отопление в квартире.',
        ];

        $response = $this->post(route('requests.store'), $data);

        $response->assertRedirect(route('requests.create'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('requests', [
            'client_name' => $data['client_name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'problem_text' => $data['problem_text'],
            'status' => Request::STATUS_NEW,
        ]);
    }

    public function test_cannot_create_request_without_required_fields(): void
    {
        $response = $this->post(route('requests.store'), []);

        $response->assertSessionHasErrors(['client_name', 'phone', 'address', 'problem_text']);
    }
}
