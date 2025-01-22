<?php

namespace App\Traits\Commands;

use Illuminate\Support\Facades\Validator;

trait InteractsWithConsole {
    /**
     * Prompt the user for valid input until a valid value is provided.
     *
     * @return mixed
     */
    public function askUntilValid(string $question, array $rules, bool $isSecret = false) {
        do {
            $value = $isSecret ? $this->secret($question) : $this->ask($question);

            $validator = Validator::make([$question => $value], [
                $question => $rules,
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $this->error($error);
                }
            }
        } while ($validator->fails());

        return $value;
    }
}
