<?php

namespace Morrislaptop\LaravelPopoCaster\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Morrislaptop\LaravelPopoCaster\Normalizer;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use TypeError;

class NormalizerTest extends TestCase
{
    /** @test */
    public function it_denormalizes_props()
    {
        $user = UserWithMoney::factory()->create([
            'amount' => 1000,
            'currency' => 'AUD',
        ]);

        $user = $user->fresh();

        $this->assertInstanceOf(Money::class, $user->money);
        $this->assertEquals(1000, $user->money->amount);
        $this->assertEquals('AUD', $user->money->currency);
    }

    /** @test */
    public function it_normalizes_an_object()
    {
        UserWithMoney::factory()->create([
            'money' => new Money(1000, 'AUD'),
        ]);

        $this->assertDatabaseHas('users', [
            'amount' => 1000,
            'currency' => 'AUD',
        ]);
    }

    /** @test */
    public function it_throws_exceptions_for_incorrect_data_structures()
    {
        $this->expectException(MissingConstructorArgumentsException::class);

        $user = UserWithMoney::factory()->create([
            'amount' => 1000,
        ]);

        $user->money; // access prop to call Normalizer
    }

    /** @test */
    public function it_rejects_invalid_types()
    {
        $this->expectException(TypeError::class);

        $user = UserWithMoney::factory()->create([
            'amount' => 'string',
            'currency' => 'AUD',
        ]);

        $user->money; // access prop to call Normalizer
    }

    /** @test */
    public function it_handles_nullable_columns()
    {
        $user = UserWithMoney::factory()->create([
            'amount' => null,
            'currency' => null,
        ]);

        $this->assertDatabaseHas('users', ['amount' => null, 'currency' => null]);

        $this->assertNull($user->refresh()->money);
    }
}

class Money
{
    public int $amount;
    public string $currency;

    public function __construct(int $amount, string $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }
}

/**
 * @var Address $address
 */
class UserWithMoney extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $casts = [
        'money' => Normalizer::class . ':' . Money::class,
    ];

    protected static function newFactory()
    {
        return UserWithMoneyFactory::new();
    }
}

class UserWithMoneyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserWithMoney::class;

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
        ];
    }
}
