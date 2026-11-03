<?php

namespace App\Services;

use App\Models\LecturerModel;
use App\Models\StudentModel;
use App\Models\UserModel;
use CodeIgniter\Model;
use RuntimeException;
use Throwable;

class AccountService
{
    public function createStudent(array $data): int
    {
        return $this->createWithAccount(model(StudentModel::class), 'student', $data['student_number'], $data['name'], [
            'student_number'   => $data['student_number'],
            'study_program_id' => $data['study_program_id'],
            'advisor_id'       => ($data['advisor_id'] ?? '') ?: null,
            'entry_year'       => $data['entry_year'],
        ]);
    }

    public function updateStudent(array $student, array $data): void
    {
        $this->updateWithAccount(model(StudentModel::class), $student, $data['student_number'], $data['name'], [
            'student_number'   => $data['student_number'],
            'study_program_id' => $data['study_program_id'],
            'advisor_id'       => ($data['advisor_id'] ?? '') ?: null,
            'entry_year'       => $data['entry_year'],
        ]);
    }

    public function createLecturer(array $data): int
    {
        return $this->createWithAccount(model(LecturerModel::class), 'lecturer', $data['lecturer_number'], $data['name'], [
            'lecturer_number'  => $data['lecturer_number'],
            'study_program_id' => $data['study_program_id'],
            'email'            => ($data['email'] ?? '') ?: null,
            'phone'            => ($data['phone'] ?? '') ?: null,
        ]);
    }

    public function updateLecturer(array $lecturer, array $data): void
    {
        $this->updateWithAccount(model(LecturerModel::class), $lecturer, $data['lecturer_number'], $data['name'], [
            'lecturer_number'  => $data['lecturer_number'],
            'study_program_id' => $data['study_program_id'],
            'email'            => ($data['email'] ?? '') ?: null,
            'phone'            => ($data['phone'] ?? '') ?: null,
        ]);
    }

    public function resetPassword(int $userId): array
    {
        $users = model(UserModel::class);
        $user = $users->find($userId);

        if ($user === null) {
            throw new RuntimeException('Pengguna tidak ditemukan.');
        }

        $users->update($userId, ['password' => $user['username']]);

        return $user;
    }

    private function createWithAccount(Model $profiles, string $role, string $username, string $name, array $profile): int
    {
        $db = db_connect();
        $db->transBegin();

        try {
            $userId = model(UserModel::class)->insert([
                'name'     => $name,
                'username' => $username,
                'password' => $username,
                'role'     => $role,
            ]);

            $profile['user_id'] = $userId;
            $profileId = $profiles->insert($profile);

            if (! $profileId) {
                throw new RuntimeException('Data profil gagal disimpan.');
            }

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();

            throw $exception;
        }

        return (int) $profileId;
    }

    private function updateWithAccount(Model $profiles, array $current, string $username, string $name, array $profile): void
    {
        $db = db_connect();
        $db->transBegin();

        try {
            model(UserModel::class)->update($current['user_id'], [
                'name'     => $name,
                'username' => $username,
            ]);

            $profiles->update($current['id'], $profile);

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();

            throw $exception;
        }
    }
}
