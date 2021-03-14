<?php

namespace Morrislaptop\LaravelPopoCaster\Tests;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Morrislaptop\LaravelPopoCaster\Serializer;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;

class SerializerTest extends TestCase
{
    /** @test */
    public function it_casts_arrays_to_json()
    {
        UserWithAddress::factory()->create([
            'address' => [
                'street' => '1640 Riverside Drive',
                'suburb' => 'Hill Valley',
                'state' => 'California',
                'moved' => '2010-01-12T11:00:00+09:00',
            ],
            'addresses' => [
                [
                    'street' => '1641 Riverside Drive',
                    'suburb' => 'Hill Valley',
                    'state' => 'California',
                    'moved' => '2010-01-12T11:00:00+09:00',
                ],
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'address->street' => '1640 Riverside Drive',
            'address->suburb' => 'Hill Valley',
            'address->state' => 'California',
            'address->moved' => '2010-01-12T11:00:00+09:00',
        ]);
    }

    /** @test */
    public function it_casts_data_transfer_objects_to_json()
    {
        UserWithAddress::factory()->create([
            'address' => new Address(
                '1640 Riverside Drive',
                'Hill Valley',
                'California',
                Carbon::parse('2010-01-12T11:00:00+09:00'),
            ),
        ]);

        $this->assertDatabaseHas('users', [
            'address->street' => '1640 Riverside Drive',
            'address->suburb' => 'Hill Valley',
            'address->state' => 'California',
            'address->moved' => '2010-01-12T11:00:00+09:00',
        ]);
    }

    /** @test */
    public function it_json_to_a_data_transfer_object()
    {
        $user = UserWithAddress::factory()->create([
            'address' => [
                'street' => '1640 Riverside Drive',
                'suburb' => 'Hill Valley',
                'state' => 'California',
                'moved' => '2010-01-12T11:00:00+09:00',
            ],
            'addresses' => [
                [
                    'street' => '1641 Riverside Drive',
                    'suburb' => 'Hill Valley',
                    'state' => 'California',
                    'moved' => '2010-01-12T11:00:00+09:00',
                ],
            ],
        ]);

        $user = $user->fresh();

        $this->assertInstanceOf(Address::class, $user->address);
        $this->assertEquals('1640 Riverside Drive', $user->address->street);
        $this->assertEquals('Hill Valley', $user->address->suburb);
        $this->assertEquals('California', $user->address->state);
        $this->assertEquals('2010-01-12T11:00:00+09:00', $user->address->moved->toIso8601String());
        $this->assertEquals('1641 Riverside Drive', $user->addresses[0]->street);
    }

    /** @test */
    public function it_throws_exceptions_for_incorrect_data_structures()
    {
        $this->expectException(MissingConstructorArgumentsException::class);

        UserWithAddress::factory()->create([
            'address' => [
                'bad' => 'thing',
            ],
        ]);
    }

    /** @test */
    public function it_rejects_invalid_types()
    {
        $this->expectException(InvalidArgumentException::class);

        UserWithAddress::factory()->create([
            'address' => 'string',
        ]);
    }

    /** @test */
    public function it_handles_nullable_columns()
    {
        $user = UserWithAddress::factory()->create(['address' => null]);

        $this->assertDatabaseHas('users', ['address' => null]);

        $this->assertNull($user->refresh()->address);
    }
}

class Address
{
    public string $street;
    public string $suburb;
    public string $state;
    public Carbon $moved;

    public function __construct(string $street, string $suburb, string $state, Carbon $moved)
    {
        $this->street = $street;
        $this->suburb = $suburb;
        $this->state = $state;
        $this->moved = $moved;
    }
}

/**
 * @var Address $address
 */
class UserWithAddress extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $casts = [
        'address' => Serializer::class . ':' . Address::class,
        'addresses' => Serializer::class . ':' . Address::class . '[]',
    ];

    protected static function newFactory()
    {
        return UserWithAddressFactory::new();
    }
}

class UserWithAddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserWithAddress::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'address' => [
                'street' => $this->faker->streetAddress,
                'suburb' => $this->faker->city,
                'state' => $this->faker->state,
                'moved' => now(),
            ],
        ];
    }
}
