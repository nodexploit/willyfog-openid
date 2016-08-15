<?php


namespace App\Models;

class User extends BaseModel
{
    protected $messages = [];

    public function find($email)
    {
        $stm = $this->pdo->prepare(
            'SELECT id, digest AS hashed_password
              FROM user
              WHERE email = ?'
        );
        $stm->execute([$email]);

        return $stm->fetchAll()[0];
    }

    public function create($params)
    {
        if (!$this->isValid($params)) {
            return null;
        }

        $stm = $this->pdo->prepare(
            'INSERT INTO user (name, surname, nif, email, digest) VALUES (?, ?, ?, ?, ?)'
        );

        $success = $stm->execute(array_values($params));

        return $success ? $this->pdo->lastInsertId() : null;
    }

    public function registerInDegree($user_id, $degree_id)
    {
        $stm = $this->pdo->prepare(
            'INSERT INTO user_enrolled_degree
             (user_id, degree_id) VALUES (?, ?)'
        );

        $success = $stm->execute([$user_id, $degree_id]);

        return $success ? $this->pdo->lastInsertId() : null;
    }

    public function assignRole($user_id, $role_id)
    {
        $stm = $this->pdo->prepare(
            'INSERT INTO user_has_role
             (user_id, role_id) VALUES (?, ?)'
        );

        $success = $stm->execute([$user_id, $role_id]);

        return $success ? $this->pdo->lastInsertId() : null;
    }

    public function isValid($params)
    {
        foreach ($params as $index => $value) {
            if (empty($value)) {
                $this->messages []= "$index can not be empty";
            }
        }

        return count($this->messages) <= 0;
    }

    public function getMessages()
    {
        return $this->messages;
    }
}
