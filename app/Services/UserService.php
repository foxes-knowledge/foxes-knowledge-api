<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function create(array $data): User
    {
        $color = dechex(rand(0x000000, 0xFFFFFF));

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            'color' => "#$color"
        ]);

        if (isset($data['picture'])) {
            $this->uploadPicture($user, $data['picture']);
        }

        return $user;
    }

    public function update(array $data, User $user): User
    {
        if (isset($data['picture'])) {
            $this->uploadPicture($user, $data['picture']);
            unset($data['picture']);
        }

        $user->update($data);

        return $user;
    }

    public function uploadPicture(User $user, \Illuminate\Http\UploadedFile $picture): void
    {
        if (!!$currentPicture = $user->picture) {
            $exploded = explode('/', $currentPicture);
            $relPath = 'avatars/' . $exploded[array_key_last($exploded)];
            if (Storage::exists($relPath)) {
                Storage::delete($relPath);
            }
        }

        $file = pathinfo($picture->getClientOriginalName());
        $filename = $user->id . str($file['filename'])->slug(); // @phpstan-ignore-line
        $picturePath = $picture->storeAs('avatars', "$filename." . $file['extension']); // @phpstan-ignore-line

        $user->update([
            'picture' => Storage::url((string)$picturePath)
        ]);
    }
}
