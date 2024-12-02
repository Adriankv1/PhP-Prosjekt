<?php
class LoyaltyModel {
    private $db;
    private $pointsPerNOK = 0.1; // 10 points per 100 NOK

    public function __construct($db) {
        $this->db = $db;
    }

    public function calculatePoints($amount, $userId) {
        $multiplier = $this->getUserMultiplier($userId);
        return floor($amount * $this->pointsPerNOK * $multiplier);
    }

    public function addPoints($userId, $bookingId, $amount) {
        $points = $this->calculatePoints($amount, $userId);
        
        // Update user points
        $stmt = $this->db->prepare("
            UPDATE users 
            SET total_points = total_points + ?, 
                spendable_points = spendable_points + ?
            WHERE id = ?
        ");
        $stmt->bind_param("iii", $points, $points, $userId);
        $stmt->execute();

        // Record transaction
        $stmt = $this->db->prepare("
            INSERT INTO points_transactions 
            (user_id, booking_id, points_earned, description)
            VALUES (?, ?, ?, 'Points earned from booking')
        ");
        $stmt->bind_param("iii", $userId, $bookingId, $points);
        $stmt->execute();

        $this->updateLoyaltyLevel($userId);
    }

    public function spendPoints($userId, $points) {
        if (!$this->hasEnoughPoints($userId, $points)) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE users 
            SET spendable_points = spendable_points - ?
            WHERE id = ?
        ");
        $stmt->bind_param("ii", $points, $userId);
        return $stmt->execute();
    }

    private function getUserMultiplier($userId) {
        $stmt = $this->db->prepare("
            SELECT l.earning_multiplier
            FROM users u
            JOIN loyalty_levels l ON u.total_points BETWEEN l.min_points AND l.max_points
            WHERE u.id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['earning_multiplier'];
    }

    private function updateLoyaltyLevel($userId) {
        $stmt = $this->db->prepare("
            UPDATE users u
            JOIN loyalty_levels l ON u.total_points BETWEEN l.min_points AND l.max_points
            SET u.loyalty_level = l.id
            WHERE u.id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }

    private function hasEnoughPoints($userId, $points) {
        $stmt = $this->db->prepare("
            SELECT spendable_points 
            FROM users 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        return $user['spendable_points'] >= $points;
    }

    public function getLoyaltyInfo($userId) {
        $stmt = $this->db->prepare("
            SELECT u.total_points, u.spendable_points, l.*
            FROM users u
            JOIN loyalty_levels l ON u.total_points BETWEEN l.min_points AND l.max_points
            WHERE u.id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}