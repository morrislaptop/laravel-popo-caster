<?php

namespace Morrislaptop\LaravelPopoCaster\Tests;

use Brick\Math\Exception\NumberFormatException;
use Brick\Money\Money;
use ErrorException;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Morrislaptop\LaravelPopoCaster\Normalizer;

class NormalizerThirdPartyTest extends TestCase
{
    /** @test */
    public function it_denormalizes_props()
    {
        $user = UserWithBrickMoney::factory()->create([
            'amount' => 1000,
            'currency' => 'AUD',
        ]);

        $user = $user->fresh();

        $this->assertInstanceOf(Money::class, $user->money);
        $this->assertEquals(1000, $user->money->getMinorAmount()->toInt());
        $this->assertEquals('AUD', $user->money->getCurrency()->getCurrencyCode());
    }

    /** @test */
    public function it_normalizes_an_object()
    {
        UserWithBrickMoney::factory()->create([
            'money' => Money::ofMinor(1000, 'AUD'),
        ]);

        $this->assertDatabaseHas('users', [
            'amount' => 1000,
            'currency' => 'AUD',
        ]);
    }

    /** @test */
    public function it_throws_exceptions_for_incorrect_data_structures()
    {
        $this->expectException(ErrorException::class);

        $user = UserWithBrickMoney::factory()->create([
            'amount' => 1000,
        ]);

        $user->money; // access prop to call Normalizer
    }

    /** @test */
    public function it_rejects_invalid_types()
    {
        $this->expectException(NumberFormatException::class);

        $user = UserWithBrickMoney::factory()->create([
            'amount' => 'string',
            'currency' => 'AUD',
        ]);

        $user->money; // access prop to call Normalizer
    }

    /** @test */
    public function it_handles_nullable_columns()
    {
        $user = UserWithBrickMoney::factory()->create([
            'amount' => null,
            'currency' => null,
        ]);

        $this->assertDatabaseHas('users', ['amount' => null, 'currency' => null]);

        $this->assertNull($user->refresh()->money);
    }
}

/**
 * @var Address $address
 */
class UserWithBrickMoney extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $casts = [
        'money' => Normalizer::class . ':' . Money::class,
    ];

    protected static function newFactory()
    {
        return UserWithBrickMoneyFactory::new();
    }
}

class UserWithBrickMoneyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserWithBrickMoney::class;

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
