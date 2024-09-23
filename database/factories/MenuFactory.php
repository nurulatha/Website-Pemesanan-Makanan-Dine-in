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
            $image = 'images/sushi.jpg';
        } elseif ($categoryId == 2) {
            $image = 'images/sashimi.jpg';
        } elseif ($categoryId == 3) {
            $image = 'images/ramen.jpg';
        } elseif ($categoryId == 4) {
            $image = 'images/donburi.jpg';
        } elseif ($categoryId == 5) {
            $image = 'images/yakitori.jpg';
        } elseif ($categoryId == 6) {
            $image = 'images/udon.jpg';
        } elseif ($categoryId == 7) {
            $image = 'images/drinks.jpg';
        } else {
            $image = 'images/noimage';
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
