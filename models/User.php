<?php
/**
 * Digiws - User Model
 */

class User {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array {
        return $this->db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public function findByEmail(string $email): ?array {
        return $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }

    public function create(array $data): int {
        return $this->db->insert('users', [
            'name'           => $data['name'],
            'email'          => $data['email'],
            'password'       => password_hash($data['password'], PASSWORD_BCRYPT),
            'phone'          => $data['phone'] ?? null,
            'avatar'         => $data['avatar'] ?? null,
            'role'           => $data['role'] ?? 'user',
            'status'         => 'active',
            'google_id'      => $data['google_id'] ?? null,
            'facebook_id'    => $data['facebook_id'] ?? null,
            'email_verified' => $data['email_verified'] ?? 0,
            'user_token'     => bin2hex(random_bytes(16))
        ]);
    }

    public function update(int $id, array $data): int {
        return $this->db->update('users', $data, 'id = ?', [$id]);
    }

    public function updatePassword(int $id, string $password): int {
        return $this->db->update('users', [
            'password' => password_hash($password, PASSWORD_BCRYPT)
        ], 'id = ?', [$id]);
    }

    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    public function authenticate(string $email, string $password): ?array {
        $user = $this->findByEmail($email);
        if (!$user) return null;
        if (!password_verify($password, $user['password'])) return null;
        return $user;
    }

    public function setSession(array $user): void {
        // Prevent session fixation whenever an account logs in.
        session_regenerate_id(true);
        $_SESSION['user_id']     = $user['id'];
        $_SESSION['user_name']   = $user['name'];
        $_SESSION['user_email']  = $user['email'];
        $_SESSION['user_role']   = $user['role'];
        $_SESSION['user_avatar'] = $user['avatar'] ?? '';
        $_SESSION['user_phone']  = $user['phone'] ?? '';
    }

    public function destroySession(): void {
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_role'], $_SESSION['user_avatar'], $_SESSION['user_phone']);
        session_regenerate_id(true);
    }

    public function createResetToken(int $id): string {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->db->update('users', [
            'reset_token'   => $token,
            'reset_expires' => $expires
        ], 'id = ?', [$id]);
        return $token;
    }

    public function findByResetToken(string $token): ?array {
        return $this->db->fetch(
            "SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()",
            [$token]
        );
    }

    public function clearResetToken(int $id): void {
        $this->db->update('users', [
            'reset_token'   => null,
            'reset_expires' => null
        ], 'id = ?', [$id]);
    }

    public function getAll(int $page = 1, int $perPage = 20, string $role = ''): array {
        $offset = ($page - 1) * $perPage;
        $where = '1=1';
        $params = [];

        if ($role) {
            $where .= ' AND role = ?';
            $params[] = $role;
        }

        $total = $this->db->count('users', $where, $params);
        $params[] = $perPage;
        $params[] = $offset;
        $users = $this->db->fetchAll(
            "SELECT * FROM users WHERE {$where} ORDER BY created_at DESC LIMIT ? OFFSET ?",
            $params
        );

        return ['users' => $users, 'total' => $total];
    }

    public function getCustomers(int $page = 1, int $perPage = 20): array {
        return $this->getAll($page, $perPage, 'user');
    }

    public function getTotalCustomers(): int {
        return $this->db->count('users', "role = 'user'");
    }

    public function getRecentCustomers(int $limit = 5): array {
        return $this->db->fetchAll(
            "SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    }

    public function emailExists(string $email, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
        $params = [$email];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        return $this->db->fetchColumn($sql, $params) > 0;
    }
    public function findByGoogleId(string $id): ?array {
        return $this->db->fetch("SELECT * FROM users WHERE google_id = ?", [$id]);
    }

    public function delete(int $id): bool {
        return $this->db->delete('users', 'id = ?', [$id]);
    }
}
