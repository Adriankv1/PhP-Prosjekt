<?php
class LoyaltyModel {
    private $db;
    private $pointsPerNOK = 0.1; // 10 points per 100 NOK

    // Constructor to initialize the database connection
    public function __construct($db) {
        $this->db = $db;
    }

    // Method to calculate points based on the amount spent and user multiplier
    public function calculatePoints($amount, $userId) {
        $multiplier = $this->getUserMultiplier($userId);
        return floor($amount * $this->pointsPerNOK * $multiplier);
    }

    // Method to add points to a user's account after a booking
    public function addPoints($userId, $bookingId, $amount) {
        $points = $this->calculatePoints($amount, $userId);
        
        // Update user points in the database
        $stmt = $this->db->prepare("
            UPDATE users 
            SET total_points = total_points + ?, 
                spendable_points = spendable_points + ?
            WHERE id = ?
        ");
        $stmt->bind_param("iii", $points, $points, $userId);
        $stmt->execute();

        // Record the points transaction
        $stmt = $this->db->prepare("
            INSERT INTO points_transactions 
            (user_id, booking_id, points_earned, description)
            VALUES (?, ?, ?, 'Points earned from booking')
        ");
        $stmt->bind_param("iii", $userId, $bookingId, $points);
        $stmt->execute();

        // Update the user's loyalty level
        $this->updateLoyaltyLevel($userId);
    }

    // Method to spend points from a user's account
    public function spendPoints($userId, $points) {
        // Check if the user has enough points to spend
        if (!$this->hasEnoughPoints($userId, $points)) {
            return false;
        }

        // Update the user's spendable points in the database
        $stmt = $this->db->prepare("
            UPDATE users 
            SET spendable_points = spendable_points - ?
            WHERE id = ?
        ");
        $stmt->bind_param("ii", $points, $userId);
        return $stmt->execute();
    }

    // Method to get the user's points earning multiplier based on their loyalty level
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

    // Method to update the user's loyalty level based on their total points
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

    // Method to check if the user has enough spendable points
    public function hasEnoughPoints($userId, $points) {
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

    // Method to get the user's loyalty information
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
?>