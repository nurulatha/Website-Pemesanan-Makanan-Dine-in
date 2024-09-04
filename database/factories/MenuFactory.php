<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Menu>
 */
class MenuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryId = mt_rand(1, 7);

        if ($categoryId == 1) {
            $image = '/storage/images/sushi.jpg';
        } elseif ($categoryId == 2) {
            $image = '/storage/images/sashimi.jpg';
        } elseif ($categoryId == 3) {
            $image = '/storage/images/ramen.jpg';
        } elseif ($categoryId == 4) {
            $image = '/storage/images/donburi.jpg';
        } elseif ($categoryId == 5) {
            $image = '/storage/images/yakitori.jpg';
        } elseif ($categoryId == 6) {
            $image = '/storage/images/udon.jpg';
        } elseif ($categoryId == 7) {
            $image = '/storage/images/drinks.jpg';
        } else {
            $image = '/storage/images/noimage';
        }

        return [
            'category_id' => $categoryId,
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'image' => $image,
            'price' => $this->faker->numberBetween(10, 40) * 1000
        ];
    }
}
