<?php

namespace Modules\Core\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\CustomerGroup;
use Tests\TestCase;

class StoreFrontBaseTestCase extends TestCase
{
    use DatabaseTransactions;

    public $model, $model_name, $route_prefix;
    protected array $headers;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function createHeader(): void
    {
        $this->headers = [
            "CONTENT-TYPE" => "application/json",
            "ACCEPT" => "application/json",
        ];
    }

    public function createCustomer(array $attributes = []): ?object
    {
        if ($this->authentication) {
            $password = $attributes["password"] ?? "password";
            $verification_token = Str::random(30);

            $data = [
                "password" => Hash::make($password),
                "customer_group_id" => CustomerGroup::first()->id,
                "verification_token" => $verification_token,
                "is_email_verified" => 1,
            ];

            $customer = Customer::factory()->create($data);
            $token = $this->createToken($customer->email, $password);
            $this->headers["Authorization"] = "Bearer {$token}";
        }

        return $customer ?? null;
    }

    public function getRoute(string $method, ?array $parameters = null): string
    {
        return route("{$this->route_prefix}{$method}", $parameters);
    }
}