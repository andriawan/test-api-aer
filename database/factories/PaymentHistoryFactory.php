<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentHistory>
 */
class PaymentHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ref_id' => $this->faker->unique()->uuid(),
            'trx_id' => $this->faker->unique()->uuid(),
            'qr_code' => '00020101021226760024ID.CO.SPEEDCASH.MERCHANT01189360081530002033470215ID10250020334760303UKE51440014ID.CO.QRIS.WWW0215ID10254269502140303UKE520448165303360540410005802ID5912IMAM GHOZALI6006JEMBER61056812162320108647336390509D261210430703A0163047CFA',
            'amount' => $this->faker->numberBetween(1000, 100000),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'expired_at' => $this->faker->dateTimeBetween('now', '+1 week'),
        ];
    }
}
