<?php
require('../Admin/inc/db_config.php');

/**
 * Retrieves the features associated with a given room.
 *
 * @param int $room_id The ID of the room.
 * @param mysqli $con The database connection object.
 * @return array An array of feature names associated with the room.
 */
function get_room_features($room_id, $con) {
    $query = "SELECT f.name FROM `features` f 
              INNER JOIN `room_features` rf ON f.id = rf.feature_id 
              WHERE rf.room_id = ?";
    
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $room_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $features = [];
    
    while ($row = $result->fetch_assoc()) {
        $features[] = $row['name'];
    }
    
    return $features;
}

/**
 * Retrieves the facilities associated with a given room.
 *
 * @param int $room_id The ID of the room.
 * @param mysqli $con The database connection object.
 * @return array An array of facility names associated with the room.
 */
function get_room_facilities($room_id, $con) {
    $query = "SELECT fac.name FROM `facilities` fac 
              INNER JOIN `room_facilities` rf ON fac.id = rf.facility_id 
              WHERE rf.room_id = ?";
    
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $room_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $facilities = [];
    
    while ($row = $result->fetch_assoc()) {
        $facilities[] = $row['name'];
    }
    
    return $facilities;
}

/**
 * Retrieves the average user rating for a given room.
 *
 * @param int $room_id The ID of the room.
 * @param mysqli $con The database connection object.
 * @return float The average rating of the room, or 0 if no ratings exist.
 */
function get_user_rating($room_id, $con) {
    $query = "SELECT AVG(rating) as avg_rating FROM `user_ratings` WHERE room_id = ?";
    
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $room_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['avg_rating'] ? $row['avg_rating'] : 0;
}

/**
 * Generates room recommendations based on features, facilities, and user ratings.
 *
 * @param int $room_id The ID of the target room for recommendations.
 * @param mysqli $con The database connection object.
 * @return array An array of room IDs that are recommended based on similarity.
 */
function get_recommendations($room_id, $con) {
    // Extract features, facilities, and rating for the target room
    $target_features = get_room_features($room_id, $con);
    $target_facilities = get_room_facilities($room_id, $con);
    $target_rating = get_user_rating($room_id, $con);
    
    // Fetch all available rooms from the database
    $query = "SELECT * FROM `rooms` WHERE `status` = ? AND `remove` = ?";
    $stmt = $con->prepare($query);
    $status = 1; // Active rooms
    $remove = 0; // Not removed
    $stmt->bind_param('ii', $status, $remove);
    $stmt->execute();
    $room_res = $stmt->get_result();
    
    $recommendations = [];
    
    while ($room_data = $room_res->fetch_assoc()) {
        // Extract features, facilities, and rating for each room
        $features = get_room_features($room_data['id'], $con);
        $facilities = get_room_facilities($room_data['id'], $con);
        $rating = get_user_rating($room_data['id'], $con);
        
        // Calculate feature and facility matches
        $feature_match = count(array_intersect($target_features, $features));
        $facility_match = count(array_intersect($target_facilities, $facilities));
        
        // Calculate a similarity score
        $similarity_score = ($feature_match * 2) + $facility_match; // Features have more weight
        
        // Adjust similarity based on ratings
        if ($rating > $target_rating) {
            $similarity_score += 1; // Bonus for higher-rated rooms
        } elseif ($rating < $target_rating) {
            $similarity_score -= 1; // Penalty for lower-rated rooms
        }
        
        // Only consider rooms with a positive similarity score
        if ($similarity_score > 0) {
            $recommendations[$room_data['id']] = $similarity_score;
        }
    }

    // Sort recommendations by similarity score in descending order
    arsort($recommendations);
    return array_keys($recommendations); // Return room IDs sorted by similarity
}
?>
