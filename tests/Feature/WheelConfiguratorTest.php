<?php

namespace Tests\Feature;

use Closure;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class WheelConfiguratorTest extends TestCase
{
    public function test_home_renders_one_configurator_with_existing_images_and_a_lead_form(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertViewIs('home')
            ->assertSee('Полимерная покраска дисков')
            ->assertSee('id="leadForm"', false)
            ->assertSee('action="'.route('lead.send').'"', false)
            ->assertViewHas('sizes', fn (array $sizes) => array_column($sizes, 'label') === ['R15', 'R17', 'R19']);

        foreach ($response->viewData('sizes') as $size) {
            foreach ($response->viewData('finishes') as $finish) {
                $this->assertFileExists(public_path($size['folder'].'/'.$finish['file']));
            }
        }

        $html = $response->getContent();
        $this->assertSame(1, substr_count(strtolower($html), '<html'));
        $this->assertSame(1, substr_count(strtolower($html), '<body'));
        $this->assertStringNotContainsString('<<<<<<<', $html);
        $this->assertStringNotContainsString('>>>>>>>', $html);
    }

    public function test_lead_route_rejects_a_missing_phone_without_sending_email(): void
    {
        Mail::shouldReceive('send')->never();

        $this->postJson(route('lead.send'), ['name' => 'Клиент'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('phone');
    }

    public function test_lead_route_accepts_a_valid_request_and_passes_it_to_email(): void
    {
        Mail::shouldReceive('send')->once()->with(
            'emails.lead_request',
            Mockery::on(fn (array $data) => $data['lead']['phone'] === '+7 (913) 000-00-00'
                && $data['lead']['message'] === 'R17, матовый чёрный'),
            Mockery::type(Closure::class),
        );

        $this->postJson(route('lead.send'), [
            'name' => 'Тестовый клиент',
            'phone' => '+7 (913) 000-00-00',
            'message' => 'R17, матовый чёрный',
        ])->assertOk()->assertJson(['status' => 'ok']);
    }
}
