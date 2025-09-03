<?php

namespace App\Helpers;

class LocationHelper
{
    /**
     * Calculate distance between two points using Haversine formula
     * Returns distance in meters
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Haversine formula for accurate distance calculation
        $earthRadius = 6371000; // Earth's radius in meters
        
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        $distance = $earthRadius * $c;
        
        return $distance; // Distance in meters
    }
    
    /**
     * Check if user is within office radius
     * Returns array with status and details
     */
    public static function checkOfficeRadius($userLat, $userLon, $officeLat, $officeLon, $officeRadius)
    {
        $distance = self::calculateDistance($userLat, $userLon, $officeLat, $officeLon);
        
        $distanceKm = round($distance / 1000, 2);
        $radiusKm = round($officeRadius / 1000, 2);
        
        return [
            'within_radius' => $distance <= $officeRadius,
            'distance' => $distance,
            'distance_km' => $distanceKm,
            'radius' => $officeRadius,
            'radius_km' => $radiusKm,
            'unit' => 'km',
            'user_coords' => [
                'latitude' => $userLat,
                'longitude' => $userLon
            ],
            'office_coords' => [
                'latitude' => $officeLat,
                'longitude' => $officeLon
            ]
        ];
    }
    
    /**
     * Convert meters to kilometers with 2 decimal places
     */
    public static function metersToKm($meters)
    {
        return round($meters / 1000, 2);
    }
    
    /**
     * Convert kilometers to meters
     */
    public static function kmToMeters($kilometers)
    {
        return $kilometers * 1000;
    }
    
    /**
     * Get human readable distance
     */
    public static function getReadableDistance($meters)
    {
        if ($meters < 1000) {
            return round($meters) . ' m';
        } else {
            return round($meters / 1000, 2) . ' km';
        }
    }
}
