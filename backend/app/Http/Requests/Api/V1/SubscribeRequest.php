<?php

namespace App\Http\Requests\Api\V1;

use App\Domain\Article\Enums\ArticleCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscribeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:subscribers,email'],
            'name' => ['required', 'string', 'max:255'],
            'preferences' => ['required', 'array', 'min:1'],
            'preferences.*' => [Rule::enum(ArticleCategory::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Adres e-mail jest wymagany.',
            'email.email' => 'Podaj poprawny adres e-mail.',
            'email.unique' => 'Ten adres e-mail jest już zapisany do newslettera.',

            'name.required' => 'Imię jest wymagane.',
            'name.string' => 'Imię musi być tekstem.',
            'name.max' => 'Imię nie może być dłuższe niż :max znaków.',

            'preferences.required' => 'Wybierz przynajmniej jedną kategorię.',
            'preferences.array' => 'Kategorie muszą być listą wartości.',
            'preferences.min' => 'Wybierz przynajmniej jedną kategorię.',

            'preferences.*.enum' => 'Kategoria ":input" jest nieprawidłowa.',
        ];
    }

    /** @return array<int, ArticleCategory> */
    public function categories(): array
    {
        /** @var array<int, string> $raw */
        $raw = $this->validated('preferences');

        return array_map(
            fn (string $category) => ArticleCategory::from($category),
            $raw,
        );
    }
}
